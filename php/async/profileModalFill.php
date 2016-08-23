<?php
	include "../config.php";
	include "../misc/profileClass.php";
	include "../misc/profileMethods.php";
	
	$mode = $_GET["mode"];
	
	// CREATE A MASTER TREE FROM XML
	$TREE_PROFILE = new Profile();												// Full tree of profiles
	createProfileTreeFromXml(loadXmlFile($IPFIXCOL_CFG), "/", $TREE_PROFILE);	// Fill it with ALL necessary data
	
	// COLLECT USER-AVAILABLE PROFILES, COLLECT USER-SELECTED PROFILE AND VERIFY IT
	$ARR_AVAILS = getAvailableProfiles("me");
	$profile = getCurrentProfile();
	if (!verifySelectedProfile($profile, $ARR_AVAILS)) {
		echo "You don't have the privileges to access the profile $profile<br>";
		exit(1);
	}
	
	// SEARCH FOR SELECTED SUBPROFILE ROOT
	$aux = null;
	searchForProfile($TREE_PROFILE, $profile, $aux);
	if ($aux == null) {
		echo "The profile $aux does not exist<br>";
		exit(2);
	}
	
	$flname	= preg_replace("/^(\/[a-zA-Z_][a-zA-Z0-9_]*)*\//", "", $aux->getName());
	
	if ($mode == "view") {
		?>
		<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4>View a profile</h4>
			</div>
		
			<div class="modal-body">
				<form class="form-horizontal">
					<div class="form-group">
						<label for="ProfilesModalParent" class="col-sm-2 control-label">Name:</label>
						<div class="col-sm-10">
							<input type="text" value="<?php echo $flname; ?>" id="ProfilesModalName" disabled class="form-control">
						</div>
					</div>
				
					<div class="form-group">
						<label for="ProfilesModalType" class="col-sm-2 control-label">Type:</label>
						<div class="col-sm-10">
							<input type="text" value="<?php if($aux->getShadow()){ echo "shadow"; } else {echo "normal";} ?>" id="ProfilesModalType" disabled class="form-control">
						</div>
					</div>
					
					<div class="form-group">
						<label for="ProfilesModalParent" class="col-sm-2 control-label">Parent:</label>
						<div class="col-sm-10">
							<input type="text" value="<?php echo $aux->getParentName(); ?>" id="ProfilesModalParent" disabled class="form-control">
						</div>
					</div>
				</form>
				
				<h5>Channels</h5>
				<div id="ProfilesModalChannels" style="overflow: auto; height: 250px;">
				<?php foreach ($aux->getChannels() as $c) { ?>
					<form class="form-horizontal well">
						<div class="form-group">
							<label for="ProfilesChannelName" class="col-sm-2 control-label">Name:</label>
							<div class="col-sm-10">
								<input type="text" value="<?php echo $c->getName(); ?>" id="ProfilesChannelName" class="form-control" disabled>
							</div>
						</div>
					
						<div class="form-group">
							<label for="ProfilesChannelFilter" class="col-sm-2 control-label">Filter:</label>
							<div class="col-sm-10">
								<textarea rows="3" id="ProfileChannelFilter" class="form-control" disabled><?php $filter = preg_replace("/\t/", "", $c->getFilter()); echo $filter; ?></textarea>
							</div>
						</div>
						
						<div class="form-group">
							<label for="ProfilesChannelSources" class="col-sm-2 control-label">Sources:</label>
							<div class="col-sm-10">
								<div class="checkbox" id="ProfilesChannelSources">
									<?php
										foreach($c->getSources() as $s) {
											echo "<span class='label label-default'>$s</span> ";
										}
									?>
								</div>
							</div>
						</div>
					</form>
				<?php } ?>
				</div>
			</div>
			
			<div class="modal-footer">
				<a href="#" class="btn btn-default" data-dismiss="modal" onclick="">Close</a>
			</div>
		<?php
	}
	else if ($mode == "add") {
		?>
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4>Add a subprofile</h4>
		</div>
		<div class="modal-body">
			<form class="form-horizontal">
				<div class="form-group">
					<label for="ProfilesModalParent" class="col-sm-2 control-label">Name:</label>
					<div class="col-sm-10">
						<input type="text" value="New_Profile" id="ProfilesModalName" class="form-control">
					</div>
				</div>
			
				<div class="form-group">
					<label for="ProfilesModalType" class="col-sm-2 control-label">Type:</label>
					<div class="col-sm-10">
						<select id="ProfilesModalType" class="form-control">
							<option value="normal" selected>normal</option>
							<option value="shadow">shadow</option>
						</select>
					</div>
				</div>
				
				<div class="form-group">
					<label for="ProfilesModalParent" class="col-sm-2 control-label">Parent:</label>
					<div class="col-sm-10">
						<input type="text" value="<?php echo $aux->getName(); ?>" id="ProfilesModalParent" disabled class="form-control">
					</div>
				</div>
			</form>
			
			<h5>Channels</h5>
			<div id="ProfilesModalChannels" style="overflow: auto; height: 250px;"></div>

			<button class="btn btn-info" onclick="_addChannel();">Add channel</button>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-success" data-dismiss="modal">Add</button>
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		</div>
		<?php
	}
	else if ($mode == "delete") {
		?>
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4>Delete a profile</h4>
		</div>
		<div class="modal-body">
			<h5>Are you sure?</h5>
			<p>
				This action will <b>delete</b> your profile configuration (and of any of it's children) from the ipfixcol configuration file. It will <b>not</b> delete the physical data from the disk. If you're trying to delete the live profile, only it's children will be removed.
			</p>
		</div>
		<div class="modal-footer">
			<!--div class="btn-group btn-group-justified"-->
				<button type="button" class="btn btn-danger" data-dismiss="modal" onclick=''>Yes</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">No</button>
			<!--/div-->
		</div>
		<?php
	}
?>