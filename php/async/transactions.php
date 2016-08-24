<?php
	include "../misc/transactionsInclude.php";

function initTransactions($stamp) {
	include "../config.php";
	
	if (!is_dir($TMP_DIR)) {
		if(!mkdir($TMP_DIR, 0777)) {
			echo "Could not create the $TMP_DIR";
			exit(0);
		}
	}
	// File with transactions will be created automatically when needed
	// All we need for now is a working lock file
	if(($file = fopen($TMP_DIR.$stamp.".lock", "w")) == false) {
		echo "Transaction file creation failed miserably! Try 'chown a+w transactions' inside of secloud-web/php folder";
		exit(1);
	}
	else {
		echo "Transaction files succesfully created.";
	}
	fwrite($file, $stamp."\n");
	fclose($file);
}

function deinitTransactions($stamp) {
	// Empty transactions first
	$lock = fopen($TMP_DIR.$stamp.".lock", "r");
	flock($lock, LOCK_EX);
	
	for($i = 0; $i < $MAX_TABS; $i++) {	// Search for every tab in transactions
		$index = findTransaction($TMP_DIR.$stamp, "$i", $pid);
		
		if ($index > -1) {				// And if it's there
			exec("kill -9 $pid");		// Kill the process
		}
	}
	
	// Release the lock
	flock($lock, LOCK_UN);
	fclose($lock);
	
	// Remove the transaction file and a protection lock file
	unlink($TMP_DIR.$stamp.".lock");
	unlink($TMP_DIR.$stamp);
	
	echo "Transactions were all removed.";
}

$mode = $_GET["mode"];
$stamp = $_GET["stamp"];

	if (!verifyUserstamp($stamp)) {
		exit(1);
	}
	
	if ($mode == "init") {
		initTransactions($stamp);
	}
	else if ($mode == "deinit") {
		deinitTransactions($stamp);
	}
?>
