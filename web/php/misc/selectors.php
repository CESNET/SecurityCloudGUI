<?php
	$ARR_GRAPH_VARS = array('flows', 'packets', 'traffic', 'flows_tcp', 'packets_tcp', 'traffic_tcp', 'flows_udp', 'packets_udp', 'traffic_udp', 'flows_icmp', 'packets_icmp', 'traffic_icmp', 'flows_other', 'packets_other', 'traffic_other', 'packets_max', 'traffic_max', 'packets_avg', 'traffic_avg',);
	$ARR_GRAPH_NAME = array('Flows/s All', 'Packets/s All', 'Bits/s All', 'Flows/s TCP', 'Packets/s TCP', 'Bits/s TCP', 'Flows/s UDP', 'Packets/s UDP', 'Bits/s UDP', 'Flows/s ICMP', 'Packets/s ICMP', 'Bits/s ICMP', 'Flows/s Other', 'Packets/s Other', 'Bits/s Other', 'Packets Max', 'Bits/s Max', 'Packets Average', 'Bits/s Average',);
	$ARR_GRAPH_LBLS = array('Flows/s All', 'Packets/s All', 'Bits/s All', 'Flows/s TCP', 'Packets/s TCP', 'Bits/s TCP', 'Flows/s UDP', 'Packets/s UDP', 'Bits/s UDP', 'Flows/s ICMP', 'Packets/s ICMP', 'Bits/s ICMP', 'Flows/s Other', 'Packets/s Other', 'Bits/s Other', 'Packets Max', 'Bits/s Max', 'Packets Average', 'Bits/s Average',);
	
	$ARR_OPTIONS_CODE_LIMITTO = array(/*'-l 0',*/ '-l 10', '-l 20', '-l 50', '-l 100', '-l 200', '-l 500', '-l 1000', '-l 2000', '-l 5000', '-l 10000');
	$ARR_OPTIONS_NAME_LIMITTO = array(/*'-',*/ '10 records', '20 records', '50 records', '100 records', '200 records', '500 records', '1000 records', '2000 records', '5000 records', '10000 records');
	
	// -a; Loaded from script
	// Exec the script
	// Load the buffer
	$cmd = 'libnf-info | tail -n +4 | sed -r \'s/(\s\s)+/\t/g\' | cut -f3,4 | tr \'\t\' \';\' | tr \'\n\' \':\'';
	$buffer = exec($cmd);
	// Split the shit
	$ARR_OPTIONS_NAME_FIELDS = array();
	$ARR_OPTIONS_HINT_FIELDS = array();
	$buffer = explode(':', $buffer);					// Rows
	$size = (int)sizeof($buffer) - 1;
	for($i = 0; $i < $size; $i++) {
		$subsplit = explode(';', $buffer[$i]);			// Name;Hint
		$ARR_OPTIONS_NAME_FIELDS[] = $subsplit[0];
		$ARR_OPTIONS_HINT_FIELDS[] = $subsplit[1];
	}
?>
