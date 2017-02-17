// #include <graphControls/setGraphCenter.js>
// #include <graphControls/setResolution.js>
// #include <graphControls/graphMoveStep.js>

var SECONDS_PER_HOUR = 3600;
var backupCursor1, backupCursor2, backupInterval;

/* ================== */
/* ACQUIRE GRAPH DATA */
/* ================== */
function acquireGraphData(callback) {
	var request = Utility.initAjax();
	var backup = arguments[1];
	
	request.onreadystatechange = function(){	// callback
		if(request.readyState == 4){
			var out = request.responseText;		// enable JS_DEBUG to see the response
			var parsedOut = JSON.parse(out);
			graphData = parsedOut.data;
			graphLegend = parsedOut.meta.legend;
			callback(backup);
		}
	}
	
	var sources = ARR_SOURCES[0];
	for (var i = 1; i < ARR_SOURCES.length; i++) {
		sources += ":"+ARR_SOURCES[i];
	}
	
	var profile = encodeURI(PROFILE);
	
	// mode, var, profile, sources, time
	var url = "php/async/graphCreate.php?var="+ARR_GRAPH_VARS[currentVar]+"&mode=image&profile="+profile+"&sources="+sources+"&time="+timestampBgn+":"+timestampEnd;
	console.log(url);
	
	request.open("GET", url, true);
	request.send();
}

/**
 *  Makes copies of the cursors which then will be
 *  used by the updateGraph method if the overridePosition
 *  is set to false.
 */
function backupCursors () {
	backupCursor1 = Graph.curTime1;
	
	if (Graph.interval) {
		backupCursor2 = Graph.curTime2;
		backupInterval = Graph.interval;
	}
	else {
		backupCursor2 = Graph.curTime1;
		backupInterval = false;
	}
}

/* ================ */
/* COLORIZE SOURCES */
/* ================ */
function colorizeSources(srcDomId) {
	var list = document.getElementById(srcDomId).getElementsByTagName("span");
	
	var colours = Graph.dygraph.getColors();
	
	for (var i = 0; i < list.length; i++) {
		list[i].style.backgroundColor = colours[i];
	}
}

/* ============= */
/* UPDATE THUMBS */
/* ============= */
function updateThumbs() {
	var list = document.getElementsByClassName("thumb-image");
	
	var sources = ARR_SOURCES[0];
	for (var i = 1; i < ARR_SOURCES.length; i++) {
		sources += ":"+ARR_SOURCES[i];
	}
	
	for(var i = 0; i < ARR_GRAPH_VARS.length; i++) {
		list[i].src = "php/async/graphCreate.php?var="+ARR_GRAPH_VARS[i]+"&mode=thumb&profile="+PROFILE+"&sources="+sources+"&time="+timestampBgn+":"+timestampEnd;
	}
}

/* ================== */
/* TIME WINDOW HANDLE */
/* ================== */
function timeWindowHandle() {
	var displ = document.getElementById("SelectedTimeBox");
	
	var str = Utility.timestampToNiceReadable(Graph.curTime1);
	//displ.innerHTML = (Graph.interval ? " from " : " at ") + str;
	displ.innerHTML = str;
	
	if(Graph.interval) {
		str = Utility.timestampToNiceReadable(Graph.curTime2);
		displ.innerHTML += " - " + str;
	}
	else {														// Compute whether the cursor is not too close to a border
		var windowSize = timestampEnd - timestampBgn;			// Width of the graph window
		var windowPos  = Graph.curTime1 - timestampBgn;			// Position of the cursor within window
		var percentage = windowPos / windowSize * 100;			// What portion of the window is the distance between left border and the cursor
		
		if (percentage < 5 || 95 < percentage) {				// If it is in the 5% space to any border
			var rtn = setGraphCenter(Graph.curTime1);
			if (setGraphCenter(Graph.curTime1)) {				// If the check returns true, we have to reload data
				acquireGraphData(updateGraph, true);			// And reset position of the cursor to the center
			}
		}
	}
	
	if (statsVisible()) collectStatistics();
}

/* =================== */
/* COMPUTE RENDER MODE */
/* =================== */
function computeRenderMode() {
	var render = document.getElementsByName('render');
	
	var i = 0;
	while (!render[i].checked) i++;
	
	return i;
}

/* ================ */
/* INITIALIZE GRAPH */
/* ================ */
function sanitizeGraphTimestamps(data) {
	for (var i = 0; i < data.length; i++) {
		data[i][0] = new Date(parseInt(data[i][0] + "000"));
	}
}

function initializeGraph(none) {
	sanitizeGraphTimestamps(graphData);
	
	Graph.init("dygraph", graphData, graphLegend, ARR_GRAPH_NAME[currentVar], computeRenderMode());
	Graph.initTime(graphData[0][0].getTime()/1000, graphData[graphData.length - 1][0].getTime()/1000, true);
	Graph.initCursor(["GraphArea_Cursor1", "GraphArea_Cursor2", "GraphArea_CurSpan"]);
	
	document.getElementById("dygraph").onmousedown = Graph.moveCursor;
	Graph.registerCallback(timeWindowHandle);
	
	colorizeSources("Channels");
	timeWindowHandle();
	
	updateThumbs();
	
	Graph.updateSourcesVisibility("Channels");
	
	if (selBgn != -1) {
		console.log("setting cursor to new position" + (selBgn != selEnd) + "; selBgn = " + selBgn + "; selEnd = " + selEnd);
		Graph.updateCursor(selBgn, selEnd, selBgn != selEnd);
	}
}

/* ============ */
/* UPDATE GRAPH */
/* ============ */
/**
 * Updates the graph area. This function is called from acquireGraphData
 * as a callback. overridePosition:bool specifies whether the position of the
 * cursor will be preserved (FALSE) or whether it will reset to the center (TRUE).
 */
function updateGraph(overridePosition) {
	sanitizeGraphTimestamps(graphData);
	
	Graph.update(graphData, ARR_GRAPH_NAME[currentVar], computeRenderMode());
	Graph.initTime(graphData[0][0].getTime()/1000, graphData[graphData.length - 1][0].getTime()/1000);

	if (overridePosition)	Graph.initCursor(["GraphArea_Cursor1", "GraphArea_Cursor2", "GraphArea_CurSpan"]);
	else 					Graph.updateCursor(backupCursor1, backupCursor2, backupInterval);
	
	timeWindowHandle();
	updateThumbs();
	Graph.updateSourcesVisibility("Channels");
}

/**
 *  After picking a different graph from the thumbnails, this
 *  function will properly change the label of the main
 *  graph window and then reloading whole graph, retaining
 *  all current selections.
 */
function changeVariable(varPtr) {
	currentVar = varPtr;
	
	//  Change title, update graph
	document.getElementById("ActiveGraphLabel").innerHTML = ARR_GRAPH_NAME[currentVar];
	
	backupCursors();
	acquireGraphData(updateGraph, false);
}
