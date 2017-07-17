<?php
	function printProfileDropdown($object, $level) {
		$str = preg_replace('/^\/[a-zA-Z0-9_\/]+\//', "", $object->getName());
		echo '<li><a href=\'#\' onclick=\'Profile.changeLocation("',$object->getName(),'");\'>';
		for ($i = 0; $i < $level; $i++) echo '-';
		echo ' ',$str,'</a></li>';
		
		foreach($object->getChildren() as $o) {
			printProfileDropdown($o, $level + 1);
		}
	}
?>

<div class="panel panel-primary">
	<div class="panel-heading">
		Actions
	</div>
	
	<div class="panel-body">
		<div class="dropdown">
			<a href="#" style="width: 100%;" class="dropdown-toggle btn btn-default" type="button" data-toggle="dropdown"><b>Profile:</b> <?php echo $PROFILE; ?>&nbsp;<span class="caret"></span></a>
			
			<ul class="dropdown-menu">
				<?php
				$object;
				foreach ($ARR_AVAILS as $av) {
					$object = null;
					searchForProfile($TREE_PROFILE, $av, $object);
					printProfileDropdown($object, 0);
				}
				?>
			</ul>
		</div>
	</div>
	
	<div class="panel-footer">
		<button class="btn btn-default btn-block" onclick="Core.parallelInstance();">Create parallel instance</button>
	</div>
</div>