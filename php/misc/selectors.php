<?php
	$ARR_GRAPH_VARS = array("flows", "traffic", "packets", "flows_tcp", "traffic_tcp", "packets_tcp", "flows_udp", "traffic_udp", "packets_udp", "flows_icmp", "traffic_icmp", "packets_icmp", "flows_other", "traffic_other", "packets_other", "traffic_max", "packets_max", "traffic_avg", "packets_avg");
	$ARR_GRAPH_NAME = array("Flows/s Any", "Traffic Any", "Packets/s Any", "Flows/s TCP", "Traffic TCP", "Packets/s TCP", "Flows/s UDP", "Traffic UDP", "Packets/s UDP", "Flows/s ICMP", "Traffic ICMP", "Packets/s ICMP", "Flows/s Other", "Traffic Other", "Packets/s Other", "Traffic Max", "Packets Max", "Traffic Average", "Packets Average");
	$ARR_GRAPH_LBLS = array("Flows/s Any", "Bits/s Any", "Packets/s Any", "Flows/s TCP", "Bits/s TCP", "Packets/s TCP", "Flows/s UDP", "Bits/s UDP", "Packets/s UDP", "Flows/s ICMP", "Bits/s ICMP", "Packets/s ICMP", "Flows/s Other", "Bits/s Other", "Packets/s Other", "Traffic Max", "Packets Max", "Traffic Average", "Packets Average");
	
	$ARR_OPTIONS_CODE_LIMITTO = array("-l 0", "-l 10", "-l 20", "-l 50", "-l 100", "-l 200", "-l 500", "-l 1000", "-l 2000", "-l 5000", "-l 10000");
	$ARR_OPTIONS_NAME_LIMITTO = array("-", "10", "20", "50", "100", "200", "500", "1000", "2000", "5000", "10000");
	
	// -a; Loaded from script
	// Exec the script
	// Load the buffer
	$cmd = "libnf-info | tail -n +4 | sed -r 's/(\s\s)+/\t/g' | cut -f3,4 | tr '\t' ';' | tr '\n' ':'";
	$buffer = exec($cmd);
	// Split the shit
	$ARR_OPTIONS_NAME_FIELDS = array();
	$ARR_OPTIONS_HINT_FIELDS = array();
	$buffer = explode(":", $buffer);					// Rows
	for($i = 0; $i < sizeof($buffer)-1; $i++) {
		$subsplit = explode(";", $buffer[$i]);			// Name;Hint
		$ARR_OPTIONS_NAME_FIELDS[] = $subsplit[0];
		$ARR_OPTIONS_HINT_FIELDS[] = $subsplit[1];
	}
?>
