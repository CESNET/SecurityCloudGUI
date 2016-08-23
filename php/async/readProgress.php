<?php
	include "../config.php";

	$stamp		= $_GET["userstamp"];
	$tab		= $_GET["tab"];
	$profile	= $_GET["profile"];
	$mode		= $_GET["mode"];
	$filename	= $stamp."_".$tab.".json";
	
	if ($mode == "read") {
		// Read the file into array
		$buffer = file($TRANS_FOLDER.$stamp.".$tab.json", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		
		for($i = 0; $i < sizeof($buffer); $i++) {
			echo $buffer[$i];
		}
	}
	else if ($mode == "delete") {
		unlink($TRANS_FOLDER.$stamp.".$tab.json");
	}
?>
