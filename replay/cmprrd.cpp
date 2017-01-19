#include <iostream>
#include <cstring>
#include <regex>

#include "RRDStats.hpp"

using std::cout;
using std::cerr;
using std::string;
using std::endl;
using std::cin;

bool checkArgumets(int argc, char *argv[]) {
	std::regex rgx1 ("^.*\\.rrd$");
	std::regex rgx2 ("^nfcapd.[0-9]{12}$");
	
	if (
		argc != 3 || 
		!std::regex_match(argv[1], rgx1) || 
		!std::regex_match(argv[2], 
	rgx2)
	) {
		cerr << "Usage:\n";
		cerr << "cmprrd path/to/file.rrd nfcapd.YYYYMMDDHHMM";
		cerr << "\ncmprrd expects stream of data on the stdin in format:\n";
		cerr << "packets bytes protocol\n";
		return false;
	}
	
	return true;
}

int main(int argc, char *argv[]) {
	(void)argc;
	//if (!checkArgumets(argc, argv)) return 1;
	
	string rrdfile	(argv[1]);
	string nfcapd	(argv[2]);
	RRDStats		stats;
	
	unsigned long packets, bytes, proto;
	while (cin >> packets >> bytes >> proto) {
		stats.update(packets, bytes, proto);
	}
	stats.printRRDcmd(rrdfile, nfcapd);
	
	return 0;
}
