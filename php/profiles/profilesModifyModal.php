<script>
	function _addChannel() {
		document.getElementById("ProfilesModalChannels").innerHTML += document.getElementById("ProfilesModalChannelsMacro").innerHTML;
	}
</script>

<div id="ProfilesModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content" id="ProfilesModalContent"></div>
	</div>
</div>

<div id="ProfilesModalChannelsMacro" style="display: none;">
	<form class="form-horizontal well">
		<div class="form-group">
			<label for="ProfilesChannelName" class="col-sm-2 control-label">Name:</label>
			<div class="col-sm-10">
				<input type="text" value="New_Channel" id="ProfilesChannelName" class="form-control">
			</div>
		</div>
	
		<div class="form-group">
			<label for="ProfilesChannelFilter" class="col-sm-2 control-label">Filter:</label>
			<div class="col-sm-10">
				<textarea placeholder="*" rows="3" id="ProfileChannelFilter" class="form-control"></textarea>
			</div>
		</div>
		
		<div class="form-group">
			<label for="ProfilesChannelSources" class="col-sm-2 control-label">Sources:</label>
			<div class="col-sm-10">
				<div class="checkbox" id="ProfilesChannelSources">
					<label><input type="checkbox" checked> ch1</label> 
					<label><input type="checkbox" checked> ch2</label> 
				</div>
			</div>
		</div>
	</form>
</div>

<!--
	In the case of the delete, there should be a confirmation dialog:
	Are you sure you want to delete a profile "Profile"?
	Yes | No
-->

<!--
	Before ANY edits to the profile, the user *must* be verified
-->