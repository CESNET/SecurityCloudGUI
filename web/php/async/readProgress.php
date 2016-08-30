<?php
	header(`Expires: Mon, 26 Jul 1997 05:00:00 GMT`);
	header(`Last-Modified: `.gmdate(`D, d M Y H:i:s`).` GMT`);
	header(`Cache-Control: no-cache, must-revalidate`);
	header(`Pragma: no-cache`);
?>
<?php
	include '../config.php';

	$stamp		= $_GET['userstamp'];
	$tab		= $_GET['tab'];
	$profile	= $_GET['profile'];
	$mode		= $_GET['mode'];
	$filename	= $stamp.'_'.$tab.'.json';
	
	if ($mode == 'read') {
		// Read the file into array
		$buffer = file($TMP_DIR.$stamp.".$tab.json", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		
		for($i = 0; $i < sizeof($buffer); $i++) {
			echo $buffer[$i];
		}
	}
	else if ($mode == 'delete') {
		unlink($TMP_DIR.$stamp.".$tab.json");
	}
?>
