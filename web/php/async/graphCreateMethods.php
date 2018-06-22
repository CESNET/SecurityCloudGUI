<?php
	/**
	*	Takes the array of sources, variable to display
	*	and produces the string containing all required
        *	variable definitions for rrdtool.
        *	Required format: DEF:<vname>=<rrdfile>:<ds-name>:<CF>
	*
	*	\return A properly formatted string with all
	*	definitions.
	*/
	function createDefinitions($sources, $profile, $ds_name) {
		global $IPFIXCOL_DATA;

		$result = "";
		for ($i = 0; $i < sizeof($sources); $i++) {
			$vname = $sources[$i];
			$rrdfile = "$IPFIXCOL_DATA/$profile/rrd/channels/$sources[$i].rrd";
			$result .= "DEF:$vname=$rrdfile:$ds_name:MAX ";
		}

		return $result;
	}

	/**
	*	Takes an array of sources, their respective colours
	*	and user defined mode and produces the string
	*	containing all required render definitions for rrd.
	*	Required format: AREA:value[#color][:[legend][:STACK][:skipscale]]
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

		$value = $sources[0];
		$c = dechex($base - $ratio);
		$color = "$c$c$c";
		$legend = "\"$sources[0]\"";
		$result	= "AREA:$value#$color:$legend ";

		for($i = 1; $i < $size; $i++) {
			$value = $sources[$i];
			$c = dechex($base - ($i + 1) * $ratio);
			$color = "$c$c$c";
			$legend = "\"$sources[$i]\"";
			$result	.= "AREA:$value#$color:$legend:STACK ";
		}

		return $result;
	}

	/**
	 * Based on $cmd, retrieves JSON string for rrd graph.
	 * Each line of loaded rrd output is yielded.
	 */
	function getGraphJSON($cmd, &$desc, &$pipes) {
		$desc = array(0 => array ('pipe', 'r'), 1 => array ('pipe', 'w'), 2 => array ('pipe', 'w') );
		$p = proc_open($cmd, $desc, $pipes);

		$buffer = "";
		if(is_resource($p))
			while($f = fgets($pipes[1]))
				$buffer .= $f;

		return $buffer;
	}

	/**
	 *  @brief Retrieves graph as png image binary source
	 *
	 *  @param [in] $cmd rrdtool command
	 *  @param [in] $desc Array of descriptors
	 *  @param [in] $pipes Communication pipes
	 *  @return Binary source of an image
	 */
	function getGraphThumb($cmd, &$desc, &$pipes) {
		$p = proc_open($cmd, $desc, $pipes);

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
