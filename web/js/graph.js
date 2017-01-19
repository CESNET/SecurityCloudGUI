/* COPY PASTE THESE ELEMENTS: */
/*
	<div id="genericGraphWrapper" style="left: 0px; position:relative; width: 100%; height: 500px;">
		<div id="GraphArea" style="position: absolute; width: 1000px; height: 500px;">
			<div id="GraphArea_Cursor1"></div>
			<div id="GraphArea_CurSpan"></div>
			<div id="GraphArea_Cursor2"></div>
		</div>
		<div id="dygraph" style="width: 100%; height: 500px;"></div>
	</div>
*/
/* AT DOCUMENT READY, CALL THESE FUNCTIONS: */
/*
	Graph.createGraph("dygraph", graphData, graphLegend, graphTitle, 0);
	Graph.initCursor(["GraphArea_Cursor1", "GraphArea_Cursor2", "GraphArea_CurSpan"]);
	Graph.initTime(graphTimeBegin, graphTimeEnd);
	document.getElementById("dygraph").onmousedown = Graph.moveCursor;
	
	$(window).resize(function(){
		Graph.initCursor(["GraphArea_Cursor1", "GraphArea_Cursor2", "GraphArea_CurSpan"]);
		Graph.initTime(graphTimeBegin, graphTimeEnd);
	});
*/

