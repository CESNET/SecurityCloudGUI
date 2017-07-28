<?php
require_once 'statsInit.php';
require_once 'statsMethods.php';

$rate = new SplFixedArray(15 * $chnlSize);
$sums = new SplFixedArray(15 * $chnlSize);
$total= new SplFixedArray(15 * 2);
$intervals = 0;
$lastint = 0;
$currint = 0;
$interval = 0;

initArrays($rate, $sums, $total, $chnlSize);

function printResourceError() {
	echo '<div class=\'panel panel-danger\'>';
	echo '<div class=\'panel-heading\'>Error</div>';
	echo '<div class=\'panel-body\'>proc_exec() failed. Enable PHP error/warning logging to see what\'s the matter.</div>';
	echo '</div>';
	exit(4);
}

for ($ch = 0; $ch < $chnlSize; $ch++) {
	$cmd = "$RRDTOOL_CMD fetch $chnl[$ch].rrd AVERAGE -r 300 $time -a | tail -n +3 | tr ' ' ','";

	if ($SINGLE_MACHINE) {
		$p = proc_open($cmd, $desc, $pipes, $IPFIXCOL_DATA."$profile/rrd/channels");// Execute the program command
		if(!$p) printResourceError();

		if(is_resource($p)) {
			while($f = fgets($pipes[1])) {											// While any stdout is inbound, load line
				processChannel($f, $ch, $rate, $sums);
				$intervals++;
			}
		}
	}
	else {
		foreach ($SLAVE_HOSTNAMES as $sh) {
			$intervals = 0;
			$p = proc_open($cmd, $desc, $pipes, $IPFIXCOL_DATA.$sh."$profile/rrd/channels");
			if (!$p) printResourceError();

			if (is_resource($p)) {
				while ($f = fgets($pipes[1])) {
					processChannel($f, $ch, $rate, $sums);
					$intervals++;
				}
			}
		}

		$intervals /= sizeof($SLAVE_HOSTNAMES);	// normalization
	}
}

if (sizeof($timeSplit) != 2) {
	$interval = 300;
	$intervals = 1;
}
else {
	$intervals /= $chnlSize;

	if ($intervals != 0) {
		$interval = (intval($timeSplit[1]) - intval($timeSplit[0])) / $intervals;
	}
	else {
		$interval = 300;
	}
}

if ($intervals != 0) computeRates($rate, $total, $chnlSize, $intervals);
computeSums ($sums, $total, $chnlSize, $interval);

/* COMPUTE WHICH ROWS OF EACH SET (Flows, Packets, Traffic) has the most significant sum/rate of values (All protos) */
$maxima = new SplFixedArray(6);
for ($i = 0; $i < 3; $i++) {
	for ($p = 0; $p < $chnlSize; $p++) {
		if ($rate[$p * 15 + $i * 5] > $rate[$maxima[$i] * 15 + $i * 5])		$maxima[$i] = $p;
		if ($sums[$p * 15 + $i * 5] > $sums[$maxima[3 + $i] * 15 + $i * 5])	$maxima[3 + $i] = $p;
	}
}

for ($t = 0; $t < 2; $t++) {
	for ($i = 0; $i < 3; $i++) {
		echo '<div class=\'col-md-4\'>';
		echo '<div class=\'panel panel-info\'>';
		echo '<div class=\'panel-heading\'>',$names[$i],'</div>';
		echo '<div class=\'panel-body\'>';
		echo '<table class=\'table table-striped table-condensed table-hover\'>';
		echo '<caption>',$types[$t],'</caption>';
		echo '<thead><tr><th>Channel</th><th>All</th><th>TCP</th><th>UDP</th><th>ICMP</th><th>Other</th></thead>';
		echo '<tbody>';

		for ($p = 0; $p < $chnlSize; $p++) {
			$label = ($t == 0) ? '/s' : '';
			printRow($chnl[$p], $t == 0 ? $rate : $sums, ($p * 15 + $i * 5), ($p * 15 + ($i + 1) * 5), ($i == 2 ? "B$label" : "$label"), $p == $maxima[$t * 3 + $i]);
		}

		printRow("Total", $total, ($t * 15 + $i * 5), ($t * 15 + ($i + 1) * 5), ($i == 2 ? "B$label" : "$label"), false);

		echo '</tbody></table></div></div></div>';
	}
	echo '</div>';
}
?>
