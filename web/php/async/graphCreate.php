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
		$fooname = "foo";
		if(preg_match("/traffic/",$var)) {
			$fooname = "bar";
		}
		
		for ($i = 0; $i < sizeof($sources); $i++) {
			$result .= "DEF:$fooname".strval($i+1)."=".$sources[$i].".rrd:$var:MAX ";
			//NOTE: $result .= "DEF:foo".strval($i+1)."=$FDUMP_FOLDER/$profile/$sources[$i].rrd:$var:MAX ";
		}
		
		// Because traffic must be converted from bytes to bits
		if($fooname == "bar") {
			for ($i = 0; $i < sizeof($sources); $i++) {
			$result .= "CDEF:foo".strval($i+1)."=bar".strval($i+1).",8,* ";
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
		$result	= "AREA:foo1#000000:\"$sources[0]\" ";
			
		for($i = 1; $i < sizeof($sources); $i++) {
			$result .= "AREA:foo".strval($i+1)."#000000:\"$sources[$i]\":STACK ";
		}
		
		return $result;
	}
	
	include "../config.php";
	
	$var		= $_GET["var"];		// Which variable to display
	$mode		= $_GET["mode"];	// 'thumb' OR 'json'
	$profile	= $_GET["profile"];	// TODO: Verify profile via user control
	//$profile	= str_replace("-", "/", $profile);
	$sources	= $_GET["sources"];	// TODO: Load from $profile
	$time		= $_GET["time"];
	
	$timeSplit	= explode(":", $time);
	$srcSplit	= explode(":", $sources);
	
	$def		= createDefinitions($srcSplit, $profile, $var);
	$render		= createRenderRules($srcSplit);
	
	$format		= "-a ";
	if($mode == "thumb") {
		$format .= "PNG --only-graph";
	}
	else {
		$format .= "JSONTIME";
	}
	
	// Descriptor array for proc open. Do not change unless you know what you're doing
	$desc = array(0 => array ('pipe', 'r'), 1 => array ('pipe', 'w'), 2 => array ('pipe', 'w') );
	
	// Create the image
	$p = proc_open("exec /opt/rrdtool16/bin/rrdtool graph - $format --start \"$timeSplit[0]\" --end \"$timeSplit[1]\" $def $render", $desc, $pipes, $IPFIXCOL_DATA."$profile/rrd/channels/");

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
