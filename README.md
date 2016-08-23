# SecurityCloud GUI
## Introduction
This web application is a part of the SecurityCloud project. Like nfsen or flowmon, this GUI allows you to visualize and analyze your internet flows collected by the ipfix collector.

The GUI also allows you to do easy querries on the data using the fdistdump and also to organize and manage export profiles of the ipfixcol.

## Prerequisities:
- rrdtool 1.6
- [ipfixcol]() with the following 3rd party plugins:
	> profile_stats (intermediate)
	> profiler (intermediate)
	> lnfstore (storage)
- [fdistdump]()
- webserver with php5 or higher
- rrdtool **must** be in the $PATH of the apache user

## Instalation
1) Copy all files to your webserver folder (example: '/var/www/html/')
2) Set owner of these files to root
3) All files should have readonly permissions for nonroot users
4) Go to 'php/misc/' and set write permissions on transaction folder
	NOTE: You can place this folder to a different location. Don't
	forget to check the 'php/config.php' for variable $TRANS_FOLDER
	and set it accordingly to you transaction folder location. The
	first '/' is a root of your webserver ('/var/www/html/') not of
	your filesystem.
	
5) Go to 'data/' and edit the 'profiles.xml' to fit your profilling
needs.
	NOTE: You *must* respect the file hierarchy. Every profile is a
	child of 'live' profile (or child of child of child...) in both
	the profile structure and folder hierarchy.
	For example the profile "incoming" which is child of "emails"
	that is child of "live" must be placed in 'data/live/emails/incoming'.
	If it's not, the GUI won't be able to load the data.
	
6) Edit the 'config.php'. Check the location of 'transactions' folder,
also verify the $FDUMP variable for location of you mpiexec program
and it's launch configuration.