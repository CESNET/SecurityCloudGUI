<?php
	if ($label == "Dbqry") $xlabel = "Database Query";
	else $xlabel = $label;
?>

<div id="TogglerAnchor<?php echo $label; ?>"></div>
<div class="panel panel-info">
	<div class="panel-heading">
		<div class="row">
			<div class="col-xs-11"> <h3 class="panel-title"><?php echo $xlabel; ?></h3> </div>
			<div class="col-sm-1 text-right">
				<a style="cursor: pointer;" onclick="toggleTab('<?php echo $label; ?>');">
					<span class="glyphicon glyphicon-chevron-up" aria-hidden="true" id="Toggler<?php echo $label; ?>"></span>
				</a>
			</div>
		</div>
	</div>
</div>