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
	
	/**
	 * Based on $cmd, retrieves JSON string for rrd graph.
	 * Each line of loaded rrd output is yielded. $slave
	 * is either "" for $SINGLE_MACHINE or any valid name
	 * from $SLAVE_HOSTNAMES.
	 */
	function getGraphJSON($cmd, &$desc, &$pipes, $slave) {
		global $IPFIXCOL_DATA, $profile;
		
		//$cmd = "exec $RRDTOOL graph - $format --start \"$timeSplit[0]\" --end \"$timeSplit[1]\" $def $render";
		$desc = array(0 => array ('pipe', 'r'), 1 => array ('pipe', 'w'), 2 => array ('pipe', 'w') );
		$p = proc_open($cmd, $desc, $pipes, $IPFIXCOL_DATA.$slave."$profile/rrd/channels/");
		
		$buffer = "";
		if(is_resource($p))
			while($f = fgets($pipes[1]))
				$buffer .= $f;
			
		return $buffer;
	}
	
	/**
	 *  When not $SINGLE_MACHINE then the resulting JSON
	 *  needs to be merge from all slaves. This function
	 *  allows to update resulting JSON with buffer loaded
	 *  using getGraphJSON.
	 */
	function updateGraphJSON(&$json, &$buffer) {
		$aux = json_decode($buffer, true);
		$dataS = sizeof($aux["data"]);
				$chnlS = sizeof($aux["data"][0]);
				for ($i = 0; $i < $dataS; $i++)
					for ($p = 1; $p < $chnlS; $p++)
						$json["data"][$i][$p] += $aux["data"][$i][$p];
	}
?>