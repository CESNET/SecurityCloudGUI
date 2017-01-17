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
    <div id="wrapper">

	<!-- Sidebar -->
	<?php include 'php/misc/sidebar.php'; ?>

	<!-- MODALS -->
	<!-- Modal window with render settings for the active graph -->
	<?php include 'php/graph/activeGraphRenderSettings.php'; ?>
	<!-- Modal with fdistdump manpage -->
	<?php include 'php/dbqry/dbqryFdistdumpHelpModal.php'; ?>
	<!-- Modal window with profile view/add/delete dialogs -->
	<?php include 'php/profiles/profilesModifyModal.php' ?>
	
	<?php include 'php/misc/lookup.php'; ?>
	<!--a class="btn btn-default" id="menu-toggle" href="#menu-toggle">Toggle Menu</a-->
	
	<!-- Page Content -->
	<div id="page-content-wrapper">
		<div class="container-fluid">
			<!--div class="alert alert-info alert-dismissible text-center" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				Currently selected profile: <?php //echo $NAME; ?>
			</div-->
		
			<div id="MainPageGraphs">
				<!-- ACTIVE GRAPH + SOURCES -->
				<div class="row">
					<div class="col-lg-10">
						<?php include 'php/graph/activeGraph.php'; ?>
					</div>
					<div class="col-lg-2">
						<?php include 'php/graph/channelSelection.php'; ?>
					</div>
				</div>
				
				<!-- THUMBNAILS -->
				<?php include 'php/graph/thumbGraphs.php'; ?>		
			</div>
			<div id="MainPageStats">
				<div class="row">
					<div class="col-lg-12" id="StatsContent"></div>
				</div>
			</div>
			<div id="MainPageDbqry">
				<div class="row">
					<div class="col-lg-12">
						<?php include 'php/dbqry/dbqry.php'; ?>
					</div>
				</div>
			</div>
			<div id="MainPageProfiles">
				<?php include 'php/profiles/profiles.php'; ?>
			</div>
		</div>
	</div>
	<!-- /#page-content-wrapper -->

	</div>
	<!-- /#wrapper -->

	<script>
		/* ========= */
		/* CONSTANTS */
		/* ========= */
		var USERSTAMP = "<?php echo $USERSTAMP; ?>";
		var ARR_RESOLUTION = [ 0.25 * 24, 0.5 * 24, 24, 2 * 24, 7 * 24, 14 * 24, 30 * 24, (2 * 30 + 1) * 24, (6 * 30 + 3) * 24, (8 * 30 + 4) * 24, 365 * 24 ];
		var PROFILE = <?php echo "\"$PROFILE\""; ?>;
		var ARR_SOURCES	= [<?php $size = sizeof($ARR_SOURCES); for($i = 0; $i < $size; $i++) echo "\"$ARR_SOURCES[$i]\", "; ?>];
		var ARR_GRAPH_VARS = [<?php $size = sizeof($ARR_GRAPH_VARS); for($i = 0; $i < $size; $i++) echo "\"$ARR_GRAPH_VARS[$i]\", "; ?>];
		var ARR_GRAPH_NAME = [<?php $size = sizeof($ARR_GRAPH_NAME); for($i = 0; $i < $size; $i++) echo "\"$ARR_GRAPH_NAME[$i]\", "; ?>];
		var USE_LOCAL_TIME = <?php if ($USE_LOCAL_TIME) echo "true"; else echo "false"; ?>;
	
		/* ================ */
		/* GLOBAL VARIABLES */
		/* ================ */
		var graphData, graphLegend;
		var timestampBgn, timestampEnd;
		var resolutionPtr;
		var currentVar = 0;
	</script>
	
	<script src="js/thirdparty/jquery.js"></script>
	<script src="js/thirdparty/bootstrap.min.js"></script>
	<script src="js/thirdparty/moment-with-locales.min.js"></script>
	<script src="js/thirdparty/bootstrap-datetimepicker.min.js"></script>
	<script src="js/thirdparty/dygraph-combined.min.js"></script>
	
	<script src="js/utility.js"></script>									<!-- Utility class -->
	<script src="js/graph.js"></script>											<!-- Graph class -->
	<script src="js/graphManagement.js"></script>
	<script src="js/dbqry.js"></script>
	<script src="js/transactions.js"></script>
	<script src="js/misc.js"></script>											<!-- gotoPage() -->
	<script src="js/profiles.js"></script>

	<!-- Menu Toggle Script -->
	<script>
	/* ==================== */
	/* DOCUMENT READY STUFF */
	/* ==================== */
	$(document).ready(function(){
		$('#TimePicker').datetimepicker({								// Initialize datetimepicker
			//format: "DD MMM YYYY HH:mm",
			format: "YYYY-MM-DD HH:mm",
			useCurrent: true,
			maxDate: new Date(Utility.getCurrentTimestamp() * 1000),
			sideBySide: true
		});
		$('#TimePicker').on(												// Register onhide event callback for datetimepicker
			"dp.hide",
			function (e) {
				setGraphCenter(new Date(e.date).getTime() / 1000);
				acquireGraphData(updateGraph);
			}
		);
		
		gotoPage('Graphs');														// Set the default viewpoint
		
		/* TIMESTAMP INIT */
		<?php if (isset($_GET['begin']) && isset($_GET['end'])) {
			echo 'timestampBgn = ',$_GET['begin'],';';
			echo 'timestampEnd = ',$_GET['end'],';';
		}
		else {
			echo 'timestampBgn = Utility.getCurrentTimestamp()-(24*3600);';
			echo 'timestampEnd = Utility.getCurrentTimestamp();';
		} ?>
		
		<?php if (isset($_GET['res'])) {
			echo 'resolutionPtr = ',$_GET['res'],';';
		}
		else {
			echo 'resolutionPtr = 2;';
		} ?>
		
		/* RESOLUTION INIT */
		var list = document.getElementById("DisplayResolutionList").getElementsByTagName("a");
		document.getElementById("DisplaySizePrint").innerHTML = list[resolutionPtr].innerHTML;
		
		acquireGraphData(initializeGraph, null);								// Create graph
		
		$(window).resize(function(){											// Register callback (reset cursor position if window was resized)
			Graph.initCursor(["GraphArea_Cursor1", "GraphArea_Cursor2", "GraphArea_CurSpan"]);
			Graph.initTime(graphData[0][0].getTime()/1000, graphData[graphData.length - 1][0].getTime()/1000);
		});
	});
	
	$("#menu-toggle").click(function(e) {										// Sidebar toggling
		e.preventDefault();
		$("#wrapper").toggleClass("toggled");
	});
	
	$('[rel=tooltip]').tooltip();
	</script>
</body>

</html>
