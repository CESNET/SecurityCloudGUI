<?php
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: ".gmdate('D, d M Y H:i:s')." GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
?>
<?php
	include '../config.php';
	include '../misc/profileMethods.php';
	include 'graphCreateMethods.php';

	$var		= $_GET['var'];		// Which variable to display
	$mode		= $_GET['mode'];	// 'thumb' OR 'json'
	$sources	= $_GET['sources'];	// Channels; bad naming in during early development
	$time		= $_GET['time'];

	/* COLLECT USER-AVAILABLE PROFILES, COLLECT USER-SELECTED PROFILE AND VERIFY IT */
	$ARR_AVAILS = getAvailableProfiles('me');
	$profile = getCurrentProfile();
	if (!verifySelectedProfile($profile, $ARR_AVAILS)) {
		echo "You don't have the privileges to access the profile $profile<br>";
		exit(1);
	}
	unset($ARR_AVAILS);

	$timeSplit	= explode(':', $time);
	$srcSplit	= explode(':', $sources);

	if (!preg_match('/^[0-9]+\:[0-9]+$/', $time)) {
		echo "$time is not a valid time argument. It should be two UNIX timestamps divided by a colon.";
		exit(1);
	}
	else if (!preg_match('/^[a-zA-Z][a-zA-Z_]*$/', $var)) {
		echo "$var is not a valid name string.";
		exit(2);
	}
	else if (!preg_match('/^([a-zA-Z_][a-zA-Z0-9_]*)(\:([a-zA-Z_][a-zA-Z0-9_]*))*$/', $sources)) {
		echo "$sources is not a valid channels string.";
		exit(3);
	}

	$def		= createDefinitions($srcSplit, $profile, $var);
	$render		= createRenderRules($srcSplit);

	$format		= '-a ';
	if($mode == 'thumb') {
		$format .= 'PNG --only-graph';
	}
	else {
		$format .= 'JSONTIME';
	}

	// Descriptor array for proc open. Do not change unless you know what you're doing
	$cmd = "$RRDTOOL_CMD graph - $format -Z --start \"$timeSplit[0]\" --end \"$timeSplit[1]\" $def $render";
	$desc = array(0 => array ('pipe', 'r'), 1 => array ('pipe', 'w'), 2 => array ('pipe', 'w') );

	// Either return .png data of an image
	if ($mode == 'thumb') {
		if ($SINGLE_MACHINE) {
			echo getGraphThumb(strval($cmd), $desc, $pipes, "");
		}
		else {
			echo getGraphThumb(strval($cmd), $desc, $pipes, $SLAVE_HOSTNAMES[0]);
		}
		exit;
	}

	// Or return graph JSON data
	if ($SINGLE_MACHINE)
		echo getGraphJSON(strval($cmd), $desc, $pipes, "");
	else {
		$js = null;
		foreach ($SLAVE_HOSTNAMES as $sh) {
			$buffer = getGraphJSON(strval($cmd), $desc, $pipes, $sh);

			if ($js == null)
				$js = json_decode($buffer, true);
			else
				updateGraphJSON($js, $buffer);
		}

		echo json_encode($js);
	}

	exit;
?>
