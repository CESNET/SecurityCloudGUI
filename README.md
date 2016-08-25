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
- pkill

## Instalation
Note this is case of the default installation that features almost zero configuration in GUIs config.php.

First off, install all neccessary prerequisities, then grab all of GUIs core files:
```git clone https://github.com/CESNET/SecurityCloudGUI
cd web
cp -r * /var/www/html
mkdir /data
cd ../configs
cp * /data
```

Now make sure that html folder has read permissions for everybody and write permissions for ipfixcol config file. If not:
```cd /var/www
chmod -R o+rX html
chmod -R g+rw /data
```

Create a new group:
```groupadd ipfixcol
usermod root -a -G ipfixcol
usermod apache -a -G ipfixcol
chown -R :ipfixcol /data
```

Now the apache will be able to read and write the /data folder where it has the profiles configuration and where the flow data will be stored. At this point you can start the collector:
```su apache -c "ipfixcol -c /data/startup.xml -p /data/pidfile.txt -d -v2 >/data/stdout.txt 2>/data/stderr.txt"
```

If the command does not work, you have to edit the /etc/passwd and change apache login shell from /bin/nologin to /bin/bash or /bin/sh or whatever you use.


This line will allow apache to send the very specific "Update your configuration" signal to the currently running ipfixcol daemon. It basically gives sudo (without asking for passwords) to apache for calling the pkill allowing only a single signal and only in case the root was the user who launched the ipfixcol, so it won't break anything.

At this point, if your webserver is set up properly, the GUI should work.
**NOTE**: If your MPI binary is not located in /usr/lib64/mpich/bin/mpiexec **OR** fdistdump is not in your $PATH **OR** rrdtool 1.6 is not in your $PATH, go to /var/www/html/php/config.php and put the correct paths in there. You can also modify some GUI settings and folders in case you don't want to have your web precisely in /var/www/html or you need more/less tabs for fdistdump querries.
