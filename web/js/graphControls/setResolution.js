/**
 *  This function is called by setResolution(...).
 *  It's purpose is to find currently selected resolution,
 *  unselect it and return its index.
 */
function unselectResolution(list) {
	for (var i = 0; i < list.length; i++) {
		if (list[i].className == "list-group-item active") {
			list[i].className = "";
			return i;
		}
	}
	
	return -1;
}

/**
 *  Based on type ("relative"|"absolute"), this function
 *  will either add value to the current index (relative)
 *  or make the value to be new index (absolute). Graph
 *  is updated afterwards.
 */
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