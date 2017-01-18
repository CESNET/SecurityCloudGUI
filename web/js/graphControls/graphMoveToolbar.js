// NOTE: SECONDS_PER_HOUR are defined elsewhere(graphManagement.js)
var backupCursor1, backupCursor2, backupInterval;

/**
 *  Makes copies of the cursor which then will be
 *  used by the updateGraph method if the overridePosition
 *  is set to false
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

/**
 *  If the right boundaries were broken, fix it
 *  by setting timestampEnd to the value of the boundary
 *  and aligning right side of the selected interval with
 *  the timestampEnd (if there's no interval, numbers work
 *  such the main cursor itself is aligned).
 */
function graphMoveAdjust() {
	var END = Utility.getCurrentTimestamp();
	
	if (timestampEnd > END) {
		var offset = timestampEnd - END;
		timestampEnd = END;
		timestampBgn -= offset;
		
		offset = backupCursor2 - backupCursor1;
		backupCursor2 = timestampEnd;
		backupCursor1 = backupCursor2 - offset;
	}
}

/**
 *  This function is called when some of the quick
 *  move buttons on the toolbar is pressed. This
 *  computes new boundaries and cursor positions and calls
 *  graph update.
 */
function graphMoveStep(direction) {
	var offset;
	backupCursors();
	
	// If we go to the end, cursor (primary left, right in case of interval) has to be aligned with end
	if (direction < 0)			offset = -ARR_RESOLUTION[resolutionPtr] * SECONDS_PER_HOUR;
	else if (direction == 0)	offset = Utility.getCurrentTimestamp() - backupCursor2;
	else 						offset = ARR_RESOLUTION[resolutionPtr] * SECONDS_PER_HOUR;
	
	timestampBgn	+= offset;
	timestampEnd	+= offset;
	backupCursor1	+= offset;
	backupCursor2	+= offset;
	
	graphMoveAdjust();
	acquireGraphData(updateGraph, false);
}