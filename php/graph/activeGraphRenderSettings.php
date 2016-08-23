<div id="myModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Graph Rendering options</h4>
			</div>
		
			<div class="modal-body">
				<p>
					Render style<br>
					<label><input type='radio' name='renderStyle'> Lines</label>
					<br>
					<label><input type='radio' name='renderStyle' checked> Areas</label>
					<hr>
					Render type<br>
					<label><input type='radio' name='renderType'> Comparative</label>
					<br>
					<label><input type='radio' name='renderType' checked> Stacked</label>
				</p>
			</div>
			
			<div class="modal-footer">
				<a href="#" class="btn btn-default" data-dismiss="modal" onclick="Graph.updateRenderMode(computeRenderMode());">Update</a>
			</div>
		</div>
	</div>
</div>
