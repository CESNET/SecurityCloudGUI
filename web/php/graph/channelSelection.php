<div class="panel panel-primary">
	<div class="panel-heading">
		Channels
	</div>
	<div class="panel-body">
		<div id="Channels" onclick="Graph.updateSourcesVisibility('Channels');">
			<?php
				$size = sizeof($ARR_SOURCES);
				for($i = 0; $i < $size; $i++) {
					echo '<label><span>&nbsp;&nbsp;&nbsp;</span> <input type=\'checkbox\' name=\'',$ARR_SOURCES[$i],'\' checked> ',$ARR_SOURCES[$i],'</label><br>';
				}
			?>
		</div>
	</div>
</div>