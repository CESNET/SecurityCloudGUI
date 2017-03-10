var Default = {
	setResolution : function() {
		resolutionPtr = 2;
	},
	
	setTimeWindow : function() {
		timestampBgn = Utility.getCurrentTimestamp() - (ARR_RESOLUTION[resolutionPtr] * 3600);
		timestampEnd = Utility.getCurrentTimestamp();
	},
};