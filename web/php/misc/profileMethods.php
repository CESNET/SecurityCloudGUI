<?php
/**
*	Parses the URL for 'profile' parameter and returns it.
*	If no 'profile' param is found, '/live' is returned
*	instead.
*/
function getCurrentProfile() {
	$profile = '/live';
	if(isset($_GET['profile'])) {
		$profile = $_GET['profile'];
	}
	return $profile;
}

/**
*	Returns a SimpleXML object loaded from file $filename
*/
function loadXmlFile($filename) {
	return simplexml_load_file($filename);
}

/**
*	Create object of class Profile (see profileClass.php)
*	from the SimpleXML '$xml' object. Result of function
*	is stored in '$object'. '$prefix' is mandatory arg
*	used in recursion. Always call this function with '/'.
*/
function createProfileTreeFromXml ($xml, $prefix, &$object) {
	$part = $xml->attributes();	// PHP5 compat workaround
	$object->setName($prefix.$part[0]);
	
	$nprefix = $object->getName().'/';
	
	$shadow = false;
	$filter = null;
	
	foreach ($xml->children() as $ch) {
		if ($ch->getName() == 'subprofileList') {
			foreach ($ch->children() as $node) {
				$result = $object->addChild();
				
				createProfileTreeFromXml ($node, $nprefix, $result);
			}
		}
		else if ($ch->getName() == 'channelList') {
			foreach ($ch->children() as $node) {
				foreach ($node as $chProp) {
					if ($chProp->getName() == 'filter') {
						$filter = $chProp;
					}
					else if ($chProp->getName() == 'sourceList') {
						$sources = array();
						foreach($chProp->children() as $src) {
							$sources[] = $src;
						}
					}
				}
				
				$part = $node->attributes();
				
				$object->addChannel($part[0], $filter, $sources);
			}
		}
		else if ($ch->getName() == 'type') {
			if($ch == 'shadow') {
				$shadow = true;
				$object->setShadow($shadow);
			}
		}
	}
}

function _createProfileTreeFromXml ($xml, $prefix, &$object) {
	$part = $xml->attributes();	// PHP5 compat workaround
	$object->setName($prefix.$part[0]);
	
	$nprefix = $object->getName().'/';
	
	$object->setName($xml->type == 'shadow');	// If <type> contains shadow, this evaluates to true
	$filter = null;
	
	if (isset($xml->subprofileList->children())) {
		$subprofiles = $xml->subprofileList->children();
		$size = (int)sizeof($subprofiles);
		for ($i = 0; $i < $size; $i++) {
			$result = $object->addChild();
			createProfileTreeFromXml($subprofiles[$i], $nprefix, $result);
		}
		unset($subprofiles);
	}
	
	$channels = $xml->channelList->children();
	$size = (int)sizeof($channels);
	for($i = 0; $i < $size; $i++) {
		$filter = $channels[$i]->filter;
		
		$sources = $channels[$]->sourceList->children();
		$ssize = (int)sizeof($sources);
		$srcs = array();
		for ($p = 0; $p < $ssize; $p++) {
			$srcs[] = $sources[$p];
		}
		
		$part = $channels[$i]->attributes();
		$object->addChannel($part[0], $filter, $srcs);
		
		unset($srcs);
	}
	unset($channels);
}

/**
*	In a '$root' Profile object search for subroot
*	specified by '$path' and store this node in
*	'$result'
*/
function searchForProfile($root, $path, &$result) {
	if ($root->getName() == $path) {
		$result = $root;
	}
	else {
		foreach ($root->getChildren() as $c) {
			$prefix = str_replace('/', '-', $c->getName());
			$search = str_replace('/', '-', $path);
			
			if (preg_match("/^$prefix/", $search)) {
				searchForProfile($c, $path, $result);
			}
		}
	}
}

/**
*	INDEV
*
*	Retrieve the array of paths to profile subroots
*	for specified '$user'
*/
// NOTE: $avails = getAvailableProfiles("me");
function getAvailableProfiles($user) {
	$result = array('/live/emails', '/live/brute');
	$result = array('/live');
	
	return $result;
}

/**
*	Takes the current URL '$selected' profile and
*	compares it with the '$avails' - profiles available
*	for the curent user. If any of '$avails' is a prefix
*	of the '$selected', TRUE is returned. Otherwise FALSE
*	is returned, since the user has not access to '$selected'
*	profile.
*/
function verifySelectedProfile($selected, $avails) {
	$size = (int)sizeof($avails);
	for ($i = 0; $i < $size; $i++) {
		$prefix = str_replace('/', '-', $avails[$i]);
		$search = str_replace('/', '-', $selected);
		
		if (preg_match("/^$prefix/", $search)) {
			return true;
		}
	}
	
	return false;
}
?>
