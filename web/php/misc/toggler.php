<div id="TogglerAnchor<?php echo $label; ?>"></div>
<div class="panel panel-info" style="cursor: pointer;" onclick="toggleTab('<?php echo $label; ?>');">
	<div class="panel-heading">
		<div class="row">
			<div class="col-xs-11"> <h3 class="panel-title"><?php echo $label; ?><span id="SelectedTimeBox"></h3> </div>
			<div class="col-sm-1 text-right">
					<span class="glyphicon glyphicon-chevron-up" aria-hidden="true" id="Toggler<?php echo $label; ?>"></span>
			</div>
		</div>
	</div>
</div>