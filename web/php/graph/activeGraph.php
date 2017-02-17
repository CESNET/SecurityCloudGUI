<?php $GRAPH_HEIGHT = 350; ?>

<div class="panel panel-primary">
	<div class="panel-heading clearfix">
		<a href="#" style="color: white" data-toggle="modal" data-target="#GraphThumbModal"><div class="row" style="vertical-align: middle;">
			<div>
				<span id="ActiveGraphLabel">Flows All</span> &nbsp;
				<span class="glyphicon glyphicon-chevron-down"></span>
			</div>
		</div></a>
	</div>
	<div class="panel-body">
		<div id="wrapper222" style="left: 0px; position:relative; width: 100%; height: <?php echo $GRAPH_HEIGHT; ?>px;">
			<!--div id="GraphArea" style="position: absolute; width: 1px; height: <?php //echo $GRAPH_HEIGHT; ?>px; z-index:11;"-->
			<div id="GraphArea" style="position: absolute; width: 1000px; height: <?php echo $GRAPH_HEIGHT; ?>px;">
				<div id="GraphArea_Cursor1"></div>
				<div id="GraphArea_CurSpan"></div>
				<div id="GraphArea_Cursor2"></div>
			</div>
			<div id="dygraph" style="width: 100%; height: <?php echo $GRAPH_HEIGHT; ?>px;"></div>
		</div>
	</div>
	<div class="panel-footer clearfix">
		<div class="row" style="vertical-align: middle;">
			<div class="col-md-3 text-left">
				<div class='input-group date' id="TimePicker">
					<?php include 'resolutionPicker.php'; ?>
					
					<input type='text' id="TimePickerDisplay" class="form-control text-center" style="display: none;" readonly />
					
					<span class="input-group-addon" onblur="$('#TimePicker').blur();">
						Move to...
					</span>
				</div>
			</div>
			
			<div class="col-md-2">
				<?php include 'activeGraphRenderSettings.php'; ?>
			</div>
			
			<div class="col-md-4 text-center">
				<div class="well well-sm" id="SelectedTimeBox"></div>
			</div>
			
			<div class="col-md-3 text-right">
				<div class="btn-group btn-group-justified">
					<a href="#" class="btn btn-default" title="Move backwards" onclick="graphMoveStep(-1)"><span class="glyphicon glyphicon-backward"></span></a>
					<a href="#" class="btn btn-default" title="Zoom in" onclick="setResolution('relative', -1);"><span class="glyphicon glyphicon-zoom-in"></span></a>
					<a href="#" class="btn btn-default" title="Zoom out" onclick="setResolution('relative', 1);"><span class="glyphicon glyphicon-zoom-out"></span></a>
					<a href="#" class="btn btn-default" title="Move forwards" onclick="graphMoveStep(1)"><span class="glyphicon glyphicon-forward"></span></a>
					<a href="#" class="btn btn-default" title="Goto end" onclick="graphMoveStep(0)"><span class="glyphicon glyphicon-step-forward"></span></a>
				</div>
			</div>
		</div>
	</div>
</div>
