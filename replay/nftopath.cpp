#include <iostream>
#include <cstring>

using std::cout;
using std::cerr;
using std::endl;
using std::string;

int main(int argc, char *argv[]) {
	if (argc != 2) {
		cerr << "Usage nftopath nfcapd_file\n";
		return 1;
	}
	
	string arg(argv[1]);
	string translate = arg.substr(7, 12);
	
	cout << translate.substr(0, 4) << "/" << translate.substr(4, 2) << "/" << translate.substr(6, 2) << endl;
	
	return 0;
}