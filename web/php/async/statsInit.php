<?php
// *** include ***
include '../config.php';													// Grab some constants from the config
include '../misc/profileClass.php';
include '../misc/profileMethods.php';

// *** get url params ***
$time	= $_GET['time']; // < this parameter needs to be changed
$src	= $_GET['src'];
$timeSplit = explode(':', $time);

/* CREATE A MASTER TREE FROM XML */
$TREE_PROFILE = new Profile();												// Full tree of profiles
createProfileTreeFromXml(loadXmlFile($IPFIXCOL_CFG), '/', $TREE_PROFILE);	// Fill it with ALL necessary data

/* COLLECT USER-AVAILABLE PROFILES, COLLECT USER-SELECTED PROFILE AND VERIFY IT */
$ARR_AVAILS = getAvailableProfiles('me');
$profile = getCurrentProfile();
if (!verifySelectedProfile($profile, $ARR_AVAILS)) {
	echo "You don't have the privileges to access the profile $profile<br>";
	exit(1);
}

unset($ARR_AVAILS);

/* SEARCH FOR SELECTED SUBPROFILE ROOT */
$aux = null;
searchForProfile($TREE_PROFILE, $profile, $aux);
if ($aux == null) {
	echo "The profile $aux does not exist<br>";
	exit(2);
}

if (sizeof($timeSplit) == 2) {
	$time = "-s $timeSplit[0] -e $timeSplit[1]";
}
else {
	$time = "-s $timeSplit[0] -e $timeSplit[0]";
}
$time = escapeshellcmd($time);

// *** PROC OPEN DESCRIPTORS ***
$desc = array(
	0 => array ('pipe', 'r'),
	1 => array ('pipe', 'w'),
	2 => array ('pipe', 'w')
);
$pipes = array();

// *** DATA ARRAYS ***
$names = array("Flows", "Packets", "Bytes");
$types = array("Rate", "Sum");
$chnl = explode(':', $src);
$chnlSize = (int)sizeof($chnl);
?>