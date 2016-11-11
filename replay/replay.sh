#!/bin/bash

# cmprrd and nftopath *must* be compiled

# Modify the following variables to suit your situation
RRDTOOL="/opt/rrdtool/bin/rrdtool"
MPI_MODULE="mpi/mpich-x86_64"
CORE_DIR="/data/live/"

# CMD arguments
REPLAY_FOLDER="$1"
DATA_NAME="$2"

if [[ $# -lt 2 ]]
then
	echo -e "Usage: replay folder_with_replay_data /path/to/dst/file.rrd\n"
elif [[ $DATA_NAME == "--help" ]]
then
	echo -e "Usage: /path/to/replay/folder profile_name\n"
fi

# Prepare for work
module load $MPI_MODULE
$RRDTOOL restore template.xml template.rrd --force-overwrite
$RRDTOOL create $DATA_NAME.rrd --template template.rrd --start 723168000

# Loop nfcapd files, read them, compute stats and save them to rrd
for x in $(find "$REPLAY_FOLDER" -type f | egrep 'nfcapd')
do
	crop=$(echo "$x" | sed -r 's/^.*\///')
	cmd=$(mpiexec -n 2 fdistdump --output-format=csv --fields=pkts,bytes,proto --output-volume-conv=none $x | tail -n +2 | tr ',' ' ' | ./cmprrd $DATA_NAME.rrd $crop)
	eval $cmd
	
	# Move the nfcapd file where it belongs
	npath=$(nftopath $crop)
	mkdir -p "$CORE_DIR"/"$DATA_NAME"/"$npath"
	
	cp $x "$CORE_DIR"/"$DATA_NAME"/"$npath"/$crop #cp or mv?
done
