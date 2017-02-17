<?php
	$ARR_GRAPH_VARS = array('flows', 'traffic', 'packets', 'flows_tcp', 'traffic_tcp', 'packets_tcp', 'flows_udp', 'traffic_udp', 'packets_udp', 'flows_icmp', 'traffic_icmp', 'packets_icmp', 'flows_other', 'traffic_other', 'packets_other', 'traffic_max', 'packets_max', 'traffic_avg', 'packets_avg');
	$ARR_GRAPH_NAME = array('Flows/s All', 'Bits/s All', 'Packets/s All', 'Flows/s TCP', 'Bits/s TCP', 'Packets/s TCP', 'Flows/s UDP', 'Bits/s UDP', 'Packets/s UDP', 'Flows/s ICMP', 'Bits/s ICMP', 'Packets/s ICMP', 'Flows/s Other', 'Bits/s Other', 'Packets/s Other', 'Bits/s Max', 'Packets Max', 'Bits/s Average', 'Packets Average');
	$ARR_GRAPH_LBLS = array('Flows/s All', 'Bits/s All', 'Packets/s All', 'Flows/s TCP', 'Bits/s TCP', 'Packets/s TCP', 'Flows/s UDP', 'Bits/s UDP', 'Packets/s UDP', 'Flows/s ICMP', 'Bits/s ICMP', 'Packets/s ICMP', 'Flows/s Other', 'Bits/s Other', 'Packets/s Other', 'Bits/s Max', 'Packets Max', 'Bits/s Average', 'Packets Average');
	
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