var Graph = {
	cursor1: null,
	cursor2: null,
	curspan: null,
	dygraph: null,
	
	interval: false,
	curTime1: 0,
	curTime2: 0,
	beginTime: 0,
	stepTime: 0,

	area: null,
	rows: 0,
	ppr: 0,							// Pixels per row
	sources: null,
	timeHandle: null,
	
	/* ======= */
	/* PRIVATE */
	/* ======= */
	generateColours: function(n) {
		var colours = [];
	
		if (n <= 10) {
			var step = 360 / n;
		
				for(var i = 0; i < n; i++) {
					colours[i] =  "hsl("+(step*i)+", 75%, 50%)";
				}
			}
			else if (n <= 24) {
			}
			
		return colours;
	},
	
	rowToTime: function(cursor, row) {
		if(cursor == this.cursor1) {
			this.curTime1 = this.beginTime + this.stepTime * row;
		}
		else if (cursor == this.cursor2) {
			this.curTime2 = this.beginTime + this.stepTime * row;
		}
	},
	
	/**
	*	Private function for positioning the cursor.
	*
	*	\pre initCursor() was called
	*/
	placeCursor: function(cursor, row) {
		cursor.style.left = (this.area.x + row * this.ppr)+"px";
		
		Graph.rowToTime(cursor, row);
	},
	
	timeToRow: function (cursor, time) {
		var row = Math.floor ((time - this.beginTime) / this.stepTime);
		
		Graph.placeCursor(cursor, row);
	},
	
	initAreaValues: function () {
		this.area	= this.dygraph.getArea();
		this.rows	= this.dygraph.numRows();
		this.ppr	= this.area.w / (this.rows-1);
	},
	/* ============== */
	/* END OF PRIVATE */
	/* ============== */
	
	/* ============== */
	/* INITIALIZATION */
	/* ============== */
	/**
	*	Creates a dygraph inside <div> element with id=canvasDomId.
	*	
	*	\param Id of destination DOM element
	*	\param Requires data as array in form [ [ timestampBEGIN, series1, ..., seriesN ], [ timestampBEGIN+STEP, series1, ..., seriesN ], ..., [ timestampEND, series1, ..., seriesN ] ]
	*	\param Legend is an array in form ["series1", "series2", "...", "seriesN" ] (the x-axis label will be automatically prepended)
	*	\param Simple string. Y-axis label
	*	\param Associative array. Keys: "type" and "style". Values are booleans. If "type" is 1, graph will be stacked, 0 comparative. "style" set to 1 creates areas, 0 lines.
	*/
	init: function(canvasDomId, gData, gLegend, gTitle, render) {
		this.sources = gLegend;
		gLegend.unshift("x");
		
		var options = {
			colors: this.generateColours(gLegend.length - 1),
			labels: gLegend,
			legend: 'always',
			ylabel: gTitle,
			axes : {
				y : { axisLabelWidth : 70,/* drawAxis: false, drawGrid: false */},
				x : { /*axisLabelWidth : 0, drawAxis: false, drawGrid: false,*/ 
					valueFormatter: function(d) {
						return Utility.JStimestampToNiceReadable(d);
					},
				},
			},
			stackedGraph: render["type"],
			fillGraph: render["style"],
			labelsKMG2: true,		// Kilo, Mega, Giga notations
			labelsUTC: !USE_LOCAL_TIME,		// Hopefully, it'll fix the off-by-one hour
			highlightCircleSize: 5,
			panEdgeFraction: 0.1,
			interactionModel: {}
		};
		
		this.dygraph = new Dygraph(document.getElementById(canvasDomId), gData, options);
		
		Graph.initAreaValues();
	},
	
	/**
	*	Initializes all variables regarding graph marker and cursor stuff.
	*	All parameters are id's of respective DOMs.
	*
	*	NOTE: This function MUST be called each time the graph is resized by ANY event! If your graph has dynamic width/height and the browser window is resized, this function must be called!
	*	Like this:
		$(window).resize(function(){
			Graph.initCursor(["GraphArea_Cursor1", "GraphArea_Cursor2", "GraphArea_CurSpan"]);
		});
	*
	*	\pre initTime() was called
	*/
	initCursor: function(arr_cursorDomIds) {
		this.cursor1 = document.getElementById(arr_cursorDomIds[0]);	
		this.cursor2 = document.getElementById(arr_cursorDomIds[1]);	
		this.curspan = document.getElementById(arr_cursorDomIds[2]);	
		
		this.cursor1.style.position = "absolute";
		this.cursor1.style.height = this.area.h+"px";
		this.cursor1.style.width	 = "2px";
		this.cursor1.style.background = "black";
		
		this.interval = false;
		
		// Rest
		this.cursor2.style.position = "absolute";
		this.cursor2.style.height = this.area.h+"px";
		this.cursor2.style.width	 = "2px";
		this.cursor2.style.background = "black";
		this.cursor2.style.display = "none";
		
		this.curspan.style.position = "absolute";
		this.curspan.style.height = this.area.h+"px";
		this.curspan.style.background = "#00FF00";
		this.curspan.style.opacity = "0.2";
		this.curspan.style.display = "none";
		
		this.placeCursor(this.cursor1, this.rows/2);
	},
	
	/**
	*	Initializes variables for computing timestamps of timeslots (since default dygraph functions for it are broken).
	*	begin is the FIRST timestamp in data array. end is the LAST timestamp in the data array.
	*	Step between timeslots is automatically computed.
	*	The first cursor is then set to default position and the proper time selector is updated.
	*	This function can be called any number of times when the view should be reset.
	*
	*	\pre Cursors are initialized
	*/
	initTime: function (begin, end, centerize) {
		this.beginTime = begin;
		this.stepTime = (end-begin) / (this.rows-1);
	},
	
	/**
	*	Register function which will be called each time the cursor and markers
	*	are updates. This can come in handy when updating time windows.
	*/
	registerCallback: function(foo) {
		this.timeHandle = foo;
	},
	/* =========== */
	/* END OF INIT */
	/* =========== */
	
	/* =========== */
	/* UPDATE FUNC */
	/* =========== */
	update: function(newData, newTitle, render) {
		this.dygraph.updateOptions( {file: newData, ylabel: newTitle, stackedGraph: render["type"], fillGraph: render["style"]} );
		
		Graph.initAreaValues();
	},
	
	/**
	 *  Set all cursors to their correct positions based on time
	 *  computed by the GUI.
	 *  Generally when something is moved, it's position within the
	 *  relative graph area should remain the same, but if any
	 *  correction happened, this would allow that correction to happen.
	 */
	updateCursor: function (newTimeA, newTimeB, interval) {
		Graph.timeToRow(this.cursor1, newTimeA);
		this.interval = interval;
		
		if (this.interval) {
			Graph.timeToRow(this.curspan, newTimeA);
			Graph.timeToRow(this.cursor2, newTimeB);
		}
	},
	
	/**
	*	Change rendering options of the graph.
	*
	*	\prms Works the exact same way as render parameter in createGraph()
	*
	*	\see createGraph()
	*/
	updateRenderMode: function(prms) {
		this.dygraph.updateOptions( { stackedGraph: prms["type"], fillGraph: prms["style"]} );
	},
	
	/**
	*	Toggles sources between visible/hidden states. This function reads all
	*	<input> tags inside wrapping <div> with id=srcDomId. <input> should be
	*	all checkboxes. Based on <input> checked status, the source is toggled.
	*	NOTE: Sources are identified by the index in <input> list. Make sure the
	*	wrapping div contains ONLY valid checkboxes and also make sure they are
	*	in the same order as in gLedend array in createGraph() parameter.
	*
	*	\pre createGraph() was called.
	*/
	updateSourcesVisibility: function(srcDomId) {
		var srcs = document.getElementById(srcDomId).getElementsByTagName("input");
		
		for(var i = 0; i < srcs.length; i++) {
			this.dygraph.setVisibility(i, srcs[i].checked);
		}
	},
	/* ============= */
	/* END OF UPDATE */
	/* ============= */
	
	/**
	*	This function must be registered as onmousedown event on the graph DOM
	*	element. For example, if graph was created in <div> with id "dygraph"
	*	this call will do the job: document.getElementById("dygraph").onmousedown = Graph.moveCursor;
	*
	*	\pre Graph was created
	*/
	moveCursor: function() {
		var row = Graph.dygraph.getSelection();									// Get closest row to actual mouse position
		
		Graph.placeCursor(Graph.cursor1, row);									// Place the first cursor to this position
		Graph.interval = false;													// Assume that user only selects one time slot
		
		var caller = this;														// Remember pointer to object that called this function
		var swapped = false;													// Auxiliary handle for right-to-left selection
		
		caller.onmousemove = function(eventE) {									// Register drag event function
			Graph.interval = true;												// At this point, user selects area
			Graph.cursor2.style.display	= "";									// Display hidden elements
			Graph.curspan.style.display	= "";									// Display hidden elements
			
			var row2 = Graph.dygraph.getSelection();							// Get closest row to actual mouse position
			
			if((row2 < row && !swapped) || (row2 > row && swapped)) {			// Check whether user is moving mouse to the left from the first cursor or to the right
				swapped = !swapped;												// Set the flag
			}
			
			Graph.placeCursor(Graph.curspan, swapped ? row2 : row);				// Show the area highlight span
			Graph.curspan.style.width = (Math.abs(row2 - row) * Graph.ppr) + "px";// Show the area highlight span
			
			Graph.placeCursor(Graph.cursor1, swapped ? row2 : row);				// Display cursors at the right positions
			Graph.placeCursor(Graph.cursor2, swapped ? row : row2);				// Display cursors at the right positions
		}
		caller.onmouseup = function() {											// This stops the cursor movement
			if(!Graph.interval || Graph.curTime1 == Graph.curTime2) {			// In case user only clicked
				Graph.cursor2.style.display = "none";							// Make sure only the first cursor is visible
				Graph.curspan.style.display = "none";							// Make sure only the first cursor is visible
				Graph.curTime2 = -1;											// And unset second time selector
				Graph.interval = false;
			}
			
			if(Graph.timeHandle != null) {										// If the user set the updateTime callback
				Graph.timeHandle();												// Call it
			}
			
			caller.onmousemove = null;											// And stop the function by unseting onmousemove callback
		}
	},
};
