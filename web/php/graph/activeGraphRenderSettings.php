<div class="panel panel-primary">
	<div class="panel-heading">
		Graph Rendering
	</div>
	
	<div class="panel-body">
		<p>
			Render style<br>
			<label><input type='radio' name='renderStyle' onclick="Graph.updateRenderMode(computeRenderMode());"> Lines</label>
			<br>
			<label><input type='radio' name='renderStyle' checked onclick="Graph.updateRenderMode(computeRenderMode());"> Areas</label>
			<hr>
			Render type<br>
			<label><input type='radio' name='renderType' onclick="Graph.updateRenderMode(computeRenderMode());"> Comparative</label>
			<br>
			<label><input type='radio' name='renderType' checked onclick="Graph.updateRenderMode(computeRenderMode());"> Stacked</label>
		</p>
	</div>
</div>