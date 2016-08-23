/*
List of available methods:
	> void			Time_Windows_printTimeWindowInput		(string str);
	> halt			Time_Windows_errorParsing				(string-date twin);
	> void			Time_Windows_changeCenter				(int center);
	> int			Time_Windows_getCurrentTimestamp		();
	> string-date 	Time_Windows_timestampToReadable		(int timestamp);
	> int			Time_Windows_readableToTimestamp		(string-date twin);
	> void			Time_Windows_processTimeWindowInput		(void);
	> void			Time_Windows_initTimeWindowInput		(void);
	> void 			Time_Windows_displayTimeWindowInput		(void);
	> void			Time_Windows_goTo						(string-id which);	// TODO: docs
*/

/**
*	Prints text to the Stats_Header_Text DOM.
*	Can be used for time window displaying
*	or error reporting.
*/
function Time_Windows_printTimeWindowInput(str) {
	var header = document.getElementById("Stats_Header_Text");
	header.innerHTML = str;
}

/**
*	This will print error to the Stats_Header_Text
*	and also halts all consequent scripts (desired).
*/
function Time_Windows_errorParsing(twin) {
	// Use print method as the debugger and error report
	Time_Windows_printTimeWindowInput(twin+" is not a valid time!");
	
	// This stops execution of all related scripts
	document.getElementById("Fail_Hook_Do_Not_Create_This_Element").value = "failed";
}

/**
*	Changes the center of the graph view.
*	Sets the view boundaries (Indicator compatible)
*	respecting timeResolution settings.
*	Calls Graph_updateAll() and Graph_Indicator_updatePosition();
*/
function Time_Windows_changeCenter(center) {
	//selectedTimeA = center;
	//selectedTimeB = center;
	graphTimeStart = selectedTimeA - (timeResolution/2 * 24 * 3600);
	graphTimeEnd = selectedTimeA + (timeResolution/2 * 24 * 3600);
	
	// Update texts
	
	Graph_updateAll();
	Graph_Indicator_updatePosition();
}

/**
*	Returns current time as a unix timestamp
*	rounded to five minute intervals.
*	Timestamp is in seconds.
*/
function Time_Windows_getCurrentTimestamp() {
	var d = new Date();
	var timestamp = Math.floor(d.getTime() / 1000);
	timestamp -= timestamp % 300;
	return timestamp;
}

/**
*	Converts the unix timestamp (in seconds) into
*	human readable format DD-MM-YY HH:MM
*	If timestamp is not rounded to five minute
*	intervals, it is rounded.
*
*	\return String with human readable date
*/
function Time_Windows_timestampToReadable(timestamp) {
	var d = new Date(timestamp * 1000);			// Conversion from seconds to miliseconds
	
	// ('0'+time).slice(-2) will always print two digits (using leading zero if necessary)
	var str = ('0'+d.getDate()).slice(-2)+"-"+('0'+(d.getMonth()+1)).slice(-2)+"-"+('0'+d.getFullYear()).slice(-2)+" "+('0'+d.getHours()).slice(-2)+":"+('0'+(d.getMinutes() - d.getMinutes() % 5)).slice(-2);
	
	return str;
}

/**
*	Scans the string given whether it is the valid DD-MM-YY HH:MM format
*	It does perform checks whether the time are valid numbers (excluding day)
*	Returns time in form of seconds from start of the epoch.
*	Input time has to have minutes rounded to multiples of 5.
*/
function Time_Windows_readableToTimestamp(twin) {
	// Perform basic checking if the time is set properly, otherwise fail miserably
	if (twin.length != 14 || twin[8] != " " || twin[2] != "-" || twin[5] != "-" || twin[11] != ":" || (twin[13] != '0' && twin[13] != '5')) {
		Time_Windows_errorParsing(twin);
	}
	// Valid characters checks
	for (var i = 0; i < twin.length; i++) {
		if (!(('0' <= twin[i] && twin[i] <= '9') || twin[i] == '-' || twin[i] == ' ' || twin[i] == ':')) {
			Time_Windows_errorParsing(twin);
		}
	}
	
	var slices	= twin.split(" ");
	var datum	= slices[0].split("-");
	var cas		= slices[1].split(":");
	
	// Valid number checks
	if(!((0 <= cas[0] && cas[0] <= 23) && (0 <= cas[1] && cas[1] <= 59) && (1 <= datum[1] && datum[1] <= 12) && (1 <= datum[0] && datum[0] <= 31) && (0 <= datum[2] && datum[2] <= 99))) {
		Time_Windows_errorParsing(twin);
	}
	
	debugLog("Date: "+(datum[2]-1+2001).toString()+"-"+(datum[1]).toString()+"-"+datum[0]+" Time: "+cas[0]+":"+cas[1]);
	var d = new Date(datum[2]-1+2001, datum[1]-1, datum[0], cas[0], cas[1], 0, 0);
	debugLog("Timestamp: "+d.getTime());
	
	var timestamp = Math.floor(d.getTime()/1000);		// Returns seconds from start of epoch
	
	if(timestamp > Time_Windows_getCurrentTimestamp()) {
		Time_Windows_errorParsing(twin);
	}
	
	return timestamp;
}

