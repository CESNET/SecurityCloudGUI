# SecurityCloud GUI
## Table of Contents
1. [Introduction](#intro)
2. [Screenshots](#screen)
3. [Prerequisities](#pre)
4. [Instalation](#install)
5. [Analyzing historical data](#historic)
6. [Troubleshooting](#trouble)
7. [Todolist](#todo)

## <a name="intro"></a> Introduction
This web application is a part of the SecurityCloud project. Like nfsen or flowmon, this GUI allows 
you to visualize and analyze your internet flows collected by the ipfix collector.

The GUI also allows you to perform queries on the flow data using the fdistdump and also to organize
and manage export profiles of the ipfixcol.

## <a name="screen"></a> Screenshots
### Workbench tab
#### Overview
![Overview](/screens/overview.png)

#### Graphs section
![Graphs](/screens/graph.png)

#### Statistics section
![Statistics](/screens/stats.png)

#### Database queries section
![Database query](/screens/dbqry.png)

### Profile management tab
![Profiles](/screens/profiles.png)


## <a name="pre"></a> Prerequisities:
- [rrdtool 1.6+](http://oss.oetiker.ch/rrdtool/pub/?M=D) (must be in $PATH for default installation)
- [ipfixcol](https://github.com/CESNET/ipfixcol/) with the following 3rd party plugins:
	* profile_stats (intermediate)
	* profiler (intermediate)
	* lnfstore (storage)
- [fdistdump](https://github.com/CESNET/fdistdump)
- webserver with php5 or higher

## <a name="install"></a> Instalation
**NOTE:** These installation notes work for the default configuration on CentOS 7. For custom 
configuration see [Troubleshooting & Advanced installation](#trouble).

After installing all prerequisities, clone the Github repo and copy all relevant data to the folders
as shown below:
```
git clone https://github.com/CESNET/SecurityCloudGUI
cd SecurityCloudGUI/web
cp -r * /var/www/html
mkdir /data
cd ../ipfix_configuration
cp * /data
```

The '/data' folder is the place where the flow data will be stored. Files copied from the
'ipfix_configuration' are the default settings for profiles and plugins of ipfixcol.

Now make sure that 'html' folder has read permissions for everybody. If not do:
```
chmod -R o+rX /var/www/html
```

Now you should set up permissions for the '/data' folder. I recommend to create a group for users who
should have the direct access to this directory:
```
groupadd ipfixcol
usermod root -a -G ipfixcol
usermod apache -a -G ipfixcol
chown -R :ipfixcol /data
chmod -R a-rwxX /data
chmod -R g+rwX /data
```

Now the apache will be able to read and write the '/data' folder where it has the 'profiles.xml'
configuration and where the flow data will be stored. At this point you can start the collector. The
collector **has** to be launched under the apache user, otherwise ipfixcol configuration won't be
automatically updated when the new profile is added or when another gets removed:
```
su apache --shell "/bin/bash" -c "ipfixcol -c /data/startup.xml -v 2 -p /data/pidfile.txt -d"
```

One last thing before you can actually boot up the GUI. User can store their filter expression for
later use. This file is identified by config value FILTER_STORAGE_PATH. If this points to location
where apache has RW permissions, the filter save and loading will work just fine. If you wish
to have file with stored filters somewhere else, please create that file and give it proper
permissions and ownership. Filter storage file has a very basic CSV structure and thus can be easily edited by hand if the
neccessity arises.

## <a name="historic"></a> Analyzing historical data
It may happen to you that you only have a bunch of nfdump files and you want to analyze them with
the help of the GUI. This can be done. The following lines are a complete guide to importing
historical data to the GUI. First and foremost please note that GUI was not originally meant for
this and the process of importing is clunky at best. You also need a separate instance of the GUI
for analyzing the historical data. Mixing it with ipfixcol and live traffic is a highway to hell.

### Preparations
Let's say you have a clean instance of SCGUI with properly set 'config.php'. At this point there's
one additional value to be set in the config. You have to set variable '$HISTORIC_DATA' to true to
enable some timestamp corrections. For purpose of this tutorial I suppose that '$IPFIXCOL_DATA' is
set to '/data'. You also need some data for processing. This tutorial uses folder
'~/shared/nfcapd-replay'. Within this folder there are three subfolders: 'pop3', 'smtp' and 'imap'.
Internal structure of these subfolders is not important. Names of these three subfolders will define
names of channels of a resulting SCGui profile. The final prerequisition is a 'replay' folder from
this repo. This can be placed anywhere as it only contains scripts and templates.

### Configure replay script
Open the 'replay' folder and edit the 'replay' script in a text editor. At the start of the script
there are three variables that needs to be configured prior to data import. Value of the variable
'RRDTOOL' has to be the same as in the 'config.php'. Variable 'MPI_MODULE' specifies the module you
use for executing fdistdump. I use mpich (version 3.2) so for me it would have value
'mpi/mpich-3.2-x86_64'. You can find out how the modules are named by executing command
'module avails'. Last line of the output lists installed modules. Last variable to be set is the
'DATA_DIR'. It's the location of data folder for the live profile. If your '$IPFIXCOL_DATA' variable
in 'config.php' is set to '/data', 'DATA_DIR' will be set to '/data/live'. You save and close the file.

![Configuring replay script](/screens/r-tutor.png)

### Run the scripts
At this point the processing can commence. Open shell in the 'replay' folder and execute following
command:
```
./replay.sh ~/share/nfcapd-replay demo
```

First argument of the script specifies location of data of all channels within a profile. Second
argument specifies name of a resulting profile. After script finishes, follow the instructions and
explore the newly created profile.

### Rinse and repeat
At this point if you wish to import new data, you just have to create a new profile and then only
execute 'replay.sh' script.

## <a name="trouble"></a> Troubleshooting & Advanced Installation
For many reasons the default configuration might failed or you just want to put the data somewhere else. In that case, consult this part of the guide.
Most of your issues can be solved via editing the 'config.php' which is located in the 'php' folder of the GUI root.

### fdistdump queries do not start
You either have a different mpi binary than the default configuration uses or fdistdump is not in your path. Open the 'config.php' and edit the value of $FDUMP variable. If you have a different mpi, replace the full path to the mpiexec from mpich with full path to your alternative to mpiexec. If you don't have fdistdump in your $PATH, enter the full path to its binary. Also check the variable $SINGLE_MACHINE. If you intend to run fdistdump on a cluster using fdistdump-ha, it has to be set to false.

### My webserver does not use /var/www/html
Copy the contents of the web wherever your webserver wants it to be. Then open the 'config.php' and edit the $BASE_DIR variable with the correct path. **Do not** omit the final slash.

### I want to save my flow data somewhere else
In that case, create your flow data folder wherever you want, but don't forget to set up groups and permissions properly. Then, you need to edit the 'profiles.xml' and rewrite all directory paths to reflect the folder you want to use. You also need to change the directory to 'profiles.xml' listed in 'startup.xml'.

After you have that, open 'config.php' and edit variables $IPFIXCOL_DATA (**do not** omit final slash) and $IPFIXCOL_CFG to reflect the new paths. If you want to have the ipfixcol pidfile somewhere else (it is neccessary for updating the profiles configuration on the fly), edit also the $PIDFILE variable.

### The graph is blank / broken
If you just opened the GUI or changed profile, then your currently selected profile has not its data accessible to the apache user. Either the permissions were misset or the 'config.php' is pointing to the wrong directory (did you set the $IPFIXCOL_CFG and $IPFIXCOL_DATA properly? Did you add the final slash?).

Alternatively, you have the GUI opened for a while, you've switched some tabs and returned to the graph and it's blank now. You've probably resized the browser window earlier. To fix this you can resize the window again or reload the page. This problem is due to the dygraphs library used to render the graph that requires to not hide the active graph which is precisely what I need to do when changing tabs. **NOTE:** this issue has been at least partially fixed. See issue #7 for details.

Even alternatively, your rrdtool is not in your $PATH or it is not in version which supports '-a JSONTIME' export format. If the former is true, edit the $RRDTOOL variable in config.php accordingly. If the latter is true, you'll most probably will have to install rrdtool from the [source code](http://oss.oetiker.ch/rrdtool/pub/?M=D).

### fdistdump query cannot be killed
Most probably you did request a lot of data that were processed quickly by the fdistdump and send to PHP which is currently struggling to process it and send it to the GUI. Querries can only be killed if the job is still performed by the fdistdump, at the PHP level you just have to wait.

### Transaction files could not be created
You've probably changed the $TMP_DIR to the place where apache does not have read+write access. You have no reason to change this variable from the original '/tmp/scgui/' and I recommend you to keep it that way.

### Graph is buggy/is not responding
Do not be hasty when using the graph. Whatever you do, the request is send to the server, it gets the data and sends it back to you where it has to be processed. It takes some time and spamming these requests results in mixing them up and the graph will stop responding. Reload the page.

### I cannot delete the live profile
Please, do read the disclaimer before deleting the live profile. The live profile cannot be deleted and you have to edit it manually in the text editor. Deleting it from the GUI will only result in deletion of its children.
