<?php
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: ".gmdate('D, d M Y H:i:s')." GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
?>
<?php
/* MAIN EXECUTION CODE */
function execDbRequest() {														// This gets called when this thread is started
	date_default_timezone_set('UTC');
	// *** include ***
	include '../config.php';													// Grab some constants from the config
	include '../misc/transactionsInclude.php';									// And get some functions related to transactions
	include '../misc/profileClass.php';
	include '../misc/profileMethods.php';

	// *** get url params ***
	$opts			= $_GET['opts'];											// Grab the url params (
	$filter 		= $_GET['filter'];											// 	...
	$stamp			= $_GET['stamp'];											//	...
	$tab			= $_GET['tab'];												//	...
	$channelsArr	= explode(':', $_GET['src']);								// TODO: Parse chnl1:chnl2:..:chnlN fmt

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

	if(strlen($filter) > 2) {
		$filter = escapeshellarg($filter);
		$filter = "-f $filter";
	}
	$opts = escapeshellcmd($opts);

	$pathsArr = $channelsArr;
	$profile_name = substr($profile, 1);
	if ($SINGLE_MACHINE) {
		foreach ($pathsArr as &$path) {
			$path = $IPFIXCOL_DATA.$profile_name."/channels/".$path;
		}
		unset($path);

		// What will be printed to user
		$cmdBackup  = "$MPIEXEC_CMD $MPIEXEC_ARGS ";
		$cmdBackup .= "$FDISTDUMP_CMD $FDISTDUMP_ARGS $filter $opts ";
		$cmdBackup .= implode(" ", $pathsArr);

		// What will be executed
		$cmd  = "$MPIEXEC_CMD $MPIEXEC_ARGS ";
		$cmd .= "$FDISTDUMP_CMD $FDISTDUMP_ARGS $filter $opts --progress-bar-type=json --progress-bar-dest=".$TMP_DIR.$stamp.".$tab.json ";
		$cmd .= implode(" ", $pathsArr);
	}
	else {
		foreach ($pathsArr as &$path) {
			$path = $profile_name."/channels/".$path;
		}
		unset($path);

		// What will be printed to user
		$cmdBackup  = "$FDISTDUMP_HA_CMD $FDISTDUMP_HA_ARGS ".implode(" ", $pathsArr)." ";
		$cmdBackup .= "$MPIEXEC_CMD $MPIEXEC_ARGS ";
		$cmdBackup .= "$FDISTDUMP_CMD $FDISTDUMP_ARGS $filter $opts";

		// What will be executed
		$cmd  = "$FDISTDUMP_HA_CMD $FDISTDUMP_HA_ARGS ".implode(" ", $pathsArr)." "; // NOTE: add --verbose for debug
		$cmd .= "$MPIEXEC_CMD $MPIEXEC_ARGS ";
		$cmd .= "$FDISTDUMP_CMD $FDISTDUMP_ARGS $filter $opts --progress-bar-type=json --progress-bar-dest=".$TMP_DIR.$stamp.".$tab.json ";
	}

	$desc = array(
		0 => array ('pipe', 'r'),
		1 => array ('pipe', 'w'),
		2 => array ('pipe', 'w')
	);
	$pipes = array();

	$lock = fopen($TMP_DIR.$stamp.'.lock', 'r');							// Apply mutex, so the transaction file can only be modified by this thread
	if (!flock($lock, LOCK_EX)) {											// If that failed (
		echo '<div class=\'panel panel-danger\'>';							// Print this *very* serious error
		echo '<div class=\'panel-heading\'>Error</div>';
		echo '<div class=\'panel-body\'>Error acquiring lock before adding transaction.</div>';
		echo '</div>';
		exit(2);															// And end this thread )
	}																		// Else

	if (isset($LOG_DIR)) {
		file_put_contents("$LOG_DIR/query.log", date('r').": $cmd\n", FILE_APPEND);
	}

	$p = proc_open($cmd, $desc, $pipes);					// Execute the program command
	if($p == false) {														// If execution failed (
		echo '<div class=\'panel panel-danger\'>';							// Print this *very* serious error
		echo '<div class=\'panel-heading\'>Error</div>';
		echo '<div class=\'panel-body\'>proc_exec() failed. Enable PHP error/warning logging to see what\'s the matter.</div>';
		echo '</div>';
		exit(1);															// And end this thread )
	}																		// Else

	$stats = proc_get_status($p);

	addTransaction($TMP_DIR.$stamp, $tab, $stats['pid']);

	flock($lock, LOCK_UN);													// Release the lock
	fclose($lock);															// And close the lock file

	/*
		At this point, any other thread can call kill on this process,
		which will remove the transaction and finish this thread
		prematurely. Both stdout and stderr will be printed anyways, at
		least that parts that were produced before the kill command.
	*/

	$buffer = "";															// Stdout buffer
	$errlog = "";															// Stderr buffer

	if(is_resource($p)) {
		while($f = fgets($pipes[1])) {										// While any stdout is inbound
			$buffer .= $f;													// Buffer it
		}
		while($f = fgets($pipes[2])) {										// While any stderr is inbound
			$errlog .= $f;													// Buffer it
		}
	}

	$lock = fopen($TMP_DIR.$stamp.'.lock', 'r');							// Apply mutex, so the transaction file can only be modified by this thread
	if (!flock($lock, LOCK_EX)) {											// If that failed (
		echo '<div class=\'panel panel-danger\'>';							// Print this *very* serious error
		echo '<div class=\'panel-heading\'>Error</div>';
		echo '<div class=\'panel-body\'>Error acquiring lock before removing transaction.</div>';
		echo '</div>';
		exit(2);															// And end this thread )
	}																		// Else

	$index = findTransaction($TMP_DIR.$stamp, $tab, $pid);	// Find our transaction (pid is not needed, but it is a mandatory argument for function call)

	if($index != -1) {														// If index was found (i.e. nobody stopped this query)
		removeTransaction($TMP_DIR.$stamp, $index);							// Remove the transaction with success
	}

	flock($lock, LOCK_UN);													// Release the lock
	fclose($lock);															// And close the lock file

	/*
		At this point any other transaction related operation can occur.
	*/

	// BOOTSTRAP CODE:
	echo '<div class=\'panel panel-info\'>';
	echo '<div class=\'panel-heading\'><div class=\'row\'><div class=\'col-md-11\'>Output</div><div class=\'col-md-1\'><a href=\'#\' onclick=\'Local_clearTab("1");\'>Clear results</a></div></div></div>';
	echo '<div class=\'panel-body\'><pre>',$cmdBackup,'</pre><pre>';

	if (strlen($buffer) > 0) {
		$auxbuf = "";
		$size = strlen($buffer);
		for ($i = 0; $i < $size; $i++) {
			if ($buffer[$i] == ' ' || $buffer[$i] == PHP_EOL || $buffer[$i] == ',') {
				if (strlen($auxbuf) == 0) {
					echo $buffer[$i];
				}
				else {
					if (@inet_pton($auxbuf)) {								// Convert string into binary ip. If the function returned valid string, $auxbuf is an ip
						$auxbuf = "<a href='#' onclick=\"lookupGrab('$auxbuf');\" data-toggle='modal' data-target='#lookupModal'>$auxbuf</a>";
					}

					echo $auxbuf.$buffer[$i];
					$auxbuf = "";
				}
			}
			else
				$auxbuf .= $buffer[$i];
		}

		if (sizeof($auxbuf) > 0)	// Any trailing text will be printed out
			echo $auxbuf;
	}

	echo '</pre>';

	if (strlen($errlog) > 0)
		echo '<pre>',$errlog,'</pre>';
}

$mode	= $_GET['mode'];

if($mode == 'exec') {
	execDbRequest();
}
else if($mode == 'kill') {
	include '../config.php';													// Grab some constants from the config
	include '../misc/transactionsInclude.php';									// And get some functions related to transactions

	$stamp	= $_GET['stamp'];
	$tab	= $_GET['tab'];

	// Acquire lock
	$lock = fopen($TMP_DIR.$stamp.'.lock', 'r');
	if(flock($lock, LOCK_EX) == false) {
		echo 'Flock failed.\n';
		exit(1);
	}

	if(($index = findTransaction($TMP_DIR.$stamp, $tab, $pid)) != -1) {
		// Kill the process
		exec("kill -15 $pid");

		// Clear the transaction
		removeTransaction($TMP_DIR.$stamp, $index);
	}
	else {
		echo 'Transaction does not exist';
	}

	// Release the lock
	flock($lock, LOCK_UN);
	fclose($lock);
}
?>
