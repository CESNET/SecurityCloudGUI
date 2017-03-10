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

for ($ch = 0; $ch < $chnlSize; $ch++) {
	$cmd = "exec $RRDTOOL fetch $chnl[$ch].rrd AVERAGE -r 300 $time -a | tail -n +3 | tr ' ' ','"; /*  | tr '\n' ';' */
	
	$p = proc_open($cmd, $desc, $pipes, $IPFIXCOL_DATA."$profile/rrd/channels");// Execute the program command
	if($p == false) {															// If execution failed (
		echo '<div class=\'panel panel-danger\'>';								// Print this *very* serious error
		echo '<div class=\'panel-heading\'>Error</div>';
		echo '<div class=\'panel-body\'>proc_exec() failed. Enable PHP error/warning logging to see what\'s the matter.</div>';
		echo '</div>';
		exit(4);																// And end this thread )
	}

	if(is_resource($p)) {
		while($f = fgets($pipes[1])) {											// While any stdout is inbound, load line
			processChannel($f, $ch, $rate, $sums);
			$intervals++;
		}
	}
<<<<<<< HEAD

	$segments = explode(';', $buffer);
	$segmentsSize = (int)sizeof($segments) - 1;

	$statsRate[$s] = array();
	$statsSums[$s] = array();
	for ($i = 0; $i < 15; $i++) {
		$statsRate[$s][] = 0;
		$statsSums[$s][] = 0;
	}
	for ($i = 0; $i < $segmentsSize; $i++) {
		$vars = explode(',', $segments[$i]);
		$varsSize = (int)sizeof($vars);
		
		if ($varsSize != 20) {
			echo "Unknown error occured.\n";
			echo "$segments[$i]";
			exit(666);
		}
		
		for ($v = 1; $v <= 15; $v++) {
			$aux = floatval($vars[$v]);
			$statsRate[$s][$v-1] += $aux;
			$statsSums[$s][$v-1] += $aux * 300;
			$totalRate[$v-1] += $aux;
			$totalSums[$v-1] += $aux * 300;
		}
		
		for ($i = 0;  $i < 15; $i++) {
			if ($segmentsSize != 0)	$statsRate[$s][$i] /= $segmentsSize;
			else					$statsRate[$s][$i] = 0;
		}
	}
	
	$segm = $segmentsSize;	// Backup it for $totalRate
}

for($i = 0; $i < 15; $i++) {
	if ($segm != 0)	$totalRate[$i] /= $segm;	// Compute $totalRate properly
	else			$totalRate[$i] = 0;
=======
}

if (sizeof($timeSplit) != 2) {
	$interval = 300;
	$intervals = 1;
}
else {
	$intervals /= 2;
	$interval = (intval($timeSplit[1]) - intval($timeSplit[0])) / $intervals;
>>>>>>> refs/remotes/origin/no_sidebar
}

if ($intervals != 0) computeRates($rate, $total, $chnlSize, $intervals);
computeSums ($sums, $total, $chnlSize, $interval);

/* COMPUTE WHICH ROWS OF EACH SET (Flows, Packets, Traffic) has the most significant sum of values (All protos) */
$maxima = new SplFixedArray(6);
for ($i = 0; $i < 3; $i++) {
<<<<<<< HEAD
	for ($p = 0; $p < $srcsSize; $p++) {
		if ($statsRate[$p][$i * 5] > $statsRate[$maxima[$i]][$i * 5]) $maxima[$i] = $p;
	}
}

/* PRINT THE TABLES FOR FLOWS, PACKETS, TRAFFIC */
echo '<div class=\'row\'>';
for($i = 0; $i < 3; $i++) {
	echo '<div class=\'col-md-4\'>';
	echo '<div class=\'panel panel-primary\'>';
	echo '<div class=\'panel-heading\'>',$names[$i],'</div>';
	echo '<div class=\'panel-body\'>';
	echo '<table class=\'table table-striped table-condensed table-hover\'>';
	echo '<caption>Rate</caption>';
	echo '<thead><tr><th>Channel</th><th>All</th><th>TCP</th><th>UDP</th><th>ICMP</th><th>Other</th></thead>';
	echo '<tbody>';
	
	for ($p = 0; $p < $srcsSize; $p++) {
		printRow($srcs[$p], $statsRate[$p], $i * 5, ($i + 1) * 5, $i == 2 ? 'B/s' : '/s', $p == $maxima[$i]);
=======
	for ($p = 0; $p < $chnlSize; $p++) {
		if ($rate[$p * 15 + $i * 5] > $rate[$maxima[$i] * 15 + $i * 5])		$maxima[$i] = $p;
		if ($sums[$p * 15 + $i * 5] > $sums[$maxima[3 + $i] * 15 + $i * 5])	$maxima[3 + $i] = $p;
>>>>>>> refs/remotes/origin/no_sidebar
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
