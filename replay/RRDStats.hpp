#ifndef RRDSTATS_H_
#define RRDSTATS_H_

class RRDStats {
private:
	unsigned long flows[5];
	unsigned long pkts[5];
	unsigned long bytes[5];
	unsigned long pktsMax, bytesMax;
	
	unsigned long	protoToIndex(unsigned long proto);

public:
	void init();
	void update(unsigned long inPkts, unsigned long inBytes, unsigned long inProto);
	void printRRDcmd(std::string rrdfile, std::string nfcapd);
	
	RRDStats();
	~RRDStats();
};

#endif
