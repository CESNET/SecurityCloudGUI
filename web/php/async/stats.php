<?php
/**
 *  @brief Converts a unformatted number in a format with metric suffic
 *  
 *  @param [in] $rawNumber Anything, that can be considered a number (int, float, even number in a string)
 *  @return String like: 134.1 M
 *  
 *  @details This function uses PHP native number_format(...) which does round number even though it is not documented
 */
function volume_conv_metric($rawNumber) {
	$suffix = array("", "k", "M", "G", "T", "P", "E", "Z", "Y" );			// none, kilo, Mega, Giga, Tera, Peta, Exa, Zetta, Yotta
	$suffixSize = (int)sizeof($suffix);
	
	$conv = floatval($rawNumber);
	$ptr = 0;
	
	while ($conv > 1000.0 && $ptr < $suffixSize) {
		$conv /= 1000.0;
		$ptr++;
	}
	
	$result = strval(number_format($conv, 1, '.', ' ')).' '.$suffix[$ptr];
	
	return $result;
}

/**
 * prints the row of statistics
 * first parameters is name of the row, will be printed in leftmost cell
 * second parameter is array of statistics value, bgn and end are start/stop iterators in this array. they should have difference of five
 * suffix is /s or B/s depending on situation
 * highlight is true/false based on whether the row has the most significant data from all selected channels
 */
function printRow($rowName, $values, $bgn, $end, $suffix, $highlight) {
	echo '<tr';
	if ($highlight) {
		echo ' class=\'info\'';
	}
	echo '>';
	echo '<th>', $rowName, '</th>';
	
	for ($iter = $bgn; $iter < $end; $iter++) {
		echo '<td>', volume_conv_metric($values[$iter]), $suffix, '</td>';
	}
	
	echo '</tr>';
}

// *** include ***
include '../config.php';													// Grab some constants from the config
include '../misc/profileClass.php';
include '../misc/profileMethods.php';

// *** get url params ***
$time	= $_GET['time']; // < this parameter needs to be changed
$src	= $_GET['src'];
$timeSplit = explode(':', $time);

/* CREATE A MASTER TREE FROM XML */
$TREE_PROFILE = new Profile();												// Full tree of profiles
createProfileTreeFromXml(loadXmlFile($IPFIXCOL_CFG), '/', $TREE_PROFILE);	// Fill it with ALL necessary data

/* COLLECT USER-AVAILABLE PROFILES, COLLECT USER-SELECTED PROFILE AND VERIFY IT */
$ARR_AVAILS = getAvailableProfiles('me');
$profile = getCurrentProfile();
if (!verifySelectedProfile($profile, $ARR_AVAILS)) {
	echo "You don't have the privileges to access the profile $profile<br>";
	exit(1);
}

unset($ARR_AVAILS);

/* SEARCH FOR SELECTED SUBPROFILE ROOT */
$aux = null;
searchForProfile($TREE_PROFILE, $profile, $aux);
if ($aux == null) {
	echo "The profile $aux does not exist<br>";
	exit(2);
}

if (sizeof($timeSplit) == 2) {
	$time = "-s $timeSplit[0] -e $timeSplit[1]";
}
else {
	$time = "-s $timeSplit[0] -e $timeSplit[0]";
}
$time = escapeshellcmd($time);

// *** PROC OPEN DESCRIPTORS ***
$desc = array(
	0 => array ('pipe', 'r'),
	1 => array ('pipe', 'w'),
	2 => array ('pipe', 'w')
);
$pipes = array();

// *** DATA ARRAYS ***
$statsRate = array();
$statsSums = array();
$totalRate = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
$totalSums = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
$names = array("Flows", "Packets", "Traffic");
$srcs = explode(':', $src);
$srcsSize = (int)sizeof($srcs);
$segm;	// Backup constant for computing $totalRate properly

for ($s = 0; $s < $srcsSize; $s++) {
	$cmd = "exec $RRDTOOL fetch $srcs[$s].rrd AVERAGE -r 300 $time -a | tail -n +3 | tr ' ' ',' | tr '\n' ';'";
	
	$p = proc_open($cmd, $desc, $pipes, $IPFIXCOL_DATA."$profile/rrd/channels");// Execute the program command
	if($p == false) {														// If execution failed (
		echo '<div class=\'panel panel-danger\'>';							// Print this *very* serious error
		echo '<div class=\'panel-heading\'>Error</div>';
		echo '<div class=\'panel-body\'>proc_exec() failed. Enable PHP error/warning logging to see what\'s the matter.</div>';
		echo '</div>';
		exit(4);															// And end this thread )
	}

	$buffer = "";															// Stdout buffer
	if(is_resource($p)) {												
		while($f = fgets($pipes[1])) {										// While any stdout is inbound
			$buffer .= $f;													// Buffer it
		}
	}

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
			$statsRate[$s][$i] /= $segmentsSize;
		}
	}
	
	$segm = $segmentsSize;	// Backup it for $totalRate
}

for($i = 0; $i < 15; $i++) {
	$totalRate[$i] /= $segm;	// Compute $totalRate properly
}

echo '<div class=\'panel panel-primary\'>';
echo '<div class=\'panel-heading\' id=\'StatsContentHeader\'></div></div>'; // content will be added by JS

/* COMPUTE WHICH ROWS OF EACH SET (Flows, Packets, Traffic) has the most significant sum of values (All protos) */
$maxima = array(0, 0, 0);
for ($i = 0; $i < 3; $i++) {
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
	echo '<caption>Sum</caption>';
	echo '<thead><tr><th>Channel</th><th>All</th><th>TCP</th><th>UDP</th><th>ICMP</th><th>Other</th></thead>';
	echo '<tbody>';
	
	for ($p = 0; $p < $srcsSize; $p++) {
		printRow($srcs[$p], $statsRate[$p], $i * 5, ($i + 1) * 5, $i == 2 ? 'B/s' : '/s', $p == $maxima[$i]);
	}
	printRow("Total", $totalRate, $i * 5, ($i + 1) * 5, $i == 2 ? 'B/s' : '/s', false);
	
	echo '</tbody></table></div></div></div>';
}
echo '</div>';

/* PRINT THE TABLES FOR FLOWS, PACKETS, TRAFFIC */
echo '<div class=\'row\'>';
for($i = 0; $i < 3; $i++) {
	echo '<div class=\'col-md-4\'>';
	echo '<div class=\'panel panel-primary\'>';
	echo '<div class=\'panel-heading\'>',$names[$i],'</div>';
	echo '<div class=\'panel-body\'>';
	echo '<table class=\'table table-striped table-condensed table-hover\'>';
	echo '<caption>Sum</caption>';
	echo '<thead><tr><th>Channel</th><th>All</th><th>TCP</th><th>UDP</th><th>ICMP</th><th>Other</th></thead>';
	echo '<tbody>';
	
	for ($p = 0; $p < $srcsSize; $p++) {
		printRow($srcs[$p], $statsSums[$p], $i * 5, ($i + 1) * 5, $i == 2 ? 'B' : ' ', $p == $maxima[$i]);
	}
	printRow("Total", $totalSums, $i * 5, ($i + 1) * 5, $i == 2 ? 'B' : ' ', false);
	
	echo '</tbody></table></div></div></div>';
}
echo '</div>';
?>
