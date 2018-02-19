<div class="row row-eq-height">
	<div class="col-md-2">
		<div class="panel panel-info">
			<div class="panel-heading">
				Channels
			</div>
			<div class="panel-body">
				<div id="Channels_Dbqry_<?php echo $tab; ?>">
					<?php
						$size = sizeof($ARR_SOURCES);
						for($i = 0; $i < $size; $i++) {
							echo '<label><span>&nbsp;&nbsp;&nbsp;</span> <input type=\'checkbox\' name=\'',$ARR_SOURCES[$i],'\' checked> ',$ARR_SOURCES[$i],'</label><br>';
						}
					?>
				</div>
			</div>
			<div class="panel-footer">
				<div class="btn-group chnls-group-justified">
					<button class="btn btn-default" onclick="Local_setChannelsTo(<?php echo $tab; ?>, true);">
						All
					</button>
					<button class="btn btn-default" onclick="Local_setChannelsTo(<?php echo $tab; ?>, false);">
						None
					</button>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="panel panel-info">
			<div class="panel-heading">
				Filter
			</div>
			<div class="panel-body">
				<textarea class="form-control" rows="4" id="Dbqry_Filter_<?php echo $tab; ?>" placeholder="Example: ip 127.0.0.1 and proto tcp and bytes > 1024"></textarea>
			</div>
			<div class="panel-footer">
				<div class="row">
					<div class="col-md-4">
						<button type="button" class="btn btn-default btn-block" data-toggle="modal" data-target="#DbqryAddFilterModal">
							Save filter
						</button>
					</div>
					<div class="col-md-4 open">
						<button type="button" class="btn btn-default btn-block dropdown-toggle" data-toggle="dropdown">
							Use saved filter
							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu" id="SavedFilterList"></ul>
					</div>
					<div class="col-md-4">
						<button class="btn btn-default btn-block" onclick="Local_clearTextarea(<?php echo $tab; ?>);">
							Clear filter
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="panel panel-info">
			<div class="panel-body bg-info" style="padding: 0px;">
				<ul class="nav nav-pills nav-justified" id="DbMainOptPicker_<?php echo $tab; ?>">
					<li class="active"><a data-toggle="pill" href="#DbMainOpt1_<?php echo $tab; ?>">Fast Options</a></li>
					<li><a data-toggle="pill" href="#DbMainOpt2_<?php echo $tab; ?>" onclick="generateCustomOptions(<?php echo $tab; ?>);">Custom Options</a></li>
				</ul>
			</div>
			<div class="panel-body">
				<div class="tab-content">
					<div id="DbMainOpt1_<?php echo $tab; ?>" class="tab-pane fade in active clearfix">
						<?php include 'dbgryOptions.php'; ?>
					</div>
					<div id="DbMainOpt2_<?php echo $tab; ?>" class="tab-pane fade">
						<a href="#" data-toggle="modal" data-target="#DbqryFdistdumpHelpModal">fdistdump options overview</a><br>
						<textarea class="form-control" rows="5" id="Options_CustomTextarea_<?php echo $tab; ?>"></textarea>
					</div>
				</div>
			</div>
			<div class="panel-footer">
				<button class="btn btn-default btn-block" id="Dbqry_ProcessButton_<?php echo $tab; ?>" onclick="Dbqry_processRequest('<?php echo $tab; ?>');">Process request</button>
				<button class="btn btn-danger btn-block" id="Dbqry_StopButton_<?php echo $tab; ?>" onclick="Dbqry_stopRequest('<?php echo $tab; ?>');" style="display:none;">Kill request</button>
			</div>
		</div>
	</div>
</div>

<!-- PROGRESS BAR -->
<div class="progress">
  <div id="Dbqry_ProgressBar_<?php echo $tab; ?>" class="progress-bar progress-bar-info progress-bar-stripped" role="progressbar" style="min-width: 2em; width: 0%; display: none;">
  0%
  </div>
</div>

<!-- QUERRY OUTPUT BOX -->
<div id="Dbqry_Output_<?php echo $tab; ?>"></div>
