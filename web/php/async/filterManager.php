<?php
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: ".gmdate('D, d M Y H:i:s')." GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
?>
<?php
	include '../config.php';

	if (!isset($_GET['action'])) {
		echo '{"status": "failure", "message": "No action parameter!", data: []}';
		exit(1);
	}
	$action = $_GET['action'];
	
	if ($action == "load") {
		$lock = fopen('../app.lock', 'r');
		if(!flock($lock, LOCK_EX)) {
			echo '{"status: "failure", "message": "Cannot acquire app.lock mutex!", data: []}';
			exit(1);
		}
		
		// Load all filters and their names from dedicated file and send them back
		$file = fopen($FILTER_STORAGE_PATH, "r");
		$output = "";
		if ($file != NULL) {
			while (($line = fgetcsv($file)) !== false) {
				if ($line != NULL && sizeof($line) == 2) {
					$output .= '{"name": "'.$line[0].'", "filter": "'.$line[1].'"},';
				}
			}
			$output = substr($output, 0, -1);
			fclose($file);
		}
		
		flock($lock, LOCK_UN);
		fclose($lock);
		
		echo '{"status":"success", "data": ['.$output.']}';
	}
	else if ($action == "save") {
		$lock = fopen('../app.lock', 'r');
		if(!flock($lock, LOCK_EX)) {
			echo '{"status: "failure", "message": "Cannot acquire app.lock mutex!", data: []}';
			exit(1);
		}
		
		// Append new filter to dedicated file
		if (!isset($_GET['name']) || !isset($_GET['filter'])) {
			echo '{"status": "failure", "message": "No name or filter parameter!", data: []}';
			exit(1);
		}
		$name = $_GET['name'];
		$filter = $_GET['filter'];
		
		if (($file = fopen($FILTER_STORAGE_PATH, "a")) == false) {
			echo '{"status": "failure", "message": "Could not open file for storing filters!", data: []}';
			exit(1);
		}
		fwrite($file, "\"$name\",\"$filter\"\n");
		fclose($file);
		
		// Free the lock
		flock($lock, LOCK_UN);
		fclose($lock);
		
		echo '{"status": "success", "data": []}';
	}
	else if ($action == "delete") {
		if (!isset($_GET['name']) || !isset($_GET['filter'])) {
			echo '{"status: "failure", "message": "No name or filter parameter!", data: []}';
			exit(1);
		}
		
		// Acquire the lock
		$lock = fopen('../app.lock', 'r');
		if(!flock($lock, LOCK_EX)) {
			echo '{"status: "failure", "message": "Cannot acquire app.lock mutex!", data: []}';
			exit(1);
		}
		
		// First, load all filters into memory, excluding the to-delete one
		$file = fopen($FILTER_STORAGE_PATH, "r");
		$name = $_GET['name'];
		$filter = $_GET['filter'];
		$output = "";
		$tosave = "";
		if ($file != NULL) {
			while (($line = fgetcsv($file)) !== false) {
				if ($line != NULL && sizeof($line) == 2) {
					if ($line[0] != $name && $line[1] != $filter) {
						$output .= '{"name": "'.$line[0].'", "filter": "'.$line[1].'"},';
						$tosave .= '"'.$line[0].'","'.$line[1].'"'.PHP_EOL;
					}
				}
			}
			$output = substr($output, 0, -1);
			fclose($file);
		}
		
		// Now export $tosave into the file
		if (($file = fopen($FILTER_STORAGE_PATH, "w")) == false) {
			echo '{"status": "failure", "message": "Could not open file for storing filters!", data: []}';
			exit(1);
		}
		fwrite($file, $tosave);
		fclose($file);
		
		// Free the lock
		flock($lock, LOCK_UN);
		fclose($lock);
		
		echo '{"status":"success", "data": ['.$output.']}';
	}
?>