<?php
	/**
	 *  @brief Finds and returns an XML subnode representing subprofile of /live
	 *  
	 *  @param [in] $root The root XML node from which the search started
	 *  @param [in] $prefix Auxiliary variable, use empty string "" when calling this function
	 *  @param [in] $search Profile to search for in form /live/path/to/profile
	 *  @param [in] $result Return value is stored in this variable
	 *  @return XML subnode on match, otherwise $result will be unchanged
	 *  
	 *  @details Load the profiles.xml file to the simplexml object and then call
	 *  this function to find your desired subprofile which is returned as a simplexml
	 *  object you can further edit
	 */
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
	include "../misc/profileMethods.php";
	
	$mode = $_GET["mode"];
	$name = $_GET["name"];
	
	$lock = fopen("../app.lock", "r");
	if(!flock($lock, LOCK_EX)) {
		?>
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4>Error</h4>
		</div>
		<div class="modal-body">
			The GUI was not set up properly. You have to set up corrent privileges for the app.lock file. Consult the installation guide.
			<span style="display: none" id="AsyncQuerryResult">fail</span>
		</div>
		<div class="modal-footer">
			<button class="btn btn-default" data-dismiss="modal">Okay</button>
		</div>
		<?php
		exit(1);
	}
	
	$xml = simplexml_load_file($IPFIXCOL_CFG);
	
	// Example: /live/path/to/profile
	$prefix = preg_replace("/\/[a-zA-Z_][a-zA-Z0-9_]*$/", "", $name);		// Output: /live/path/to
	$flname	= preg_replace("/^(\/[a-zA-Z_][a-zA-Z0-9_]*)*\//", "", $name);	// Output: profile
	
	/* COLLECT USER-AVAILABLE PROFILES, COLLECT USER-SELECTED PROFILE AND VERIFY IT */
	$ARR_AVAILS = getAvailableProfiles("me");
	$PROFILE = $prefix;
	if (!verifySelectedProfile($PROFILE, $ARR_AVAILS)) {
		?>
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4>Error</h4>
		</div>
		<div class="modal-body">
			You don't have the privileges to access the profile <?php echo $PROFILE; ?>
			<span style="display: none" id="AsyncQuerryResult">fail</span>
		</div>
		<div class="modal-footer">
			<button class="btn btn-default" data-dismiss="modal">Okay</button>
		</div>
		<?php
		exit(1);
	}
	
	$parent = null;
	findParentNode($xml, "", $prefix, $parent);
	
	if ($parent == null) {
		?>
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4>Error</h4>
		</div>
		<div class="modal-body">
			The path <?php echo $parent; ?> does not even exist in the profiles hierarchy.
			<span style="display: none" id="AsyncQuerryResult">fail</span>
		</div>
		<div class="modal-footer">
			<button class="btn btn-default" data-dismiss="modal">Okay</button>
		</div>
		<?php
		exit(1);
	}
	
	/* ========= */
	/* MAIN CODE */
	/* ========= */
	if ($mode == "create") {
		$type	= $_GET["type"];
		$chnls	= $_GET["channels"];
		
		if (!preg_match("/^[a-zA-Z_][a-zA-Z0-9_]*$/", $flname)) {			// ERROR handling (BADNAME)
			?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4>Error</h4>
			</div>
			<div class="modal-body">
				The profile name does not comply with the ipfixcol naming convention. First letter of the name must be a alphabetical letter, rest can be letters, numbers or underscores.
				<span style="display: none" id="AsyncQuerryResult">fail</span>
			</div>
			<div class="modal-footer">
				<button class="btn btn-default" data-dismiss="modal">Okay</button>
			</div>
			<?php
			exit(2);
		}
		else if ($type != "normal" && $type != "shadow") {					// ERROR handling (BADTYPE)
			?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4>Error</h4>
			</div>
			<div class="modal-body">
				Please stop trying to hack this... Profile type can be 'normal' or 'shadow', nothing else.
				<span style="display: none" id="AsyncQuerryResult">fail</span>
			</div>
			<div class="modal-footer">
				<button class="btn btn-default" data-dismiss="modal">Okay</button>
			</div>
			<?php
			exit(3);
		}
	
		if (empty($parent->subprofileList)) {								//	If profile has no children [
			$list = $parent->addChild("subprofileList");					//		add one
		}																	//	]
		else {
			$list = $parent->subprofileList;
		}
		
		$profile = $list->addChild("profile");								// 	Add <profile> element
		$profile->addAttribute("name", $flname);							// 	Modify <profile name="">
		$profile->addChild("type", $type);									// 	Add <type> to <profile>
		$profile->addChild("directory", $IPFIXCOL_DATA.$name);				// 	Add <directory> to <profile>
		
		$chlist = $profile->addChild("channelList");						// 	Add <channelList> to <profile>
		$channels = explode(";", $chnls);									//	Break $chnls to list of channels
		
		foreach ($channels as $c) {											//	For every channel [
			$buf = explode (":", $c);										// 		break it to bits
			
			if (!preg_match("/^[a-zA-Z_][a-zA-Z0-9_]*$/", $buf[0])) {
				?>
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4>Error</h4>
				</div>
				<div class="modal-body">
					The channel name does not comply with the ipfixcol naming convention. First letter of the name must be a alphabetical letter, rest can be letters, numbers or underscores.
					<span style="display: none" id="AsyncQuerryResult">fail</span>
				</div>
				<div class="modal-footer">
					<button class="btn btn-default" data-dismiss="modal">Okay</button>
				</div>
				<?php
				exit(4);
			}
			
			// TODO: somehow test the filter (safely)
			// Build a PROFILE_TREE and check agains source names?!
			
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
			if (sizeof($parent->subprofileList->children()) == 1) {
				unset($parent->subprofileList);
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
		?>
		<div class="modal-body">
			Unknown mode selected: <?php echo $mode; ?>
			<span style="display: none" id="AsyncQuerryResult">success</span>
		</div>
		<?php
		exit(69);
	}
	
	// Rewrite the original ipfixcol cfg file
	$xml->asXML($IPFIXCOL_CFG);
	
	// Find out the ipfixcol PID and reload it's configuration
	$pid = exec("head -1 $PIDFILE");
	$rtn = exec("kill -10 $pid && echo $?");
	
	// Release the lock
	flock($lock, LOCK_UN);
	fclose($lock);
	
	?>
<!-- Success msg back to GUI -->
<div class="modal-body">
	Success. Page will reload shortly...
	<span style="display: none" id="AsyncQuerryResult">success</span>
</div>