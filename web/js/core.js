
var Core = {
	initWorkbench : function() {
		gotoWindow("Workbench");
		acquireGraphData(initializeGraph, null);
	},
	
	swapCheck : function () {
		if (selBgn > selEnd) {
			var tmp = selBgn;
			selBgn = selEnd;
			selEnd = selBgn;
		}
		
		if (timestampBgn > timestampEnd) {
			var tmp = timestampBgn;
			timestampBgn = timestampEnd;
			timestampEnd = tmp;
		}
	},
	
	sanityCheck : function() {
		this.swapCheck();
		
		if ((timestampEnd - timestampBgn) != ARR_RESOLUTION[resolutionPtr] * 3600) {
			console.log("resolution mismatch");
			Default.setResolution();
			Default.setTimeWindow();
		}
		
		if (selBgn != -1 && selEnd != -1) {
			if ((timestampBgn > selBgn) || (timestampEnd < selEnd)) {
				this.computeResolution(selBgn, selEnd);
				this.computeTimeWindow(selBgn, selEnd);
			}
		}
	},
	
	initResolution : function(value) {
		var list = document.getElementById("DisplayResolutionList").getElementsByTagName("a");
		list[resolutionPtr].className = "list-group-item active";
		document.getElementById("DisplaySizePrint").innerHTML = list[resolutionPtr].innerHTML;
	},
	
	computeTimeWindow : function() {
		var selCtr = (selBgn + selEnd) / 2;
		var dist = (ARR_RESOLUTION[resolutionPtr] / 2) * 3600;
		
		timestampBgn = selCtr - dist;
		timestampEnd = selCtr + dist;
	},
	
	computeResolution : function() {
		// Find correct resolution
		var dist = (selEnd - selBgn) / 3600; // Size of the window in hours
		for (resolutionPtr = 0; resolutionPtr < ARR_RESOLUTION.length; resolutionPtr++) {
			if (ARR_RESOLUTION[resolutionPtr] > dist) break;
		}
	},
	
	parallelInstance : function () {
		var address = "index.php?profile=" + encodeURI(PROFILE);
		address += "&tbgn=" + timestampBgn;
		address += "&tend=" + timestampEnd;
		address += "&tres=" + resolutionPtr;
		address += "&start=" + Graph.curTime1;
		if (Graph.interval) address += "&end=" + Graph.curTime2;
		
		if (document.getElementById("Dbqry_Filter_1").innerHTML != "")
			address += "&filter=" + encodeURI(document.getElementById("Dbqry_Filter_1").innerHTML);
		
		Utility.openInNewTab(address);
	},
};