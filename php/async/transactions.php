<?php
	include "../misc/transactionsInclude.php";

function initTransactions($stamp) {
	// File with transactions will be created automatically when needed
	// All we need for now is a working lock file
	if(($file = fopen("../misc/transactions/$stamp.lock", "w")) == false) {
		echo "Transaction file creation failed miserably! Try 'chown a+w transactions' inside of secloud-web/php folder";
	}
	else {
		echo "Transaction files succesfully created.";
	}
	fwrite($file, $stamp."\n");
	fclose($file);
}

function deinitTransactions($stamp) {
	// Empty transactions first
	$lock = fopen("../misc/transactions/$stamp.lock", "r");
	flock($lock, LOCK_EX);
	
	for($i = 0; $i < $MAX_TABS; $i++) {	// Search for every tab in transactions
		$index = findTransaction("../misc/transactions/$stamp", "Tab$i_", $pid);
		
		if ($index > -1) {				// And if it's there
			exec("kill -9 $pid");		// Kill the process
		}
	}
	
	// Release the lock
	flock($lock, LOCK_UN);
	fclose($lock);
	
	// Remove the transaction file and a protection lock file
	unlink("../misc/transactions/$stamp.lock");
	unlink("../misc/transactions/$stamp");
	
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
