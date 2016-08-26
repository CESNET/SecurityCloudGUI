<script>
	var tabCount = 1, tabLimit = <?php echo $MAX_TABS; ?>;
	function Local_addTab() {
		var list = document.getElementById("Tabs").getElementsByTagName("li");
		
		list[tabCount].style.display = "";
		
		tabCount++;
		
		if(tabCount == tabLimit) {
			list[tabLimit].style.display = "none";
		}
	}
	
	function Local_clearTextarea(tab) {
		document.getElementById("Filter_"+tab).value = "";
	}
	
	function generateCustomOptions (tab) {
		var txtArea = document.getElementById("Options_CustomTextarea_"+tab);
		
		txtArea.value = Dbqry_parseQuerryParameter(tab);
	}
</script>

<div class="panel panel-primary">
	<div class="panel-body bg-info" style="padding: 0px;" id="test">
		<ul class="nav nav-pills nav-justified" id="Tabs">
			<li class="active"><a data-toggle="pill" href="#Tab_1">Tab 1</a></li>
			<?php
			for ($i = 2; $i <= $MAX_TABS; $i++) {
				echo "<li style='display:none;'><a data-toggle='pill' href='#Tab_$i'>Tab $i</a></li>";
			}
			?>
			<li><a href="#" onclick="Local_addTab();"><b>+</b></a></li>
		</ul>
	</div>
	<div class="panel-body">
		<!-- Import tab content -->
		<div class="tab-content">
			<?php
			for ($tab = 1; $tab <= $MAX_TABS; $tab++) {
				echo "<div id='Tab_$tab' class='tab-pane fade in";
				if ($tab == 1) {
					echo " active";
				}
				echo "'>";
					include "dbqryTabContent.php";
				echo "</div>";
			}
			?>
		</div>
	</div>
</div>