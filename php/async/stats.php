<?php
// *** include ***
include "../config.php";													// Grab some constants from the config
include "../misc/profileClass.php";
include "../misc/profileMethods.php";

// *** get url params ***
$time	= $_GET["time"];
$src	= $_GET["src"];

/* CREATE A MASTER TREE FROM XML */
$TREE_PROFILE = new Profile();												// Full tree of profiles
createProfileTreeFromXml(loadXmlFile("../../data/profiles.xml"), "/", $TREE_PROFILE);	// Fill it with ALL necessary data

/* COLLECT USER-AVAILABLE PROFILES, COLLECT USER-SELECTED PROFILE AND VERIFY IT */
$ARR_AVAILS = getAvailableProfiles("me");
$profile = getCurrentProfile();
if (!verifySelectedProfile($profile, $ARR_AVAILS)) {
	echo "You don't have the privileges to access the profile $profile<br>";
	exit(1);
}

/* SEARCH FOR SELECTED SUBPROFILE ROOT */
$aux = null;
searchForProfile($TREE_PROFILE, $profile, $aux);
if ($aux == null) {
	echo "The profile $aux does not exist<br>";
	exit(2);
}

if ($aux->getShadow()) {
	echo "<div class='panel panel-danger'>";
	echo "<div class='panel-heading'>Shadow profile</div>";
	echo "<div class='panel-body'>This profile is a shadow profile, no metadata for statistics are available.</div>";
	echo "</div>";
	exit(3);
}

// *** proc_open params ***
$cmd = "exec $FDUMP $time --output-items=m --output-format=csv --output-volume-conv=metric-prefix $src | tail -n 3 | tr '\n' ','";	// <---------- $CMD --------------
$desc = array(
	0 => array ('pipe', 'r'),
	1 => array ('pipe', 'w'),
	2 => array ('pipe', 'w')
);
$pipes = array();

$p = proc_open($cmd, $desc, $pipes, "../../data$profile/");				// Execute the program command
if($p == false) {														// If execution failed (
	echo "<div class='panel panel-danger'>";							// Print this *very* serious error
	echo "<div class='panel-heading'>Error</div>";
	echo "<div class='panel-body'>proc_exec() failed. Enable PHP error/warning logging to see what's the matter.</div>";
	echo "</div>";
	exit(4);															// And end this thread )
}

$buffer = "";															// Stdout buffer
if(is_resource($p)) {												
	while($f = fgets($pipes[1])) {										// While any stdout is inbound
		$buffer .= $f;													// Buffer it
	}
}

$ptime = explode("#", substr($time, 3));
$stats = explode(",", $buffer);

echo "<div class='panel panel-info'>";
echo "<div class='panel-heading'>";
echo "Statistics for: ";
echo date("d M Y H:i",$ptime[0]);
if (sizeof($ptime) > 1) {
	echo " - ".date("d M Y H:i",$ptime[1]);
}
echo "</div></div>";

echo "<div class='row'>";
for($i = 0; $i < 3; $i++) {
	echo "<div class='col-md-4'>";
	echo "<div class='panel panel-info'>";
	echo "<div class='panel-heading'>".$stats[$i * 6]."</div>";
	echo "<div class='panel-body'>";
	echo "<table class='table table-striped table-condensed'>";
	echo "<thead><tr><th>All</th><th>TCP</th><th>UDP</th><th>ICMP</th><th>Other</th></thead>";
	echo "<tbody><tr>";
	for($p = 1; $p <=5; $p++) {
		echo "<td>".$stats[$i * 6 + $p]."</td>";
	}
	echo "</tr></tbody>";
	echo "</table>";
	echo "</div></div></div>";
}
echo "</div>";
?>
