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

/**
 *  Centers the graph around the new center. If the end of time was reached, properly
 *  adjustment takes place. This function returns TRUE if the dygraph should be updated
 *  and FALSE otherwise. Note that FALSE is only returned when timestampEnd was already
 *  aligned with the end of the time and new centering would cause nothing.
 */
function setGraphCenter(newCenter) {
	var tmp = timestampEnd;
	timestampBgn = newCenter - ARR_RESOLUTION[resolutionPtr] * SECONDS_PER_HOUR / 2;
	timestampEnd = newCenter + ARR_RESOLUTION[resolutionPtr] * SECONDS_PER_HOUR / 2;
	return adjustGraphCenter(tmp);
}