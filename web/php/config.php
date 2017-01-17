<?php
	// Full path to the index.php file of the GUI
	$BASE_DIR			= '/var/www/html/';
	
	// Maximum of tabs for parallel fdistdump querries
	$MAX_TABS 			= 8;
	
	// This is the path to your MPI binary and launch
	// configuration. Also the path to the fdistdump
	// binary has to be provided
	$FDUMP				= 'mpiexec -n 2 fdistdump';
	$FDUMP_HA			= 'fdistdump-ha';
	$FDUMP_ENV			= array('PATH' => '/usr/lib64/mpich/bin:/usr/local/bin:/bin:/usr/local/sbin:/usr/bin:/usr/sbin');
	$SINGLE_MACHINE		= true;	// Change this to true if you're running the whole SecurityCloud on the single machine. ($FDUMP will be called instead of $FDUMP_HA)
	
	// Time settings
	$USE_LOCAL_TIME		= true; // Will use UTC if set to false
	
	// RRD Tool
	$RRDTOOL			= 'rrdtool';
	
	// Length of the userstamp identifying the
	// transactions. The userstamp is a combination
	// of symbols respecting the regex: [a-zA-Z0-9]
	$USERSTAMP_LENGTH	= 16;
	
	// Folder for storing transactions of the GUI.
	// User apache needs privileges to write into this
	// folder.
	$TMP_DIR			= '/tmp/scgui/';
	
	$IPFIXCOL_DATA		= '/data/';
	
	// Path to the ipfixcol profile configuration
	// file.
	$IPFIXCOL_CFG		= $IPFIXCOL_DATA.'profiles.xml';
	
	// Path to the pidfile of the ipfixcol. This file has
	// to exist (and has to be valid) in order to reconfigure
	// collector on the run as the users create new profiles
	// in the gui.
	// ipfixcol is updated by sending SIGUSR1 to it's running process
	$PIDFILE			= $IPFIXCOL_DATA.'pidfile.txt';
?>
