#!/bin/bash

# Modify the following variables to suit your situation
RRDTOOL="/opt/rrdtool/bin/rrdtool"
MPI_MODULE="mpi/mpich-x86_64"
CORE_DIR="/data/live"

# CMD arguments
REPLAY_FOLDER="$1"
DATA_NAME="$2"

# Prepare for work
module load $MPI_MODULE
$RRDTOOL restore template.xml template.rrd --force-overwrite
$RRDTOOL create $DATA_NAME.rrd --template template.rrd --start 723168000

# Loop nfcapd files, read them, compute stats and save them to rrd
for x in $(find "$REPLAY_FOLDER" -type f | egrep 'nfcapd')
do
	crop=$(echo "$x" | sed -r 's/^.*\///')
	cmd=$(mpiexec -n 2 fdistdump --output-format=csv --fields=pkts,bytes,proto --output-volume-conv=none $x | tail -n +2 | tr ',' ' ' | ./cmprrd $DATA_NAME.rrd $crop)
	echo $cmd
	eval $cmd
	
	# Move the nfcapd file where it belongs
	npath=$(./nftopath $crop)
	mkdir -p "$CORE_DIR"/"$DATA_NAME"/"$npath"
	
	cp $x "$CORE_DIR"/"$DATA_NAME"/"$npath"/$crop #cp or mv?
done

mkdir -p "$CORE_DIR"/"$DATA_NAME"/"rrd"/"channels"
mv $DATA_NAME.rrd "$CORE_DIR"/"$DATA_NAME"/"rrd"/"channels"