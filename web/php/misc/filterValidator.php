<?php
include '../config.php';

/**
 *  @details Runs ipfixcol-filter-check -x with a given filter parameter. Polls stdout for error
 *  messages (thats how ifc works) and returns null on success or error message on fail.
 */
function validateFilter($filter) {
	$cmd = $IPFIXCOL_FILTER_CHECK.' -x '.escapeshellarg($filter);
	
	$desc = array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w'));
	$p = proc_open($cmd, $desc, $pipes);
	
	$buf = "";
	if (is_resource($p)) {
		while ($f = fgets($pipes[1])) {
			$buf .= $f;
		}
	}
	
	$result = proc_get_status($p)['exitcode'];
	
	if ($result == 0) {
		return null;
	}
	
	return $buf;
}
?>