<?php
	/* =========== */
	/* ENVIRONMENT */
	/* =========== */
	// Change this to true if you're running the whole SecurityCloud on the single machine. ($FDUMP will be called instead of $FDUMP_HA)
	$SINGLE_MACHINE = true;

	// Change this to true if this instance of GUI is supposed to analyze historical data
	$HISTORIC_DATA = false;

	// Maximum of tabs for parallel fdistdump querries
	$MAX_TABS = 8;

	// Length of the userstamp identifying the transactions. The userstamp is a combination of symbols respecting the regex: [a-zA-Z0-9]
	$USERSTAMP_LENGTH = 16;

	/* =========== */
	/* EXECUTABLES */
	/* =========== */
	// use absolute paths or command names (relative to the PATH env. var.)
	$FDISTDUMP_CMD		= '/usr/lib64/mpich/bin/fdistdump_mpich';
	$FDISTDUMP_HA_CMD	= 'fdistdump-ha';
	$MPIEXEC_CMD		= '/usr/lib64/mpich/bin/mpiexec';
	$MPIEXEC_ARGS		= '-n 2';
	$RRDTOOL_CMD		= '/opt/rrdtool-1.7.0-no-mmap/bin/rrdtool';

	/* =========== */
	/* DIRECTORIES */
	/* =========== */
	// Absolute path to the index.php file of the GUI
	$BASE_DIR = '/var/www/html/';

	// Names of slave nodes in distributed environment
	// Do not omit final slashes
	$SLAVE_HOSTNAMES = array ('slave1/', 'slave2/', 'slave3/');

	// Folder for storing transactions of the GUI.
	// User apache needs privileges to write into this
	// folder.
	$TMP_DIR = '/tmp/scgui/';

	// Path to folder where query and graph data will be stored (live folder will be there)
	$IPFIXCOL_DATA = '/data/';

	// Path to the IPFIXcol profile configuration file
	$IPFIXCOL_CFG = $IPFIXCOL_DATA.'profiles.xml';

	// Path to the pidfile of IPFIXcol. This file has
	// to exist (and has to be valid) in order to reconfigure
	// collector on the run as the users create new profiles
	// in the gui.
	// IPFIXcol is updated by sending SIGUSR1 to it's running process
	$PIDFILE = $IPFIXCOL_DATA.'pidfile.txt';

	// In distributed environment, IPFIXcol configuration reload is handled by creation of this file
	$IPFIXCOL_UPDATE_FILE = $IPFIXCOL_DATA.'updatecfg';
?>
