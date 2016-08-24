<?php
	function findParentNode($root, $prefix, $search, &$result) {
		$attr = $root->attributes();
		$match = $prefix."/".$attr[0];
		
		if ($match == $search) {
			$result = $root;
			return;
		}
		else {
			if (empty($root->subprofileList)) {
				return;
			}
			
			foreach ($root->subprofileList->children() as $c) {
				findParentNode($c, $match, $search, $result);
			}
		}
	}

	include "../config.php";
	//include "../misc/profileClasses.php";
	include "../misc/profileMethods.php";
	
	$mode = $_GET["mode"];
	$name = $_GET["name"];
	
	$lock = fopen("../app.lock", "r");
	flock($lock, LOCK_EX);
	
	$xml = simplexml_load_file($IPFIXCOL_CFG);
	
	// Example: /live/path/to/profile
	$prefix = preg_replace("/\/[a-zA-Z_][a-zA-Z0-9_]*$/", "", $name);		// Output: /live/path/to
	$flname	= preg_replace("/^(\/[a-zA-Z_][a-zA-Z0-9_]*)*\//", "", $name);	// Output: profile
	
	/* COLLECT USER-AVAILABLE PROFILES, COLLECT USER-SELECTED PROFILE AND VERIFY IT */
	$ARR_AVAILS = getAvailableProfiles("me");
	$PROFILE = $prefix;
	if (!verifySelectedProfile($PROFILE, $ARR_AVAILS)) {
		echo "You don't have the privileges to access the profile $PROFILE<br>";
		exit(1);
	}
	
	$parent = null;
	findParentNode($xml, "", $prefix, $parent);
	
	if ($mode == "create") {
		$type	= $_GET["type"];												//	...
		$chnls	= $_GET["channels"];
	
		if (empty($parent->subprofileList)) {								//	If profile has no children [
			$list = $parent->addChild("subprofileList");					//		add one
		}																	//	]
		
		$profile = $list->addChild("profile");								// 	Add <profile> element
		$profile->addAttribute("name", $flname);							// 	Modify <profile name="">
		$profile->addChild("type", $type);									// 	Add <type> to <profile>
		$profile->addChild("directory", $base_dir.$name);					// 	Add <directory> to <profile>
		
		$chlist = $profile->addChild("channelList");						// 	Add <channelList> to <profile>
		$channels = explode(";", $chnls);									//	Break $chnls to list of channels
		
		foreach ($channels as $c) {											//	For every channel [
			$buf = explode (":", $c);										// 		break it to bits
			
			$channel = $chlist->addChild("channel");						//		add <channel> to <channelList>
			$channel->addAttribute("name", $buf[0]);						//		modify <channel name="">

			$srclist = $channel->addChild("sourceList");					//		add <sourceList> to <channel>
			
			for ($i = 2; $i < sizeof($buf); $i++) {							//		for every source of channel {
				$srclist->addChild("source", $buf[$i]);						//			add <source> to <sourceList>
			}																//		}
			
			$channel->addChild("filter", $buf[1]);							//		add <filter> to <channel>
		}																	//	]
	}																		// )
	else if ($mode == "delete") {
		if ($name != "/live") {
			if (sizeof($parent->subprofileList->children()) == 1) {?>
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4>Deletion successful</h4>
				</div>
				<div class="modal-body">
					The profile was deleted successfully. Reload the page.
					<span style="display: none" id="AsyncQuerryResult">success</span>
				</div>
				<?php unset($parent->subprofileList);
			}
			else {
				foreach ($parent->subprofileList->children() as $c) {
					$attr = $c->attributes();
					if ($attr[0] == $flname) {
						unset($c[0]);
						break;
					}
				}
			}
		}
		else {																// Deleting live results
			unset($xml->subprofileList);									// In deleting it's children
		}
	}
	else {
		exit(69);
	}
	
	$xml->asXML($IPFIXCOL_CFG);
	
	// Release the lock
	flock($lock, LOCK_UN);
	fclose($lock);
?>