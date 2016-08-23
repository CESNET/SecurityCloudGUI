<div class="panel panel-primary">
	<div class="panel-heading">
		Sources
	</div>
	<div class="panel-body">
		<div id="Sources" onclick="Graph.updateSourcesVisibility('Sources');">
			<?php
				for($i = 0; $i < sizeof($ARR_SOURCES); $i++) {
					echo "<label><span>&nbsp;&nbsp;&nbsp;</span> <input type='checkbox' name='$ARR_SOURCES[$i]' checked> $ARR_SOURCES[$i]</label><br>\n";
				}
			?>
		</div>
	</div>
</div>