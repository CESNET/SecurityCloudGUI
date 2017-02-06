var Utility = {
	/* ===================== */
	/* GET CURRENT TIMESTAMP */
	/* ===================== */
	/* This timestamp is rounded to full five minutes interval and moved five minutes
	to the past simply because current interval is not finished and thus it should not
	be accessible for any querying. */
	getCurrentTimestamp: function() {
		var d = new Date();
		var timestamp = Math.floor(d.getTime() / 1000);
		timestamp -= ((timestamp % 300) + 300);
		return timestamp;
	},

	/**
	*	Browser safe Ajax initialized
	*	This function creates a browser specific
	*	version of Ajax object (ActiveXObject or XMLHttpRequest)
	*	and returns it.
	*	It alerts the user in case the object cannot be created.
	*/
	initAjax: function() {
		var ajaxRequest;
	
		try{			// Opera 8.0+, Firefox, Safari
			ajaxRequest = new XMLHttpRequest();
		} catch (e){	// Internet Explorer Browsers
			try{
				ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e) {
				try{
					ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
				} catch (e){
					// Something went wrong
					alert("Your browser broke!");
					return false;
				}
			}
		}
	
		return ajaxRequest;
	},
	
	// This function is obsolete
	// It was used for formatting the timestamps into
	// at-time format before fdistdump started to
	// accept unix timestamps
	timestampToFdistdump: function(timestamp) {
		var d = new Date(timestamp * 1000);
		return ('0'+d.getDate()).slice(-2)+"."+('0'+d.getMonth()).slice(-2)+"."+d.getFullYear()+" "+('0'+d.getHours()).slice(-2)+":"+('0'+d.getMinutes()).slice(-2);
	},
	
	// Accepts UNIX timestamp
	// Outputs time str in ISO fmt YYYY-MM-DD HH:MM
	timestampToNiceReadable: function (timestamp) {
		var d = new Date(timestamp * 1000);
		return d.getFullYear() + "-" + ('0'+(d.getMonth()+1)).slice(-2) + "-" + ('0'+d.getDate()).slice(-2) + " " +('0'+d.getHours()).slice(-2) + ":" + ('0'+d.getMinutes()).slice(-2);
	},
	
	// Accepts Javascript timestamp
	// Outputs time str in ISO fmt YYYY-MM-DD HH:MM
	JStimestampToNiceReadable: function (timestamp) {
		var d = new Date(timestamp);
		return d.getFullYear() + "-" + ('0'+(d.getMonth()+1)).slice(-2) + "-" + ('0'+d.getDate()).slice(-2) + " " +('0'+d.getHours()).slice(-2) + ":" + ('0'+d.getMinutes()).slice(-2);
	},
	
	// Accepts UNIX timestamp
	// Outputs time in DD Mon YYYY HH:MM format
	// Deprecated because ISO stadard was requested
	_timestampToNiceReadable: function(timestamp) {
		var month = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
		var d = new Date(timestamp * 1000);
		return month[d.getMonth()]+" "+('0'+d.getDate()).slice(-2)+" "+d.getFullYear()+" "+('0'+d.getHours()).slice(-2)+":"+('0'+d.getMinutes()).slice(-2);
	},
	
	// Accepts Javascript timestamp
	// Outputs time in DD Mon YYYY HH:MM format
	// Deprecated because ISO stadard was requested
	_JStimestampToNiceReadable: function(timestamp) {
		var month = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
		var d = new Date(timestamp);
		return month[d.getMonth()]+" "+('0'+d.getDate()).slice(-2)+" "+d.getFullYear()+" "+('0'+d.getHours()).slice(-2)+":"+('0'+d.getMinutes()).slice(-2);
	},
}