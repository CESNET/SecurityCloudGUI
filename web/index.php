<?php
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: ".gmdate('D, d M Y H:i:s')." GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
?>
<!DOCTYPE html>

<html lang="en">

<?php
	include 'php/config.php';
	include 'php/misc/selectors.php';
	/*
		NOTE: Change of the profile requires reloading of the page
	*/
	include 'php/misc/profileClass.php';
	include 'php/misc/profileMethods.php';
	
	/* CREATE A MASTER TREE FROM XML */
	$TREE_PROFILE = new Profile();												// Full tree of profiles
	createProfileTreeFromXml(loadXmlFile($IPFIXCOL_CFG), '/', $TREE_PROFILE);	// Fill it with ALL necessary data
	
	/* COLLECT USER-AVAILABLE PROFILES, COLLECT USER-SELECTED PROFILE AND VERIFY IT */
	$ARR_AVAILS = getAvailableProfiles('me');
	$PROFILE = getCurrentProfile();
	if (!verifySelectedProfile($PROFILE, $ARR_AVAILS)) {
		echo "You don't have the privileges to access the profile $PROFILE<br>";
		exit(1);
	}
	
	/* SEARCH FOR SELECTED SUBPROFILE ROOT */
	$aux = null;
	searchForProfile($TREE_PROFILE, $PROFILE, $aux);
	if ($aux == null) {
		echo "The profile $aux does not exist<br>";
		exit(2);
	}
	
	/* GENERATE INFO ABOUT AVAILABLE CHANNELS */
	$ARR_SOURCES = array();
	foreach ($aux->getChannels() as $c) {
		$ARR_SOURCES[] = $c->getName();
	}

	include 'php/misc/transactionsInclude.php';

	$USERSTAMP = createUserstamp($USERSTAMP_LENGTH);
	$NAME = preg_replace('/^(\/[a-zA-Z_][a-zA-Z0-9_]*)*\//', "", $PROFILE);
?>

<head>
	<?php include 'php/misc/head.php'; ?>
</head>

