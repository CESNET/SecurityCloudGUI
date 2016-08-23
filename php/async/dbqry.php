<?php
/* MAIN EXECUTION CODE */
function execDbRequest() {														// This gets called when this thread is started
	// *** include ***
	include "../config.php";													// Grab some constants from the config
	include "../misc/transactionsInclude.php";									// And get some functions related to transactions
	include "../misc/profileClass.php";
	include "../misc/profileMethods.php";
	
	// *** get url params ***
	$opts	= $_GET["opts"];													// Grab the url params (
	$filter = $_GET["filter"];													// 	...
	$stamp	= $_GET["stamp"];													//	...
	$tab	= $_GET["tab"];														//	...
	$src	= $_GET["src"];														//  ...
	
	/* CREATE A MASTER TREE FROM XML */
	$TREE_PROFILE = new Profile();												// Full tree of profiles
	createProfileTreeFromXml(loadXmlFile($IPFIXCOL_CFG), "/", $TREE_PROFILE);	// Fill it with ALL necessary data
	
	/* COLLECT USER-AVAILABLE PROFILES, COLLECT USER-SELECTED PROFILE AND VERIFY IT */
	$ARR_AVAILS = getAvailableProfiles("me");
	$profile = getCurrentProfile();
	if (!verifySelectedProfile($profile, $ARR_AVAILS)) {
		echo "You don't have the privileges to access the profile $profile<br>";
		exit(1);
	}
	
	/* SEARCH FOR SELECTED SUBPROFILE ROOT */
	$aux = null;
	searchForProfile($TREE_PROFILE, $profile, $aux);
	if ($aux == null) {
		echo "The profile $aux does not exist<br>";
		exit(2);
	}
	
	if($aux->getShadow()) {
		$profile = $aux->getParentName();
		
		$aaux = $aux->getParent();
		foreach ($aaux->getChannels() as $c) {
			$src = "";
			$src .= $c->getName()."/ ";
		}
		
		$f = "";
		foreach($aux->getChannels() as $c) {
			if (strlen($f) != 0) {
				$f .= " or ";
			}
			$f .= "(".$c->getFilter().")";
		}
		$filter = "(($filter) and ($f))";
		
		echo "$filter<br>";
	}
	
	$cmdBackup = "$FDUMP -f $filter $opts";
	
	//echo strlen($filter)." ".$filter."<br>";
	if(strlen($filter) > 2) {
		$filter = escapeshellarg($filter);
		$filter = "-f $filter";
	}
	$opts = escapeshellcmd($opts);
	
	// *** proc_open params ***
	$cmd = "exec $FDUMP $filter $opts --progress-bar-type=json --progress-bar-dest=".$TRANS_FOLDER.$stamp.".$tab.json $src";	// <---------- $CMD --------------
	$desc = array(
		0 => array ('pipe', 'r'),
		1 => array ('pipe', 'w'),
		2 => array ('pipe', 'w')
	);
	$pipes = array();
	
	$lock = fopen("../misc/transactions/$stamp.lock", "r");					// Apply mutex, so the transaction file can only be modified by this thread
	if (!flock($lock, LOCK_EX)) {											// If that failed (
		echo "<div class='panel panel-danger'>";							// Print this *very* serious error
		echo "<div class='panel-heading'>Error</div>";
		echo "<div class='panel-body'>Error acquiring lock before adding transaction.</div>";
		echo "</div>";
		exit(2);															// And end this thread )
	}																		// Else
	
	$p = proc_open($cmd, $desc, $pipes, "../../data$profile/");				// Execute the program command
	if($p == false) {														// If execution failed (
		echo "<div class='panel panel-danger'>";							// Print this *very* serious error
		echo "<div class='panel-heading'>Error</div>";
		echo "<div class='panel-body'>proc_exec() failed. Enable PHP error/warning logging to see what's the matter.</div>";
		echo "</div>";
		exit(1);															// And end this thread )
	}																		// Else

	$stats = proc_get_status($p);
	
	addTransaction("../misc/transactions/$stamp", $tab, $stats['pid']);	
	
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
	
	$lock = fopen("../misc/transactions/$stamp.lock", "r");					// Apply mutex, so the transaction file can only be modified by this thread
	if (!flock($lock, LOCK_EX)) {											// If that failed (
		echo "<div class='panel panel-danger'>";							// Print this *very* serious error
		echo "<div class='panel-heading'>Error</div>";
		echo "<div class='panel-body'>Error acquiring lock before removing transaction.</div>";
		echo "</div>";
		exit(2);															// And end this thread )
	}																		// Else
	
	$index = findTransaction("../misc/transactions/".$stamp, $tab, $pid);	// Find our transaction (pid is not needed, but it is a mandatory argument for function call)
	
	if($index != -1) {														// If index was found (i.e. nobody stopped this querry)
		removeTransaction("../misc/transactions/".$stamp, $index);			// Remove the transaction with success
	}
	
	flock($lock, LOCK_UN);													// Release the lock
	fclose($lock);															// And close the lock file
	
	/*
		At this point any other transaction related operation can occur.
	*/
	
	// BOOTSTRAP CODE:
	echo "<div class='panel panel-info'>";									// Print info about used parameters
	echo "<div class='panel-heading'>Querry parameters</div>";				// Heading will be light blue
	echo "<div class='panel-body'><pre>$cmdBackup</pre></div>";					// Print commands
	echo "</div>";
	if (strlen($buffer) > 0) {
		echo "<div class='panel panel-success'>";							// Print stdout from fdistdump
		echo "<div class='panel-heading'>Querry output</div>";				// Heading will be dark blue
		echo "<div class='panel-body'><pre>";
		$auxbuf = "";
		for ($i = 0; $i < strlen($buffer); $i++) {
			if ($buffer[$i] == ' ' || $buffer[$i] == '\n' || $buffer[$i] == ',') {
				if (strlen($auxbuf) == 0) {
					echo $buffer[$i];
				}
				else {//http://rest.db.ripe.net/search.json?query-string=194.228.92.50&flags=no-filtering
					if (@inet_pton($auxbuf)) {								// Convert string into binary ip. If the function returned valid string, $auxbuf is an ip
						$auxbuf = "<a href='#' onclick=\"lookupGrab('$auxbuf');\" data-toggle='modal' data-target='#lookupModal'>$auxbuf</a>";
					}
				
					echo $auxbuf.$buffer[$i];
					$auxbuf = "";
				}
			}
			else {
				$auxbuf .= $buffer[$i];
			}
		}
		echo "</pre></div>";												// Print stdout
		echo "</div>";
	}
	if(strlen($errlog) > 0) {
		echo "<div class='panel panel-danger'>";							// Print stderr from fdistdump
		echo "<div class='panel-heading'>Querry errors</div>";				// Heading will be red
		echo "<div class='panel-body'><pre>$errlog</pre></div>";			// Print stderr
		echo "</div>";
	}
}

$mode	= $_GET["mode"];

if($mode == "exec") {
	execDbRequest();
}
else if($mode == "kill") {
	include "../config.php";													// Grab some constants from the config
	include "../misc/transactionsInclude.php";									// And get some functions related to transactions
	
	$stamp	= $_GET["stamp"];
	$tab	= $_GET["tab"];

	// Acquire lock
	$lock = fopen("../misc/transactions/$stamp.lock", "r");
	if(flock($lock, LOCK_EX) == false) {
		echo "Flock failed.\n";
	}

	if(($index = findTransaction("../misc/transactions/".$stamp, $tab, $pid)) != -1) {
		// Kill the process
		echo "PID = ".$pid.", index = ".$index;
		exec("kill -9 $pid");
		
		// Clear the transaction
		removeTransaction("../misc/transactions/".$stamp, $index);
	}
	else {
		echo "Transaction does not exist";
	}
	
	// Release the lock
	flock($lock, LOCK_UN);
	fclose($lock);
}
?>
