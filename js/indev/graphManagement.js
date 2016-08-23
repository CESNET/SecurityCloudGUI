/* ================== */
/* ACQUIRE GRAPH DATA */
/* ================== */
function acquireGraphData(callback) {
	var request = Utility.initAjax();
	
	request.onreadystatechange = function(){	// callback
		if(request.readyState == 4){
			var out = request.responseText;		// enable JS_DEBUG to see the response
			var parsedOut = JSON.parse(out);
			graphData = parsedOut.data;
			graphLegend = parsedOut.meta.legend;
			callback();
		}
	}
	
	var sources = ARR_SOURCES[0];
	for (var i = 1; i < ARR_SOURCES.length; i++) {
		sources += ":"+ARR_SOURCES[i];
	}
	
	// mode, var, profile, sources, time
	request.open("GET", "php/async/graphCreate.php?var="+ARR_GRAPH_VARS[currentVar]+"&mode=image&profile="+PROFILE+"&sources="+sources+"&time="+timestampBgn+":"+timestampEnd, true);
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
function setGraphCenter(newCenter) {
	timestampBgn = newCenter - ARR_RESOLUTION[resolutionPtr] * 1800;
	timestampEnd = newCenter + ARR_RESOLUTION[resolutionPtr] * 1800;
	
	if (timestampEnd > Utility.getCurrentTimestamp()) {
		timestampEnd = Utility.getCurrentTimestamp();
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

/* ============== */
/* SET RESOLUTION */
/* ============== */
function setResolution (type, value) {
	var list = document.getElementById("DisplayResolutionList").getElementsByTagName("a");
	
	var i;
	for(i = 0; i < list.length; i++) {						// Loop resolution list
		if(list[i].className == "list-group-item active") {	// Find the previously selected resolution
			list[i].className = "";							// Unselect it
			break;											// Remember it's index
		}
	}
	
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
	
	acquireGraphData(updateGraph);							// Update stuff
}

/* =============== */
/* GRAPH MOVE STEP */
/* =============== */
function graphMoveStep(direction) {
	if(direction < 0) {
		setGraphCenter(Graph.curTime1 - ARR_RESOLUTION[resolutionPtr] * 1800);
	}
	else if (direction == 0) {
		setGraphCenter(Utility.getCurrentTimestamp());
	}
	else {
		setGraphCenter(Graph.curTime1 + ARR_RESOLUTION[resolutionPtr] * 1800);
	}
	
	acquireGraphData(updateGraph);
}

/* ================== */
/* TIME WINDOW HANDLE */
/* ================== */
function timeWindowHandle() {
	var month = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

	var doc = document.getElementById("TimePickerDisplay");
	doc.readonly = "false";
	
	//doc.value = "<b>Time selected:</b> ";
	
	var d = new Date(Graph.curTime1 * 1000);
	var str = ('0'+d.getDate()).slice(-2)+" "+month[d.getMonth()]+" "+d.getFullYear()+" "+('0'+d.getHours()).slice(-2)+":"+('0'+d.getMinutes()).slice(-2);
	doc.value = str;
	
	if(Graph.interval) {
		d = new Date(Graph.curTime2 * 1000);
		str = str = ('0'+d.getDate()).slice(-2)+" "+month[d.getMonth()]+" "+d.getFullYear()+" "+('0'+d.getHours()).slice(-2)+":"+('0'+d.getMinutes()).slice(-2);
		doc.value += " - "+str;
	}
	else {
		var windowSize = timestampEnd - timestampBgn;
		var windowPos  = Graph.curTime1 - timestampBgn;
		var percentage = windowPos / windowSize * 100;
		
		if (percentage < 5 || 95 < percentage) {
			setGraphCenter(Graph.curTime1);
			acquireGraphData(updateGraph);
		}
	}
	
	doc.readonly = "true";
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
function initializeGraph() {
	for(var x = 0; x < graphData.length; x++) {
		graphData[x][0] = new Date(parseInt(graphData[x][0]+"000"));
	}
	
	Graph.init("dygraph", graphData, graphLegend, ARR_GRAPH_NAME[currentVar], computeRenderMode());
	Graph.initCursor(["GraphArea_Cursor1", "GraphArea_Cursor2", "GraphArea_CurSpan"]);
	Graph.initTime(graphData[0][0].getTime()/1000, graphData[graphData.length - 1][0].getTime()/1000);
	
	document.getElementById("dygraph").onmousedown = Graph.moveCursor;
	Graph.registerCallback(timeWindowHandle);
	
	colorizeSources("Sources");
	timeWindowHandle();
	
	updateThumbs();
	
	Graph.updateSourcesVisibility("Sources");
}

/* ============ */
/* UPDATE GRAPH */
/* ============ */
function updateGraph() {
	for(var x = 0; x < graphData.length; x++) {
		graphData[x][0] = new Date(parseInt(graphData[x][0]+"000"));
	}
	
	Graph.update(graphData, ARR_GRAPH_NAME[currentVar], computeRenderMode());
	Graph.initCursor(["GraphArea_Cursor1", "GraphArea_Cursor2", "GraphArea_CurSpan"]);
	Graph.initTime(graphData[0][0].getTime()/1000, graphData[graphData.length - 1][0].getTime()/1000);
	
	timeWindowHandle();
	
	updateThumbs();
	
	Graph.updateSourcesVisibility("Sources");
}

function changeVariable(varPtr) {
	currentVar = varPtr;
	
	//  Change title, update graph
	document.getElementById("ActiveGraphLabel").innerHTML = ARR_GRAPH_NAME[currentVar];
	
	acquireGraphData(updateGraph);
}
