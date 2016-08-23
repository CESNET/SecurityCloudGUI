<script>
	function _fillProfileModal(mode) {
		var ajax = Utility.initAjax();
		
		ajax.onreadystatechange = function () {
			if (ajax.readyState == 4) {
				document.getElementById("ProfilesModalContent").innerHTML = ajax.responseText;
			}
		}
		
		ajax.open("GET", "php/async/profileModalFill.php?mode="+mode, true);
		ajax.send(null);
	}
</script>

<?php
	/**
	 *	Prints the profile tree in a format convenient for the GUI, alongside with the
	 *	valid URL links for changing the profiles.
	 */
	function printProfileTree2($object, $level) {
		echo "<tr><td>";
		
		/* Print offset */
		for ($i = 0; $i < $level; $i++) {
			echo "&nbsp;&nbsp;&nbsp;";
		}
		
		if ($level % 2 == 0) {
			echo "&nbsp;&#9679;&nbsp;";
		}
		else {
			echo "&nbsp;&#9900;&nbsp;";
		}
		
		/* Print name */
		$str = preg_replace("/^\/[a-zA-Z0-9_\/]+\//", "", $object->getName());
		echo $str;
		
		
		
		/* Print sources */
		echo "</td><td>";
		foreach ($object->getChannels() as $src) {
			echo "<span class='label label-default'>".$src->getName()."</span> ";
		}
		
		echo "</td><td align='right' style='width: 30%; min-width: 350px;'><div class='btn-group'>";
		echo "<button class='btn btn-info' data-toggle='modal' data-target='#ProfilesModal' onclick=\"_fillProfileModal('view');\">View profile</button>";
		echo "<button class='btn btn-success' data-toggle='modal' data-target='#ProfilesModal' onclick=\"_fillProfileModal('add');\">Add subprofile</button>";
		echo "<button class='btn btn-danger' data-toggle='modal' data-target='#ProfilesModal' onclick=\"_fillProfileModal('delete');\">Delete profile</button>";
		
		echo "</div></td></tr>";
		
		/* Do children */
		foreach($object->getChildren() as $o) {
			printProfileTree2($o, $level + 1);
		}
	}
?>

<div class="panel panel-primary">
	<div class="panel-heading">
		Profiles
	</div>
	
	<div class="panel-body">
		<table class="table table-striped table-condensed table-hover"><!-- table-bordered -->
			<tr>
				<thead>
					<th>Profile</th>
					<th>Channels</th>
					<th>Options</th>
				</thead>
			</tr><tbody>
		<?php
			$object;
			foreach($ARR_AVAILS as $av) {
				$object = null;
				searchForProfile($TREE_PROFILE, $av, $object);
				printProfileTree2($object, 0);
			}
		?>
		</tbody></table>
	</div>
</div>