# SecurityCloud GUI
## Table of Contents
1. [Introduction](#intro)
2. [Prerequisities](#pre)
3. [Instalation](#install)
4. [Troubleshooting](#trouble)
5. [Todolist](#todo)

##<a name="intro"></a> Introduction
This web application is a part of the SecurityCloud project. Like nfsen or flowmon, this GUI allows you to visualize and analyze your internet flows collected by the ipfix collector.

The GUI also allows you to perform querries on the flow data using the fdistdump and also to organize and manage export profiles of the ipfixcol.

##<a name="pre"></a> Prerequisities:
- [rrdtool 1.6](http://oss.oetiker.ch/rrdtool/pub/?M=D) (must be in $PATH for default installation)
- [ipfixcol](https://github.com/CESNET/ipfixcol/) with the following 3rd party plugins:
	* profile_stats (intermediate)
	* profiler (intermediate)
	* lnfstore (storage)
- [fdistdump](https://github.com/CESNET/fdistdump)
- webserver with php5 or higher

##<a name="install"></a> Instalation
**NOTE:** These installation notes work for the default configuration on CentOS 7. For custom configuration see [Troubleshooting & Advanced installation](#trouble).

After installing all prerequisities, clone the Github repo and copy all relevant data to the folders as shown below:
```
git clone https://github.com/CESNET/SecurityCloudGUI
cd SecurityCloudGUI/web
cp -r * /var/www/html
mkdir /data
cd ../ipfix_configuration
cp * /data
```

The '/data' folder is the place where the flow data will be stored. Files copied from the 'ipfix_configuration' are the default settings for profiles and plugins of ipfixcol.

Now make sure that 'html' folder has read permissions for everybody. If not do:
```
chmod -R o+rX /var/www/html
```

Now you should set up permissions for the '/data' folder. I recommend to create a group for users who should have the direct access to this directory:
```
groupadd ipfixcol
usermod root -a -G ipfixcol
usermod apache -a -G ipfixcol
chown -R :ipfixcol /data
chmod -R a-rwxX /data
chmod -R g+rwX /data
```

Now the apache will be able to read and write the '/data' folder where it has the 'profiles.xml' configuration and where the flow data will be stored. At this point you can start the collector. The collector **has** to be launched under the apache user, otherwise ipfixcol configuration won't be automatically updated when the new profile is added or when another gets removed:
```
su apache --shell "/bin/bash" -c "ipfixcol -c /data/startup.xml -v 2 -p /data/pidfile.txt -d"
```

At this point, if your webserver is set up properly, the GUI should work.

##<a name="trouble"></a> Troubleshooting & Advanced Installation
For many reasons the default configuration might failed or you just want to put the data somewhere else. In that case, consult this part of the guide.
Most of your issues can be solved via editing the 'config.php' which is located in the 'php' folder of the GUI root.

### fdistdump querries do not start
You either have a different mpi binary than the default configuration uses or fdistdump is not in your path. Open the 'config.php' and edit the value of $FDUMP variable. If you have a different mpi, replace the full path to the mpiexec from mpich with full path to your alternative to mpiexec. If you don't have fdistdump in your $PATH, enter the full path to its binary.

### My webserver does not use /var/www/html
Copy the contents of the web wherever your webserver wants it to be. Then open the 'config.php' and edit the $BASE_DIR variable with the correct path. **Do not** omit the final slash.

### I want to save my flow data somewhere else
In that case, create your flow data folder wherever you want, but don't forget to set up groups and permissions properly. Then, you need to edit the 'profiles.xml' and rewrite all directory paths to reflect the folder you want to use. You also need to change the directory to 'profiles.xml' listed in 'startup.xml'.

After you have that, open 'config.php' and edit variables $IPFIXCOL_DATA (**do not** omit final slash) and $IPFIXCOL_CFG to reflect the new paths. If you want to have the ipfixcol pidfile somewhere else (it is neccessary for updating the profiles configuration on the fly), edit also the $PIDFILE variable.

### The graph is blank / broken
If you just opened the GUI or changed profile, then your currently selected profile has not its data accessible to the apache user. Either the permissions were misset or the 'config.php' is pointing to the wrong directory (did you set the $IPFIXCOL_CFG and $IPFIXCOL_DATA properly? Did you add the final slash?).

Alternatively, you have the GUI opened for a while, you've switched some tabs and returned to the graph and it's blank now. You've probably resized the browser window earlier. To fix this you can resize the window again or reload the page. This problem is due to the dygraphs library used to render the graph that requires to not hide the active graph which is precisely what I need to do when changing tabs.

Even alternatively, your rrdtool is not in your $PATH or it is not in version which supports '-a JSONTIME' export format. If the former is true, edit the $RRDTOOL variable in config.php accordingly. If the latter is true, you'll most probably will have to install rrdtool from the [source code](http://oss.oetiker.ch/rrdtool/pub/?M=D).

### fdistdump querry cannot be killed
Most probably you did request a lot of data that were processed quickly by the fdistdump and send to PHP which is currently struggling to process it and send it to the GUI. Querries can only be killed if the job is still performed by the fdistdump, at the PHP level you just have to wait.

### Transaction files could not be created
You've probably changed the $TMP_DIR to the place where apache does not have read+write access. You have no reason to change this variable from the original '/tmp/scgui/' and I recommend you to keep it that way.

### Graph is buggy/is not responding
Do not be hasty when using the graph. Whatever you do, the request is send to the server, it gets the data and sends it back to you where it has to be processed. It takes some time and spamming these requests results in mixing them up and the graph will stop responding. Reload the page.

### I cannot delete the live profile
Please, do read the disclaimer before deleting the live profile. The live profile cannot be deleted and you have to edit it manually in the text editor. Deleting it from the GUI will only result in deletion of its children.

### I want to restrict selected profiles for selected users only
This feature is currently in development.

### Selecting sources in the fdistdump has no effect
Correct, it hasn't. Ipfixcol currently cannot export separate channels. For the same reason the statistics are only for all channels together.

### Shadow profiles don't work
Correct, they don't. But they will.

##<a name="todo"></a> Todolist
Following features are expected to be implemented in the future:

* shadow profiles
* channel selection in fdistdump querries (currently the selection has no effect)
* user control and profile restriction