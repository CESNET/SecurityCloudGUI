#include <iostream>
#include <cstring>

#include "RRDStats.hpp"

using std::cout;
using std::endl;
using std::string;

enum {ANY, TCP, UDP, ICMP, OTHER};

unsigned long RRDStats::protoToIndex(unsigned long proto) {
	if (proto == 1) {		// ICMP
		return ICMP;
	}
	else if (proto == 6) {	//TCP
		return TCP; 
	}
	else if (proto == 17) {	// UDP
		return UDP;
	}
	
	return OTHER;			// Default switch state - Other
}

void RRDStats::init() {
	for (unsigned int i = 0; i < 5; i++) {
		flows[i] = 0;
		pkts [i] = 0;
		bytes[i] = 0;
	}
	
	pktsMax = 0;
	bytesMax= 0;
}

void RRDStats::update(unsigned long inPkts, unsigned long inBytes, unsigned long inProto) {
	unsigned long i = protoToIndex(inProto);
	
	unsigned long aux[2] = {ANY, i};
	
	for (int i = 0; i < 2; i++) {
		flows[aux[i]] ++;
		pkts [aux[i]] += inPkts;
		bytes[aux[i]] += inBytes;
	}
	
	if (bytesMax < inBytes) bytesMax = inBytes;
	if (pktsMax  < inPkts)  pktsMax  = inPkts;
}

void RRDStats::printRRDcmd(string rrdfile, string nfcapd) {
	unsigned long pktsAvg  = pkts [ANY] / flows[ANY];
	unsigned long bytesAvg = bytes[ANY] / flows[ANY];

	// $nfcapd has format of nfcapd.YYYYMMDDHHMM
	// I don't need the 'nfcapd.' part
	string timestamp = nfcapd.substr(7, 12);	
	
	// Whole command will be - rrdtool update $rrdfile "timestamp@val1:val2:...:val19"
	cout << "rrdtool update " << rrdfile << " ";
	
	cout << "\"";	// Beginning of the data export
	
	// This will create a at-time specification in fmt HH:MM YYYYMMDD
	cout << timestamp.substr(8, 2) << ":" << timestamp.substr(10, 2) << " " << timestamp.substr(0, 4) << timestamp.substr(4, 2) << timestamp.substr(6, 2);
	
	for (int i = 0; i < 5; i++) {
		cout << (i == 0 ? "@" : ":") << flows[i];
	}
	
	for (int i = 0; i < 5; i++) {
		cout << ":" << pkts[i];
	}
	
	for (int i = 0; i < 5; i++) {
		cout << ":" << bytes[i];
	}
	
	cout << ":" << pktsMax;
	cout << ":"	<< pktsAvg;
	cout << ":" << bytesMax;
	cout << ":" << bytesAvg;
	
	cout << "\"";	// End of the data export
}

RRDStats::RRDStats() {
	init();
}

RRDStats::~RRDStats() {}