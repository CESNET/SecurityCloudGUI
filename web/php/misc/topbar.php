<?php
	function _printProfileDropdown($object, $level) {
		$str = preg_replace('/^\/[a-zA-Z0-9_\/]+\//', "", $object->getName());
		echo '<li><a href=\'#\' onclick=\'Profile.changeLocation("',$object->getName(),'");\'>';
		for ($i = 0; $i < $level; $i++) echo '-';
		echo ' ',$str,'</a></li>';
		
		foreach($object->getChildren() as $o) {
			_printProfileDropdown($o, $level + 1);
		}
	}
?>

<nav class="navbar navbar-inverse navbar-top">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand" href="#">SecurityCloud</a>
		</div>
		
		<div> <!-- class="collapse navbar-collapse" -->
			<ul class="nav navbar-nav">
				<li id="TopbarLinkWorkbench"> <a href="#" onclick="gotoWindow('Workbench');">Workbench</a> </li>
				<li id="TopbarLinkProfileManager"> <a href="#"  onclick="gotoWindow('ProfileManager');">Manage Profiles</a> </li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-hashpopup="true" aria-expanded="false">Change profile <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<?php
						$object;
						foreach ($ARR_AVAILS as $av) {
							$object = null;
							searchForProfile($TREE_PROFILE, $av, $object);
							_printProfileDropdown($object, 0);
						}
						?>
					</ul>
				</li>
			</ul>
		</div>
	</div>
</nav>