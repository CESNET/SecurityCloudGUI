<script>
	function Local_clearTextarea(tab) {
		document.getElementById("Dbqry_Filter_" + tab).value = "";
	}
	
	function generateCustomOptions (tab) {
		var txtArea = document.getElementById("Options_CustomTextarea_"+tab);
		
		txtArea.value = Dbqry_parseQuerryParameter(tab);
	}
	
	function Local_clearTab(tab) {
		document.getElementById("Dbqry_Output_" + tab).innerHTML = "";
	}
</script>

<div class="panel panel-primary">
	<div class="panel-heading">
		Database query
	</div>
	
	<div class="panel-body">
		<?php
			$tab = 1;
			include 'dbqryTabContent.php';
		?>
	</div>
</div>
