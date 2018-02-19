<?php
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: ".gmdate('D, d M Y H:i:s')." GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
?>
<?php
	include '../config.php';

	if (!isset($_GET['action'])) {
		echo '{"status": "failure", "message": "No action parameter!" data: []}';
		exit(1);
	}
	$action = $_GET['action'];
	
	if ($action == "load") {
		// Load all filters and their names from dedicated file and send them back
		$file = fopen($FILTER_STORAGE_PATH, "r");
		$output = "";
		if ($file != NULL) {
			while (($line = fgetcsv($file)) !== FALSE) {
				if ($line != NULL && sizeof($line) == 2) {
					$output .= '{"name": "'.$line[0].'", "filter": "'.$line[1].'"},';
				}
			}
			$output = substr($output, 0, -1);
			fclose($file);
		}
		
		echo '{"status":"success", "data": ['.$output.']}';
	}
	else if ($action == "save") {
		// Append new filter to dedicated file
		if (!isset($_GET['name']) || !isset($_GET['filter'])) {
			echo '{"status": "failure", "message": "No name or filter parameter!" data: []}';
		}
		$name = $_GET['name'];
		$filter = $_GET['filter'];
		
		if (($file = fopen($FILTER_STORAGE_PATH, "a")) == false) {
			echo '{"status": "failure", "message": "Could not open file for storing filters!" data: []}';
			exit(1);
		}
		fwrite($file, "\"$name\",\"$filter\"\n");
		fclose($file);
		
		echo '{"status": "success", "data": []}';
	}
?>