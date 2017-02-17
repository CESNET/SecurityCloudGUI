<div class="panel panel-primary">
	<div class="panel-heading">
		Channels
	</div>
	<div class="panel-body">
		<div id="Channels" onclick="Graph.updateSourcesVisibility('Channels');">
			<div class="row">
			<?php
				$size = sizeof($ARR_SOURCES);
				for($i = 0; $i < $size; $i++) {
					echo '<div class=\'col-lg-6 col-md-6 col-sm-4\'><label><span>&nbsp;&nbsp;&nbsp;</span> <input type=\'checkbox\' name=\'',$ARR_SOURCES[$i],'\' checked> ',$ARR_SOURCES[$i],'</label><br></div>';
				}
			?>
			</div>
		</div>
	</div>
</div>