/**
*	This function is triggered by submiting the time window forms.
*	Reads TimeWindowA/B contents, parses them, prints them to
*	Stats_Header_Text and stores them in the global time window
*	variables selectedTimeA/B.
*	Depending on value of timeWindowInterval it uses the second
*	window.
*/
function Time_Windows_processTimeWindowInput() {
	debugLog("Time_Windows_processTime_WindowInput() triggered");
	
	var slotA = document.getElementById("TimeWindowA");
	var slotB = document.getElementById("TimeWindowB");
	
	slotA = slotA.value;
	
	// Parse and propagate time up
	selectedTimeA = Time_Windows_readableToTimestamp(slotA);
	
	if (timeWindowInterval) {	// In this case, slotB is used
		slotB = slotB.value;
		Time_Windows_printTimeWindowInput("Statistics timeslot "+slotA+" - "+slotB);
		
		selectedTimeB = Time_Windows_readableToTimestamp(slotB);
	}
	else {						// Only slotA is used
		Time_Windows_printTimeWindowInput("Statistics timeslot "+slotA);
		selectedTimeB = selectedTimeA;
	}
	
	debugLog("Time_Windows_processTime_WindowInput() ended");
}

/**
*	This function is called onload of the page. Fills the
*	input texts for time windows with the current time.
*	Also sets the content text of the Statistics header
*	so the user can see what timeslot it displays.
*/
function Time_Windows_initTimeWindowInput() {
	debugLog("Initializing Time Window Input");
	var d = new Date();
	
	var slotA = document.getElementById("TimeWindowA");
	
	slotA.value = Time_Windows_timestampToReadable(selectedTimeA);
	debugLog(slotA.value+" is for timestamp "+selectedTimeA);
	
	Time_Windows_printTimeWindowInput("Statistics timeslot "+slotA.value);
}

/**
*	Displays/Hides a second textbox for entering
*	time window specifications.
*	Also displays proper indicators
*/
function Time_Windows_displayTimeWindowInput() {
	debugLog("reached");
	var sel = document.getElementById("Time_WindowSelector");
	
	if (sel.selectedIndex == 1) {
		document.getElementById("HiddenTimeWindow").style.display = "inline";
		document.getElementById("TimeWindowB").value = document.getElementById("TimeWindowA").value;
		selectedTimeB = selectedTimeA;
		debugLog("It should be now visible");
		document.getElementById("Graph_Indicator_Span").style.display = "block";
		document.getElementById("Graph_Indicator_Stop").style.display = "block";
		debugLog("It should be now visible");
	}
	else {
		document.getElementById("HiddenTimeWindow").style.display = "none";
		
		document.getElementById("Graph_Indicator_Span").style.width = "0px";
		document.getElementById("Graph_Indicator_Span").style.display = "none";
		document.getElementById("Graph_Indicator_Stop").style.left = "-100px";
		document.getElementById("Graph_Indicator_Stop").style.display = "none";
	}
	
	timeWindowInterval = !timeWindowInterval;
	debugLog("valud of the timeWindowInterval = "+timeWindowInterval);
}

/**
*	Changes actual time slot by a specified amount (last, next, prev, forw, bakw)
*	Updates the Indicator position, updates the statistics
*/
function Time_Windows_goTo(which) {
	if(which == "last") {
		selectedTimeA = Time_Windows_getCurrentTimestamp();
		selectedTimeB = selectedTimeB;
	}
	else if(which == "next") {
		// Size of a timeslot should be computed using timeResolution
		selectedTimeA += 300;
		selectedTimeB += 300;
	}
	else if(which == "prev") {
		selectedTimeA -= 300;
		selectedTimeB -= 300;
	}
	else if(which == "forw") {
		selectedTimeA += 3600 * 24;
		selectedTimeB += 3600 * 24;
	}
	else if(which == "bakw") {
		selectedTimeA -= 3600 * 24;
		selectedTimeB -= 3600 * 24;
	}
	
	Graph_Indicator_updatePosition();
	
	document.getElementById("TimeWindowA").value = Time_Windows_timestampToReadable(selectedTimeA);
	document.getElementById("TimeWindowB").value = Time_Windows_timestampToReadable(selectedTimeB);
	
	Time_Windows_processTimeWindowInput();
	// TODO: stats
}

function Time_Windows_changeDisplaySize(that) {
	timeResolution = that.options[that.selectedIndex].value;
	Time_Windows_changeCenter();
}
