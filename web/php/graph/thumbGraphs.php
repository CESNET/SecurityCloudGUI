<div class="row">
	<?php
	for($i = 0; $i < sizeof($ARR_GRAPH_VARS); $i++) {
		echo "<div class='col-md-4'>\n";
		echo "\t<div class='panel panel-primary'>\n";
		echo "\t\t<div class='panel-heading'>\n";
		echo "\t\t\t$ARR_GRAPH_NAME[$i]\n";
		echo "\t\t</div>\n";
		echo "\t\t<div class='panel-body'>\n";
		echo "\t\t\t<a href='#' onclick='changeVariable($i);'>";
		echo "\t\t\t\t<img src='image/img_sm.png' class='thumb-image' width='100%'>";
		echo "\t\t\t</a>";
		echo "\t\t</div>";
		echo "\t</div>";
		echo "</div>";
	}
	?>
</div>
