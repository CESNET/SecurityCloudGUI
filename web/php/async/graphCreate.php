<?php
	header(`Expires: Mon, 26 Jul 1997 05:00:00 GMT`);
	header(`Last-Modified: `.gmdate(`D, d M Y H:i:s`).` GMT`);
	header(`Cache-Control: no-cache, must-revalidate`);
	header(`Pragma: no-cache`);
?>
<?php
	/**
	*	Takes the array of sources, variable to display
	*	and produces the string containing all required
	*	variable definitions for rrdtool.
	*
	*	\return A properly formatted string with all
	*	definitions.
	*/
	function createDefinitions($sources, $profile, $var) {
		$result = "";
		$fooname = 'foo';
		if(preg_match('/traffic/',$var)) {
			$fooname = 'bar';
		}
		
		$size = sizeof($sources);
		for ($i = 0; $i < $size; $i++) {
			$result .= "DEF:$fooname".strval($i+1).'='.$sources[$i].".rrd:$var:MAX ";
			//NOTE: $result .= "DEF:foo".strval($i+1)."=$FDUMP_FOLDER/$profile/$sources[$i].rrd:$var:MAX ";
		}
		
		// Because traffic must be converted from bytes to bits
		if($fooname == "bar") {
			$size = sizeof($sources);
			for ($i = 0; $i < $size; $i++) {
			$result .= 'CDEF:foo'.strval($i+1).'=bar'.strval($i+1).',8,* ';
			//NOTE: $result .= "CDEF:foo0=bar0,8,* "
			}
		}

		return $result;
	}
	
	/**
	*	Takes an array of sources, their respective colours
	*	and user defined mode and produces the string
	*	containing all required render definitions for rrd.
	*
	*	If $mode has it's last bit set ($mode % 2 == 1) then
	*	the graph will be rendered with lines instead of areas
	*	If $mode has at least single other bit set ($mode / 2 > 0)
	*	the graph will be rendered comparative instead of
	*	aggregative.
	*
	*	\return A properly formatted string with all
	*	definitions.
	*/
	function createRenderRules($sources) {
		$base = 224;
		$size = sizeof($sources);
		$ratio = 192 / $size;
		
		$c = dechex($base - $ratio);
		$result	= "AREA:foo1#$c".$c."$c:\"$sources[0]\" ";
			
		for($i = 1; $i < $size; $i++) {
			$c = dechex($base - ($i + 1) * $ratio);
			$result .= 'AREA:foo'.strval($i+1)."#$c".$c."$c:\"$sources[$i]\":STACK ";
		}
		
		return $result;
	}
	
	include '../config.php';
	include '../misc/profileMethods.php';
	
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
		//$format .= 'JSONTIME';
		$format .= 'JSON';
	}
	
	// Descriptor array for proc open. Do not change unless you know what you're doing
	$desc = array(0 => array ('pipe', 'r'), 1 => array ('pipe', 'w'), 2 => array ('pipe', 'w') );
	
	// Create the image
	$p = proc_open("exec $RRDTOOL graph - $format --start \"$timeSplit[0]\" --end \"$timeSplit[1]\" $def $render", $desc, $pipes, $IPFIXCOL_DATA."$profile/rrd/channels/");

	//echo "exec rrdtool graph - $format --start \"$timeSplit[0]\" --end \"$timeSplit[1]\" $def $render"; exit;
	
	$buffer = "";
	if(is_resource($p)) {
		while($f = fgets($pipes[1])) {
			$buffer .= $f;
		}
	}
	
	echo $buffer;
	exit;
?>
