Depencies
	- apache
	- php5+
	- rrdtool
	- fdistdump

Instalation guide:
	1) Copy secloud-web to your 'html' folder
	2) chmod -R a+rX secloud-web
	3) cd secloud-web/php
	4) chmod a+w transactions
	5) (still in 'php' subfolder) make a symbolic link to the folder containing profiles data (for example /data)
		5.1) now we can go to php/data/profile-name
		5.2) if the folder is named other than data, you need to change it's name in config.php $IPFIX_FOLDER
		5.3) chmod -R a+w data
	6) make sure that all nfcapd and rrd files have read permissions for all
	
Why to set up permissions as described above:
	- Transactions: Users have their unique IDs and file mutex locks managed in this folder. By managing I mean create, read, update and delete. Your web server needs permissions to do this.
	- Data: Webpage generate graphs from the rrd files. It also needs a temporary image file to store these graphs before sending them to the webpage. That's why it needs to be able to write into all profile subfolders. It also needs to fetch the data and without read rights on the rrds (nfcapds) it would be impossible.
	
Adding a profile:
*** Profile name in this case is 'example' ***
***	Sources are: smtp, pop3, imap, ssh, http *
***	Profile name must not contain spaces   ***
	1) Go to 'php/profiles' and open 'profile.list'
	2) Add a new line stating: 'example example.profile' (without quotes). Save and close the file.
	3) Create a new file 'example.profile', open it as text
	4) Write multiple lines in format '[source-name] [hex-color]' (without quotes). NOTE: Use precisely 1 space between name and color. Not tab. Also note that hex-color is without initial '#'
	5) File looks like this:
http FF0000
imap 00FF00
pop3 0000FF
smpt FFFF00
ssh FF00FF
	6) Save and close
	7) Go to 'php/data'
	8) Create folder 'example'
	9) Make your ipfixcol save it's rrd into this folder. RRD's must be named: http.rrd imap.rrd pop3.rrd smtp.rrd ssh.rrd. Make sure that rrd's are not in any subfolders
	10) Now navigate to http://your-server/secloud-web/index.php?profile=example