<body onload="Transactions.init();" onbeforeunload="Transactions.deinit();">
	<?php include 'php/misc/topbar.php'; ?>

	<!-- MODALS -->
	<?php include 'php/dbqry/dbqryFdistdumpHelpModal.php';	// Fdistdump manpage modal ?>
	<?php include 'php/dbqry/dbqryAddFilterModal.php';	// Fdistdump manpage modal ?>
	<?php include 'php/profiles/profilesModifyModal.php'	// Modal for profile management ?>
	<?php include 'php/misc/lookup.php';					// IPaddr lookup ?>
	<?php include 'php/graph/thumbGraphs.php';				// Graph thumbnails?>
	
	<!-- Page Content -->
	<div id="page-content-wrapper">
		<div class="container-fluid">
			<?php include 'php/views/workbench.php'; ?>
			
			<?php include 'php/views/profilemgr.php'; ?>
		</div>
	</div>
	<!-- /#page-content-wrapper -->

	<script>
		/* ========= */
		/* CONSTANTS */
		/* ========= */
		var USERSTAMP = "<?php echo $USERSTAMP; ?>";
		var ARR_RESOLUTION = [
			0.25 * 24, // 6 Hours
			0.5 * 24, // 12 Hours
			24, // 1 Day
			2 * 24, // 2 Days
			4 * 24, // 4 Days
			7 * 24, // 1 Week
			14 * 24, // 2 Weeks
			30 * 24, // 1 Month
			(2 * 30 + 1) * 24, // 2 Months
			(6 * 30 + 3) * 24, // 6 Months
			(8 * 30 + 4) * 24, // 8 Months
			365 * 24 // 1 Year
		];
		var PROFILE = <?php echo "\"$PROFILE\""; ?>;
		var ARR_SOURCES	= [<?php $size = sizeof($ARR_SOURCES); for($i = 0; $i < $size; $i++) echo "\"$ARR_SOURCES[$i]\", "; ?>];
		var ARR_GRAPH_VARS = [<?php $size = sizeof($ARR_GRAPH_VARS); for($i = 0; $i < $size; $i++) echo "\"$ARR_GRAPH_VARS[$i]\", "; ?>];
		var ARR_GRAPH_NAME = [<?php $size = sizeof($ARR_GRAPH_NAME); for($i = 0; $i < $size; $i++) echo "\"$ARR_GRAPH_NAME[$i]\", "; ?>];
		var HISTORIC_DATA =  <?php if ($HISTORIC_DATA) echo "true"; else echo "false"; ?>;
		var pendingResizeEvent = false;
	
		/* ================ */
		/* GLOBAL VARIABLES */
		/* ================ */
		var graphData, graphLegend;
		var timestampBgn = -1, timestampEnd = -1, selBgn = -1, selEnd = -1;
		var resolutionPtr = -1;
		var currentVar = 0;
		var selWindow = "Workbench";
		var pendingResizeEvent = false;
	</script>
	
	<?php include "php/misc/jsInclude.php"; ?>

	<!-- Menu Toggle Script -->
	<script>
	/* ==================== */
	/* DOCUMENT READY STUFF */
	/* ==================== */
	$(document).ready(function(){
		$('#lookupModal').on('shown.bs.modal', function () {
			$('#LookupToNERD').focus();
		});

		$(document).ready(function() {
			$('#Option_AggregateList_1').multiselect( { enableFiltering: true, maxHeight: 200, buttonWidth: '100%', } );
		});
		
		$(document).ready(function() {
			$('#Option_FieldList').multiselect( { enableFiltering: true, maxHeight: 200, buttonWidth: '100%',numberDisplayed: 9 } );
		});

		
		$('#TimePicker').datetimepicker({								// Initialize datetimepicker
			format: "YYYY-MM-DD HH:mm",									// ISO time format
			useCurrent: true,
			maxDate: new Date(Utility.getCurrentTimestamp() * 1000),	// Can't select time in the future
			sideBySide: true,											// For seeing days and daytime side by side
			ignoreReadonly: true,										// Because the input field is readonly
			toolbarPlacement: 'top',
			showClose: true,
			icons: { close: 'glyphicon glyphicon-ok' },
		});
		$('#TimePicker').on(											// Register onhide event callback for datetimepicker
			"dp.hide",
			function (e) {
				setGraphCenter(new Date(e.date).getTime() / 1000);
				acquireGraphData(updateGraph, true);
			}
		);
		$(window).resize(function(){									// Register callback (reset cursor position if window was resized)
			if (selectedWindow != "Workbench") {
				pendingResizeEvent = true;
				Graph.miniature.resize();
			}
			else							resizeGraph();
		});
		
		// *** PARSE URL PARAMS ***
		<?php
		if (isset($_GET['tbgn']) && isset($_GET['tend']) && isset($_GET['tres'])) {
			echo 'timestampBgn =',$_GET['tbgn'],';';
			echo 'timestampEnd =',$_GET['tend'],';';
			echo 'resolutionPtr=',$_GET['tres'],';';
		}
		
		if (isset($_GET['start'])) {
			// *** GRAB CURSOR SELECTION ***
			echo 'selBgn = ',$_GET['start'],';';
			if (isset($_GET['end']))	echo 'selEnd = ',$_GET['end'],';';
			else						echo 'selEnd = selBgn;';
			?>
			
			if (timestampBgn == -1 || timestampEnd == -1 || resolutionPtr == -1) {
				Core.computeTimeWindow();
				Core.computeResolution();
			}
			<?php
		}
		
		if (isset($_GET['filter'])) {
			echo 'document.getElementById(\'Dbqry_Filter_1\').innerHTML = "',$_GET['filter'],'";';
			echo 'location.href = "#"; location.href = "#WorkbenchDbqry";';
		}
		?>
		
		// *** FALLBACK TO DEFAULTS IF SOMETHING IS UNSET ***
		if (resolutionPtr == -1)						Default.setResolution();
		if (timestampBgn == -1 || timestampEnd == -1)	Default.setTimeWindow();
		
		// *** SANITY CHECK ***
		Core.sanityCheck();
		
		// *** ALL SET, INITIALIZE ***
		Core.initResolution(resolutionPtr);		
		Core.initWorkbench();
		
		toggleFieldsSelector()
	});
	</script>
</body>

</html>
