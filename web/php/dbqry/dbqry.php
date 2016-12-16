<script>
	var tabCount = 1, tabLimit = <?php echo $MAX_TABS; ?>;
	function Local_addTab() {
		var list = document.getElementById("Tabs").getElementsByTagName("li");
		
		var i = 0;		
		while (list[i].style.display != "none" && i < tabLimit) i++;
		
		list[i].style.display = "";
		
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
	
	function Local_clearTab(tab) {
		document.getElementById("Dbqry_Output_" + tab).innerHTML = "";
	}
	
	function Local_closeTab(tab) {
		if (tabCount == 1) {
			alert("Cannot close last and only tab.");
			return;
		}
		
		tabCount--;							// Reduce number of tabs
		Local_clearTab(tab);				// Clear contents of closed tab
		var list = document.getElementById("Tabs").getElementsByTagName("li");
		
		// *** Change focus ***
		var i = tab;		
		while (list[i].style.display == "none") {	// Find suitable open tab
			if (i + 1 >= tabLimit)	i = 0;			// If no tab is open on the right side, search from the leftmost
			else 					i++;			// from left to right
		}
		i++;
		
		var addr = "#Tabs a[href='#Tab_" + i + "']";// Identifier of the destination tab
		$(addr).tab('show');						// Change view
		
		list[tab-1].style.display = "none"; 		// Hide the to-be-removed tab
	}
</script>

<div class="panel panel-primary">
	<div class="panel-body bg-info" style="padding: 0px;" id="test">
		<ul class="nav nav-pills nav-justified" id="Tabs">
			<li class="active"><a data-toggle="pill" href="#Tab_1">Tab 1</a></li>
			<?php
			for ($i = 2; $i <= $MAX_TABS; $i++) {
				echo '<li style=\'display:none;\'><a data-toggle=\'pill\' href=\'#Tab_',$i,'\'>Tab ',$i,'</a></li>';
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
				echo '<div id=\'Tab_',$tab,'\' class=\'tab-pane fade in';
				if ($tab == 1) {
					echo ' active';
				}
				echo '\'>';
				?>
				<div class="row">
					<div class="col-md-8">&nbsp;</div>
					<div class="col-md-4">
						<div class="btn-group btn-group-justified">
							<button type="button" class="btn btn-primary" onclick="Local_clearTab('<?php echo $tab; ?>');">Clear the results</button>
							<button type="button" class="btn btn-primary" onclick="Local_closeTab('<?php echo $tab; ?>');">Close this tab</button>
						</div>
					</div>
				</div><br>
				<?php
					include 'dbqryTabContent.php';
				echo '</div>';
			}
			?>
		</div>
	</div>
</div>
