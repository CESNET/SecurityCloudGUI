<?php
	// Full path to the index.php file of the GUI
	$BASE_DIR			= "/var/www/html/";
	
	// Maximum of tabs for parallel fdistdump querries
	$MAX_TABS 			= 8;
	
	// This is the path to your MPI binary and launch
	// configuration. Also the path to the fdistdump
	// binary has to be provided
	$FDUMP				= "/usr/lib64/mpich/bin/mpiexec -n 2 fdistdump";
	
	// Length of the userstamp identifying the
	// transactions. The userstamp is a combination
	// of symbols respecting the regex: [a-zA-Z0-9]
	$USERSTAMP_LENGTH	= 16;
	
	// Folder for storing transactions of the GUI.
	// User apache needs privileges to write into this
	// folder.
	$TMP_DIR			= "/tmp/scgui/";
	
	$IPFIXCOL_DATA		= "/data/";
	
	// Path to the ipfixcol profile configuration
	// file.
	$IPFIXCOL_CFG		= $IPFIXCOL_DATA."profiles.xml";
	
	// Path to the pidfile of the ipfixcol. This file has
	// to exist (and has to be valid) in order to reconfigure
	// collector on the run as the users create new profiles
	// in the gui.
	// ipfixcol is updated by sending SIGUSR1 to it's running process
	$PIDFILE			= $IPFIXCOL_DATA."pidfile.txt";
?>
