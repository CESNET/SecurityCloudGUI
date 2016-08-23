Prerequisities:
	- rrdtool 1.6 (or 1.5 if it supports graph JSONTIME export format. 1.4 does not)
	- fdistdump
	- webserver with php5 or higher
	- both fdistdump and rrdtool must be in $PATH of apache user
	- ipfixcol with plugins: profiler, profile_stats (intermediate) and lnfstore (storage)
	
Recommended
	- mod_ssl and openssl for webserver
	
INSTALATION
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