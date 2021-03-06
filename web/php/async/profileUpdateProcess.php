<?php
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: ".gmdate('D, d M Y H:i:s')." GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
?>
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
		$match = $prefix.'/'.$attr[0];
		
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

	include '../config.php';
	include '../misc/profileMethods.php';
	include '../misc/filterValidator.php';
	
	$mode = $_GET['mode'];
	$name = $_GET['name'];
	
	$lock = fopen('../app.lock', 'r');
	if(!flock($lock, LOCK_EX)) {
		?>
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert">
				<span>&times;</span>
			</button>
			The GUI was not set up properly. You have to set up corrent privileges for the app.lock 
			file. Consult the installation guide.
			<span style="display: none" id="AsyncQuerryResult">fail</span>
		</div>
		<?php
		exit(1);
	}
	
	$xml = simplexml_load_file($IPFIXCOL_CFG);
	
	// Example: /live/path/to/profile
	$prefix = preg_replace('/\/[a-zA-Z_][a-zA-Z0-9_\-]*$/', "", $name);		// Output: /live/path/to
	$flname	= preg_replace('/^(\/[a-zA-Z_][a-zA-Z0-9_\-]*)*\//', "", $name);	// Output: profile
	
	/* COLLECT USER-AVAILABLE PROFILES, COLLECT USER-SELECTED PROFILE AND VERIFY IT */
	$ARR_AVAILS = getAvailableProfiles('me');
	$PROFILE = $name == '/live' ? $name : $prefix;
	if (!verifySelectedProfile($PROFILE, $ARR_AVAILS)) {
		?>
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert">
				<span>&times;</span>
			</button>
			You don't have the privileges to access the profile <?php echo $PROFILE; ?>
			<span style="display: none" id="AsyncQuerryResult">fail</span>
		</div>
		<?php
		exit(1);
	}
	
	$parent = null;
	findParentNode($xml, "", $prefix, $parent);
	
	if ($name != '/live') {
		if ($parent == null) {
			?>
			<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert">
					<span>&times;</span>
				</button>
				The path <?php echo $prefix; ?> does not even exist in the profiles hierarchy.
				<span style="display: none" id="AsyncQuerryResult">fail</span>
			</div>
			<?php
			exit(1);
		}
		
		if ($parent->type == "shadow") {
			?>
			<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert">
					<span>&times;</span>
				</button>
				You cannot create a subprofile of a shadow profile.
				<span style="display: none" id="AsyncQuerryResult">fail</span>
			</div>
			<?php
			exit(1);
		}
	}
	
	/* ========= */
	/* MAIN CODE */
	/* ========= */
	$dry_run = false; // If error occures, dry_run will test for all possible errors but will not save changes
	if ($mode == 'create') {
		$type	= $_GET['type'];
		$chnls	= $_GET['channels'];
		
		if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_\-]*$/', $flname)) {			// ERROR handling (BADNAME)
			?>
			<!-- This error probably never happens due to regex based chackes above -->
			<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert">
					<span>&times;</span>
				</button>
				The profile name does not comply with the ipfixcol naming convention. All names must
				comply with regex [a-zA-Z_][a-zA-Z0-9_\-]
				<?php if (!$dry_run) { 
					echo '<span style="display: none" id="AsyncQuerryResult">fail</span>';
					$dry_run = true;
				} ?>
			</div>
			<?php
		}
		else if ($type != 'normal'/* && $type != 'shadow'*/) {					// ERROR handling (BADTYPE)
			?>
			<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert">
					<span>&times;</span>
				</button>
				Please stop trying to hack this... Profile type can be 'normal', nothing else.
				<?php if (!$dry_run) {
					echo '<span style="display: none" id="AsyncQuerryResult">fail</span>';
					$dry_run = true;
				} ?>
			</div>
			<?php
		}
	
		if (empty($parent->subprofileList)) {								//	If profile has no children [
			$list = $parent->addChild('subprofileList');					//		add one
		}																	//	]
		else {
			$list = $parent->subprofileList;
		}
		
		$profile = $list->addChild('profile');								// 	Add <profile> element
		$profile->addAttribute('name', $flname);							// 	Modify <profile name="">
		$profile->addChild('type', $type);									// 	Add <type> to <profile>
		
		if ($SINGLE_MACHINE) {
			$profile->addChild('directory', $IPFIXCOL_DATA.$name);			// 	Add <directory> to <profile>
		}
		else {
			$profile->addChild('directory', $IPFIXCOL_DATA.'/%h'.$name);	// 	Add <directory> to <profile>
		}
		
		$chlist = $profile->addChild('channelList');						// 	Add <channelList> to <profile>
		$channels = explode(';', $chnls);									//	Break $chnls to list of channels
		
		foreach ($channels as $c) {											//	For every channel [
			$buf = explode (':', $c);										// 		break it to bits
			
			if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_\-]*$/', $buf[0])) {
				?>
				<div class="alert alert-danger alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert">
						<span>&times;</span>
					</button>
					The channel name <?php echo $buf[0]; ?> does not comply with the ipfixcol naming
					convention. All names must meet the regex [a-zA-Z_][a-zA-Z0-9_\-]
					<?php if (!$dry_run) {
						echo '<span style="display: none" id="AsyncQuerryResult">fail</span>';
						$dry_run = true;
					} ?>
				</div>
				<?php
			}
			
			if (($filter_error = validateFilter($buf[1])) != null) {
				?>
				<div class="alert alert-danger alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert">
						<span>&times;</span>
					</button>
					Filter for channel <?php echo $buf[0]; ?> is not valid:
					<pre><?php echo $filter_error; ?></pre>
					<?php if (!$dry_run) {
						echo '<span style="display: none" id="AsyncQuerryResult">fail</span>';
						$dry_run = true;
					} ?>
				</div>
				<?php
			}
			
			$channel = $chlist->addChild('channel');						//		add <channel> to <channelList>
			$channel->addAttribute('name', $buf[0]);						//		modify <channel name="">
			
			$srclist = $channel->addChild('sourceList');					//		add <sourceList> to <channel>
			
			$size = sizeof($buf);
			for ($i = 2; $i < $size; $i++) {								//		for every source of channel {
				$srclist->addChild('source', $buf[$i]);						//			add <source> to <sourceList>
			}																//		}
			
			$channel->addChild('filter', $buf[1]);							//		add <filter> to <channel>
		}																	//	]
	}																		// )
	else if ($mode == 'delete') {
		if ($name != '/live') {
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
	
	// If error occured, exit w/o saving changes
	if ($dry_run) {
		exit(2);
	}
	
	// Rewrite the original ipfixcol cfg file
	if (!$xml->asXML($IPFIXCOL_CFG)) {
		?>
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert">
				<span>&times;</span>
			</button>
			Ipfixcol configuration couldn't be overwritten. Contact your administrator or if you're
			the administrator, check access privileges for the file <?php echo $IPFIXCOL_CFG; ?>
			<span style="display: none" id="AsyncQuerryResult">fail</span>
		</div>
		<?php
		exit(3);
	}
	
	if ($SINGLE_MACHINE) {
		// Find out the ipfixcol PID and reload it's configuration
		$pid = exec("head -1 $PIDFILE");
		$rtn = exec("kill -10 $pid && echo $?");
	}
	else {
		$updater = fopen($IPFIXCOL_UPDATE_FILE, "w");
		fclose($updater);
	}
	
	// Release the lock
	flock($lock, LOCK_UN);
	fclose($lock);
	
	?>
<!-- Success msg back to GUI -->
<div class="alert alert-success alert-dismissible" role="alert">
	<button type="button" class="close" data-dismiss="alert">
		<span>&times;</span>
	</button>
	Success. Page will reload shortly...
	<span style="display: none" id="AsyncQuerryResult">success</span>
</div>