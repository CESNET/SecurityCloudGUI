<div class="modal fade" role="dialog" id="GraphThumbModal">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4>
					Choose a graph variable to display
				</h4>
			</div>
			
			<div class="modal-body">
				<div class="row">
					<?php
					for($i = 0; $i < sizeof($ARR_GRAPH_VARS); $i++) {
						echo '<div class=\'col-md-4\'>';
						echo '<div class=\'panel panel-primary\'>';
						echo '<div class=\'panel-heading\'>',$ARR_GRAPH_NAME[$i],'</div>';
						echo '<div class=\'panel-body\'>';
						echo "<a href='#' data-dismiss='modal' onclick='changeVariable($i);'>";
						echo "<img src='image/img_sm.png' class='thumb-image' width='100%'>";
						echo '</a></div></div></div>';
					}
					?>
				</div>
			</div>
		</div>
	</div>
</div>