<div id="WindowWorkbench">
	<div id="WorkbenchGraph">
		<!-- ACTIVE GRAPH + CHANNELS -->
		<div class="row">
			<div class="col-lg-10">
				<?php include 'php/graph/activeGraph.php'; ?>
			</div>
			<div class="col-lg-2">
				<?php include 'php/misc/otherActions.php'; ?>
				<?php include 'php/graph/channelSelection.php'; ?>
			</div>
		</div>
	</div>
	
	<!-- STATISTICS -->
	<?php include 'php/stats/statistics.php'; ?>
	
	<!-- DATABASE QUERY -->
	<div id="WorkbenchDbqry">
		<div class="row">
			<div class="col-lg-12">
				<?php include 'php/dbqry/dbqry.php'; ?>
			</div>
		</div>
	</div>
</div>