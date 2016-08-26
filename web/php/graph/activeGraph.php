<div class="panel panel-primary">
	<div class="panel-heading clearfix">
		<div class="row" style="vertical-align: middle;">
			<div class="col-sm-11" id="ActiveGraphLabel">Flows Any</div>
			<div class="col-sm-1 text-right">
				<a href="#" style="color: white;" data-toggle="modal" data-target="#ActiveGraphRenderSettings">
					<span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
				</a>
			</div>
		</div>
	</div>
	<div class="panel-body">
		<div id="wrapper222" style="left: 0px; position:relative; width: 100%; height: 500px;">
			<div id="GraphArea" style="position: absolute; width: 1000px; height: 500px;">
				<div id="GraphArea_Cursor1"></div>
				<div id="GraphArea_CurSpan"></div>
				<div id="GraphArea_Cursor2"></div>
			</div>
			<div id="dygraph" style="width: 100%; height: 500px;"></div>
		</div>
	</div>
	<div class="panel-footer">
		<div class="row">
			<div class="col-sm-3 text-left">
				<div class="dropdown">
					<a href="#" style="width: 100%;" class="dropdown-toggle btn btn-default" type="button" data-toggle="dropdown"><b>Display:</b> <span id="DisplaySizePrint"></span>&nbsp;<span class="caret"></span></a>
					
					<ul class="dropdown-menu" id="DisplayResolutionList">
						<li><a href="#" onclick="setResolution('absolute', 0);">6 Hours</a></li>
						<li><a href="#" onclick="setResolution('absolute', 1);">12 Hours</a></li>
						<li><a href="#" class="list-group-item active" onclick="setResolution('absolute', 2);">1 Day</a></li>
						<li><a href="#" onclick="setResolution('absolute', 3);">2 Day</a></li>
						<li><a href="#" onclick="setResolution('absolute', 4);">4 Day</a></li>
						<li><a href="#" onclick="setResolution('absolute', 5);">1 Week</a></li>
						<li><a href="#" onclick="setResolution('absolute', 6);">2 Week</a></li>
						<li><a href="#" onclick="setResolution('absolute', 7);">1 Month</a></li>
						<li><a href="#" onclick="setResolution('absolute', 8);">2 Months</a></li>
						<li><a href="#" onclick="setResolution('absolute', 9);">6 Months</a></li>
						<li><a href="#" onclick="setResolution('absolute', 10);">8 Months</a></li>
						<li><a href="#" onclick="setResolution('absolute', 11);">1 Year</a></li>
					</ul>
				</div>
			</div>
			<div class="col-sm-6 text-center">
				<input type='text' class="form-control text-center" id='TimePickerDisplay' style="cursor: pointer;" onclick="Graph.interval=false;">
				<!-- Setting up time via the picker will ALWAYS result in a single time point. The interval needs to be falsed, otherwise you'll quickly spot a bug. -->
			</div>
			<div class="col-sm-3 text-right">
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
