<?php
	$ARR_GRAPH_VARS = array('flows', 'packets', 'traffic', 'flows_tcp', 'packets_tcp', 'traffic_tcp', 'flows_udp', 'packets_udp', 'traffic_udp', 'flows_icmp', 'packets_icmp', 'traffic_icmp', 'flows_other', 'packets_other', 'traffic_other', 'packets_max', 'traffic_max', 'packets_avg', 'traffic_avg',);
	$ARR_GRAPH_NAME = array('Flows/s All', 'Packets/s All', 'Bytes/s All', 'Flows/s TCP', 'Packets/s TCP', 'Bytes/s TCP', 'Flows/s UDP', 'Packets/s UDP', 'Bytes/s UDP', 'Flows/s ICMP', 'Packets/s ICMP', 'Bytes/s ICMP', 'Flows/s Other', 'Packets/s Other', 'Bytes/s Other', 'Packets Max', 'Bytes/s Max', 'Packets Average', 'Bytes/s Average',);
	$ARR_GRAPH_LBLS = array('Flows/s All', 'Packets/s All', 'Bytes/s All', 'Flows/s TCP', 'Packets/s TCP', 'Bytes/s TCP', 'Flows/s UDP', 'Packets/s UDP', 'Bytes/s UDP', 'Flows/s ICMP', 'Packets/s ICMP', 'Bytes/s ICMP', 'Flows/s Other', 'Packets/s Other', 'Bytes/s Other', 'Packets Max', 'Bytes/s Max', 'Packets Average', 'Bytes/s Average',);
	
	$ARR_OPTIONS_CODE_LIMITTO = array(/*'-l 0',*/ '-l 10', '-l 20', '-l 50', '-l 100', '-l 200', '-l 500', '-l 1000', '-l 2000', '-l 5000', '-l 10000');
	$ARR_OPTIONS_NAME_LIMITTO = array(/*'-',*/ '10 records', '20 records', '50 records', '100 records', '200 records', '500 records', '1000 records', '2000 records', '5000 records', '10000 records');
	
	class Option {
		public $name;
		public $hint;
		
		function __construct($name, $hint) {
			$this->name = $name;
			$this->hint = $hint;
		}
	};
	
	// Orderby, aggregate, fields
	$ARR_OPTIONS_AGGREG_FIELDS = array(
		// Preffered
		new Option("srcip", "Source IP address"),
		new Option("dstip", "Destination IP address"),
		new Option("ip", "Source or destination ip address (pair field)"),
		new Option("srcport", "Source port"),
		new Option("dstport", "Destination port"),
		new Option("port", "Source or destination port (pair field)"),
		new Option("proto",  "IP protocol"),
		new Option("flags", "TCP flags"),
		
		// Rest
		new Option("flows", "The number of flows (aggregated)"),
		new Option("bytes", "The number of bytes"),
		new Option("packets", "The number of packets"),
		new Option("bps", "Bytes per second"),
		new Option("pps", "Packets per second"),
		new Option("bpp", "Bytes per packet"),
		new Option("first", "Timestamp of the first packet seen (in miliseconds)"),
		new Option("last", "Timestamp of the last packet seen (in miliseconds)"),
		new Option("received", "Timestamp regarding when the packet was received by collector"),
		new Option("duration", "Flow duration (in milliseconds)"),
	);
	
	$ARR_OPTIONS_ORDERBY_FIELDS= array(
		// Preffered
		new Option("first", "Timestamp of the first packet seen (in miliseconds)"),
		new Option("flows", "The number of flows (aggregated)"),
		new Option("packets", "The number of packets"),
		new Option("bps", "Bytes per second"),
		new Option("pps", "Packets per second"),
		new Option("bpp", "Bytes per packet"),
		new Option("bytes", "The number of bytes"),
		
		// Rest
		new Option("last", "Timestamp of the last packet seen (in miliseconds)"),
		new Option("received", "Timestamp regarding when the packet was received by collector"),
		new Option("duration", "Flow duration (in milliseconds)"),
		new Option("flags", "TCP flags"),
		new Option("srcip", "Source IP address"),
		new Option("dstip", "Destination IP address"),
		new Option("srcport", "Source port"),
		new Option("dstport", "Destination port"),
		new Option("proto",  "IP protocol"),
		new Option("port", "Source or destination port (pair field)"),
		new Option("ip", "Source or destination ip address (pair field)"),
	);
	
	$ARR_OPTIONS_FIELDSEL_FIELDS = array(
		// Preffered
		new Option("first", "Timestamp of the first packet seen (in miliseconds)"),
		new Option("last", "Timestamp of the last packet seen (in miliseconds)"),
		new Option("flows", "The number of flows (aggregated)"),
		new Option("packets", "The number of packets"),
		new Option("bytes", "The number of bytes"),
		new Option("srcip", "Source IP address"),
		new Option("dstip", "Destination IP address"),
		new Option("srcport", "Source port"),
		new Option("dstport", "Destination port"),
		new Option("flags", "TCP flags"),
		new Option("proto",  "IP protocol"),
		new Option("bps", "Bytes per second"),
		new Option("pps", "Packets per second"),
		new Option("bpp", "Bytes per packet"),
		
		// Rest
		new Option("received", "Timestamp regarding when the packet was received by collector"),
		new Option("duration", "Flow duration (in milliseconds)"),
		new Option("port", "Source or destination port (pair field)"),
		new Option("ip", "Source or destination ip address (pair field)"),
	);
	
	$ARR_OPTIONS_COMMON_FIELDS = array(
		// Weird
		new Option("outbytes", "The number of output bytes"),
		new Option("outpackets", "The number of output packets"),
		new Option("nextip", "IP next hop"),
		new Option("srcmask", "Source mask"),
		new Option("dstmask", "Destination mask"),
		new Option("tos", "Source type of service"),
		new Option("dsttos", "Destination type of service"),
		new Option("srcas", "Source AS number"),
		new Option("dstas", "Destination AS number"),
		new Option("nextas", "BGP Next AS"),
		new Option("prevas", "BGP Previous AS"),
		new Option("bgpnexthop", "BGP next hop"),
		new Option("srcvlan", "Source vlan label"),
		new Option("dstvlan", "Destination vlan label"),
		new Option("insrcmac", "In source MAC address"),
		new Option("outsrcmac", "Out source MAC address"),
		new Option("indstmac", "In destination MAC address"),
		new Option("outdstmac", "Out destination MAC address"),
		new Option("mpls", "MPLS labels"),
		new Option("inif", "SNMP input interface number"),
		new Option("outif", "SNMP output interface number"),
		new Option("dir", "Flow directions ingress/egress"),
		new Option("fwd", "Forwarding status"),
		new Option("routerip", "Exporting router IP"),
		new Option("engine-type", "Type of exporter"),
		new Option("engine-id", "Internal SysID of exporter"),
		new Option("eventtime", "NSEL The time that the flow was created"),
		new Option("connid", "NSEL An identifier of a unique flow for the device"),
		new Option("icmp-code", "NSEL ICMP code value"),
		new Option("icmp-type", "NSEL ICMP type value"),
		new Option("xevent", "NSEL Extended event cod"),
		new Option("xsrcip", "NSEL Mapped source IPv4 address"),
		new Option("xdstip", "NSEL Mapped destination IPv4 address"),
		new Option("xsrcport", "NSEL Mapped source port"),
		new Option("xdstport", "NSEL Mapped destination port"),
		new Option("iacl", "Hash value or ID of the ACL name"),
		new Option("iace", "Hash value or ID of the ACL name"),
		new Option("ixace", "Hash value or ID of an extended ACE configuration"),
		new Option("eacl", "Hash value or ID of the ACL name"),
		new Option("eace", "Hash value or ID of the ACL name"),
		new Option("exace", "Hash value or ID of an extended ACE configuration"),
		new Option("username", "NSEL username"),
		new Option("ingressvrfid", "NEL NAT ingress vrf id"),
		new Option("egressvrfid", "NAT event flag (always set to 1 by nfdump)"),
		new Option("eventflag", "NAT egress VRF ID"),
		new Option("blockstart", "NAT pool block start"),
		new Option("blockend", "NAT pool block end"),
		new Option("blockstep", "NAT pool block step"),
		new Option("blocksize", "NAT pool block size"),
		new Option("cl", "nprobe latency client_nw_delay_usec"),
		new Option("sl", "nprobe latency server_nw_delay_usec"),
		new Option("al", "nprobe latency appl_latency_usec"),
		new Option("event", "NSEL Extended event code"),
		new Option("ingressacl", "96 bit value including all items in ACL (iacl, iace, ixace)"),
		new Option("egressacl", "96 bit value including all items in ACL (eacl, eace, exace)"),
		new Option("inetfamily", "IENT family for src/dst IP address (ipv4 or ipv6); platform dependant"),
		new Option("exporterip", "Exporter IP address"),
		new Option("exporterid", "Exporter Observation Domain ID"),
		new Option("exporterversion", "Version of exporter"),
		new Option("samplermode", "Sampling mode"),
		new Option("samplerinterval", "Sampling interval"),
		new Option("samplerid", "Sampler ID assigned by exporting device"),
		
		// Discard
		new Option("pkts", "The number of packets"),
		new Option("outpkts", "The number of output packets"),
		new Option("nexthop", "IP next hop"),
		new Option("router", "Exporting router IP"),
		new Option("systype", "Type of exporter"),
		new Option("sysid", "Internal SysID of exporter"),
		new Option("icmpcode", "NSEL ICMP code value"),
		new Option("icmptype", "NSEL ICMP type value"),
		new Option("tcpflags", "TCP flags"),
		new Option("srcnet", "Source IP address"),
		new Option("dstnet", "Destination IP address"),
		new Option("brec1", "basic record 1"),
		new Option("as", "Source or destination ASn (pair field)"),
		new Option("if", "Input or output interface (pair field)"),
		new Option("vlan", "Source or destination vlan (pair field)"),
		new Option("net", "Source or destination ip address (pair field)"),
	);
	$ARR_OPTIONS_HINT_FIELDS = array();
	
?>
