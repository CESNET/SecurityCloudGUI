<?php
	/**
	 *	Prints the profile tree in a format convenient for the GUI, alongside with the
	 *	valid URL links for changing the profiles.
	 */
	function printProfileTree($object) {
		$str = preg_replace("/^\/[a-zA-Z0-9_\/]+\//", "", $object->getName());
		echo "<ul>";
		echo "<li><a href='index.php?profile=".$object->getName()."'>$str</a></li>";
		
		foreach($object->getChildren() as $o) {
			printProfileTree($o);
		}
		
		echo "</ul>";
	}
	
	function printProfileTree2($object, $level) {
		echo "<tr><td>";
		
		/* Print offset */
		for ($i = 0; $i < $level; $i++) {
			echo "<span class='glyphicon glyphicon-play'></span>";
		}
		
		/* Print name */
		$str = preg_replace("/^\/[a-zA-Z0-9_\/]+\//", "", $object->getName());
		echo $str;
		
		
		
		/* Print sources */
		echo "</td><td>";
		foreach ($object->getChannels() as $src) {
			echo $src->getName()." ";
		}
		
		echo "</td><td>";
		echo "<button class='btn btn-primary'>Edit profile</button> ";
		echo "<button class='btn btn-danger'>Delete profile</button> ";
		echo "<button class='btn btn-info'>Add subprofile</button> ";
		
		echo "</td></tr>";
		
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
		<table class="table table-striped table-condensed table-bordered"><tbody>
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