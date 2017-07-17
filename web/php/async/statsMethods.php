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

/**
 *  Updates the aggregate array for a specified
 *  channel with a output line.
 */
function processChannel($line, $channel, &$rate, &$sums) {
	$values = explode(',', $line);
	
	$i = 0;
	for ($p = 1; $p < 16; $p++, $i++) {
		$rate[$channel * 15 + $i] += floatval($values[$p]);
		$sums[$channel * 15 + $i] += floatval($values[$p]);
	}
	
	unset($values);
}

/**
 *  Safely sets all fields of arrays to zero
 */
function initArrays(&$rate, &$sums, &$total, $chnlSize) {
	$chnlSize *= 15;
	for ($i = 0; $i < $chnlSize; $i++) {
		$rate[$i] = 0;
		$sums[$i] = 0;
	}
	for ($i = 0; $i < 30; $i++) $total[$i] = 0;
}

/**
 *  
 */
function computeRates(&$rate, &$total, $chnlSize, $intervals) {
	for ($i = 0; $i < $chnlSize; $i++) {
		for ($p = 0; $p < 15; $p++) {
			$total [$p] += $rate[$i * 15 + $p];
			$rate[$i * 15 + $p] /= $intervals;
		}
	}
	
	for ($i = 0; $i < 15; $i++) $total[$i] /= $intervals;
}

/**
 *  
 */
function computeSums(&$sums, &$total, $chnlSize, $interval) {
	for ($i = 0; $i < $chnlSize; $i++) {
		for ($p = 0; $p < 15; $p++) {
			$total [15 + $p] += $sums[$i * 15 + $p];
			$sums[$i * 15 + $p] *= $interval;
		}
	}
	
	for ($i = 15; $i < 30; $i++) $total[$i] *= $interval;
}
?>