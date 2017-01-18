var SECONDS_PER_HOUR = 3600;

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

/* ================ */
/* SET GRAPH CENTER */
/* ================ */
/**
*	Takes a selected resolution and defines a new time window regarding the
*	resolution with newCenter in the center of this window. If right boundary
*	should be in the future, window is adjusted so the right border is at 'now'
*/
function adjustGraphCenter(tmp) {
	var END = Utility.getCurrentTimestamp();
	
	if (timestampEnd > END) {
		timestampEnd = END;
		timestampBgn = timestampEnd - ARR_RESOLUTION[resolutionPtr] * SECONDS_PER_HOUR;
	}
	
	if (tmp == END) return false;

	return true;
}

function setGraphCenter(newCenter) {
	var tmp = timestampEnd;
	timestampBgn = newCenter - ARR_RESOLUTION[resolutionPtr] * SECONDS_PER_HOUR / 2;
	timestampEnd = newCenter + ARR_RESOLUTION[resolutionPtr] * SECONDS_PER_HOUR / 2;
	return adjustGraphCenter(tmp);
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

/* ============== */
/* SET RESOLUTION */
/* ============== */
function unselectResolution(list) {
	for (var i = 0; i < list.length; i++) {
		if (list[i].className == "list-group-item active") {
			list[i].className = "";
			return i;
		}
	}
	
	return -1;
}

function setResolution (type, value) {
	var list = document.getElementById("DisplayResolutionList").getElementsByTagName("a");
	
	var i = unselectResolution(list);
	
	if(type == "relative") {								// Relative is an offset from the last selection
		resolutionPtr = i + value;							// So add the offset to the previously selected res
		
		if(resolutionPtr < 0)		resolutionPtr = 0;		// And if it overflown, fix it
		else if(resolutionPtr >= list.length) resolutionPtr = list.length-1;// And if it overflown, fix it
	}
	else if (type == "absolute") {							// Absolute is just a new value
		resolutionPtr = value;								// Assign it
	}
	
	list[resolutionPtr].className = "list-group-item active";// Select the new resolution
	document.getElementById("DisplaySizePrint").innerHTML = list[resolutionPtr].innerHTML;
	
	setGraphCenter(Graph.curTime1);							// Reset time window
	acquireGraphData(updateGraph, true);					// Update stuff
}

/* ================== */
/* TIME WINDOW HANDLE */
/* ================== */
function timeWindowHandle() {
	var displ = document.getElementById("SidebarSelectedTime");
	
	var str = Utility.timestampToNiceReadable(Graph.curTime1);
	displ.innerHTML = (Graph.interval ? "<br>Begin: " : "") + str;
	
	if(Graph.interval) {
		str = Utility.timestampToNiceReadable(Graph.curTime2);
		displ.innerHTML += "<br>End: " + str;
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
}

/* =================== */
/* COMPUTE RENDER MODE */
/* =================== */
function computeRenderMode() {
	var type = document.getElementsByName("renderType");
	var style= document.getElementsByName("renderStyle");
	
	var prms = {};
	
	var i;
	for(i = 0; i < type.length; i++) {
		if(type[i].checked) break;
	}
	prms["type"] = i;
	
	for(i = 0; i < style.length; i++) {
		if(style[i].checked) break;
	}
	prms["style"] = i;
	
	return prms;
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
}

/* ============ */
/* UPDATE GRAPH */
/* ============ */
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

function changeVariable(varPtr) {
	currentVar = varPtr;
	
	//  Change title, update graph
	document.getElementById("ActiveGraphLabel").innerHTML = ARR_GRAPH_NAME[currentVar];
	
	backupCursors();
	acquireGraphData(updateGraph, false);
}
