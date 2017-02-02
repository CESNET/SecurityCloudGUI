<?php
	if ($label == "Dbqry") $xlabel = "Database Query";
	else $xlabel = $label;
?>

<div id="TogglerAnchor<?php echo $label; ?>"></div>
<div class="panel panel-info" style="cursor: pointer;" onclick="toggleTab('<?php echo $label; ?>');">
	<div class="panel-heading">
		<div class="row">
			<div class="col-xs-11"> <h3 class="panel-title"><?php echo $xlabel; ?><span id="SelectedTimeBox"></h3> </div>
			<div class="col-sm-1 text-right">
					<span class="glyphicon glyphicon-chevron-up" aria-hidden="true" id="Toggler<?php echo $label; ?>"></span>
			</div>
		</div>
	</div>
	<?php if ($label == "Graph") { ?>
	<div class="panel-body" id="GraphMiniArea" style="display: none;">
		<div id="miniDygraph" style="width: 100%; height: 100px;"></div>
	</div>
	<?php } ?>
</div>