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
	// How fdistdump will be executed with $SINGLE_MACHINE set to true. Path to mpiexec is specified in $FDUMP_ENV
	$FDUMP = 'mpiexec -n 2 fdistdump';
	
	// How fdistdump will be executed in distributed environment
	$FDUMP_HA = 'fdistdump-ha';
	
	// $PATH contents present when executing fdistdump. Make sure path to mpiexec is present here
	$FDUMP_ENV = array('PATH' => '/usr/lib64/mpich/bin:/usr/local/bin:/bin:/usr/local/sbin:/usr/bin:/usr/sbin');
	
	// How rrdtool will be executed. If it is not in $PATH, enter it's path here
	$RRDTOOL = 'rrdtool';
	
	/* =========== */
	/* DIRECTORIES */
	/* =========== */
	// Full path to the index.php file of the GUI
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
	
	// Path to the ipfixcol profile configuration file
	$IPFIXCOL_CFG = $IPFIXCOL_DATA.'profiles.xml';
	
	// Path to the pidfile of the ipfixcol. This file has
	// to exist (and has to be valid) in order to reconfigure
	// collector on the run as the users create new profiles
	// in the gui.
	// ipfixcol is updated by sending SIGUSR1 to it's running process
	$PIDFILE = $IPFIXCOL_DATA.'pidfile.txt';
	
	// In distributed environment, ipfixcol configuration reload is handled by creation of this file
	$IPFIXCOL_UPDATE_FILE = $IPFIXCOL_DATA.'updatecfg';
?>
