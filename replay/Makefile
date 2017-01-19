CC=g++
CFLAGS=-std=gnu++11 -Wall -Werror -Wextra -pedantic

all: cmprrd nftopath

RRDStats.o: RRDStats.cpp
	$(CC) $(CFLAGS) $^ -c

cmprrd: cmprrd.cpp RRDStats.o
	$(CC) $(CFLAGS) $^ -o $@
	
nftopath: nftopath.cpp
	$(CC) $(CFLAGS) $^ -o $@