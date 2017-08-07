// nfcapd files are always 1 hour behind rrdgraphs.
// i.e.: if you're pointing at 14:00 hours, fdistdump
// will query file ending with 1300. This is ok when
// ipfixcol generates the data, but when using
// replay, those two times are aligned. Computing
// additional offset solves that issue by artificially
// adjusting the timestamp.

function Dbqry_parseSelectedTime() {
	var timeSpec = Graph.curTime1;

	if (HISTORIC_DATA) {
		if (Graph.interval) {
			timeSpec = "-T \"U " + timeSpec + "\"#\"U " + Graph.curTime2 + "\"";
		}
		else {
			timeSpec = "-t \"U "+timeSpec + "\"";
		}
	}
	else {
		if (Graph.interval) {
			timeSpec = "-T " + timeSpec + "#" + Graph.curTime2;
		}
		else {
			timeSpec = "-t "+timeSpec;
		}
	}
	
	return timeSpec;
}

function customTrim(str) {
	return str.replace(/^[,]+|[,]+$/gm, '');
}

function Dbqry_getAggregation(tab) {
	var sel = document.getElementById("Option_AggregateList_" + tab);
	
	var str = "";
	for (var i = 0, L = sel.options.length; i < L; i++) {
		if (sel.options[i].selected) {
			str += "," + sel.options[i].value;
			/*if (i == 0) str = sel.options[i].value;
			else		str += "," + sel.options[i].value;*/
		}
	}
	
	return customTrim(str);
}

function Dbqry_parseQuerryParameter(tab) {
	if (document.getElementById("DbMainOptPicker_"+tab).getElementsByTagName("li")[0].className == "active") {
		/* Time window */
		var timeSpec = Dbqry_parseSelectedTime();
	
		/* Limit to */
		var limitSel = document.getElementById("Option_LimitTo_"+tab);
		var limitTo = limitSel.options[limitSel.selectedIndex].value;
	
		/* Aggregation */
		var aggreg = Dbqry_getAggregation(tab);
		if (aggreg != "")	aggreg = "-a " + aggreg;	// "-a field1,field2,field3"
	
		/* Order by */
		var orderSel = document.getElementById("Option_OrderBy_"+tab);
		var orderBy = "";
		if(orderSel.options[orderSel.selectedIndex].value != "none") {
			orderBy = "-o "+orderSel.options[orderSel.selectedIndex].value;				// "-o field"
			orderSel = document.getElementById("Option_OrderDirection_"+tab);
			orderBy += orderSel.options[orderSel.selectedIndex].value;					// ""/"#asc"/"#dsc"
		}
	
		/* Output */
		var outputSel = document.getElementById("Option_OutputFormat_"+tab);
		var output;
		if (outputSel.options[outputSel.selectedIndex].value == "long") { // See https://github.com/CESNET/SecurityCloudGUI/issues/10
			output = "--output-format=pretty --output-volume-conv=none";
		}
		else {
			output = "--output-format="+outputSel.options[outputSel.selectedIndex].value;// "pretty"/"csv"
		}
	
		if (document.getElementById("Option_OutputNoSummary_"+tab).checked) {
			output += " --output-items=r";
		}
		else {
			output += " --output-items=r,p";
		}
	
		var str = timeSpec+" "+limitTo+" "+aggreg+" "+orderBy+" "+output;
	}
	else {
		var str = document.getElementById("Options_CustomTextarea_"+tab).value;
	}
	
	return str;
}

function Dbqry_selectedChannelsToString(tab) {
	var channels = document.getElementById("Channels_Dbqry_" + tab).getElementsByTagName("input");
	
	var str = "#";
	for (var i = 0; i < channels.length; i++) {
		if (channels[i].checked) {
			if (str == "#")	str = channels[i].name;
			else			str += ":" + channels[i].name;
		}
	}
	
	if (str == "#") str = "";
	
	return str;
}

/**
*	Asynchronous call to 'db-manage.php' which attempts to
*	kill process actually running in given tab.
*/
function Dbqry_stopRequest(tab) {
	var ajaxRequest = Utility.initAjax();
	
	/*ajaxRequest.onreadystatechange = function(){	// callback
		if(ajaxRequest.readyState == 4){
			var output = ajaxRequest.responseText;
			alert(output);
		}
	}*/
	
	ajaxRequest.open("GET", "php/async/dbqry.php?mode=kill&stamp="+USERSTAMP+"&tab="+tab, true);
	ajaxRequest.send();
}

/**
*	This is the function called automatically from the point when database
*	query is called till it ends. This function reads the progress file,
*	updates the progress bar and that's basically it.
*/
var intervalHandle;
function Dbqry_trackProgress(tab) {
	var ajax = Utility.initAjax();
	
	ajax.onreadystatechange = function() {
		if(ajax.readyState == 4) {
			var data = JSON.parse(ajax.responseText);
			var progress = document.getElementById("Dbqry_ProgressBar_"+tab);
			progress.innerHTML = data.total+"%";
			progress.style.width = data.total+"%";
		}
	}
	
	ajax.open("GET", "php/async/readProgress.php?mode=read&userstamp="+USERSTAMP+"&tab="+tab+"&profile="+PROFILE+"&nocache="+new Date().getTime(), true);
	ajax.send();
}

/**
*	Merges all arguments into a single string
*	Calls db program, catches and prints the output
*	into the DOM object with id "output"
*
*	Should work in all major browsers
*	This is a non blocking call, it fills the DOM
*	object when the data area available (webpage
*	should not be refreshed though).
*/
function Dbqry_processRequest(tab){
	var ajaxRequest = Utility.initAjax();
	
	// *** Read the filter texteara ***
	var filter;
	if (document.getElementById("Dbqry_Filter_"+tab).value.length >= 1) {
		filter = document.getElementById("Dbqry_Filter_"+tab).value;
	}
	else {
		filter = "";
	}
	
	// *** Read the options ***
	var opts = Dbqry_parseQuerryParameter(tab);
	
	var srcs = Dbqry_selectedChannelsToString(tab);
	
	// *** Encode everything into URL friendly format ***
	var profile = encodeURIComponent(PROFILE);
		filter	= encodeURIComponent(filter);
		opts	= encodeURIComponent(opts);
		srcs	= encodeURIComponent(srcs);
	
	// *** Register ajax callback ***
	ajaxRequest.onreadystatechange = function(){								// callback
		if(ajaxRequest.readyState == 4){
			// *** Print the dbqry output ***
			document.getElementById("Dbqry_Output_"+tab).innerHTML = ajaxRequest.responseText;
			
			// *** Change the caption of Kill button to Progress button ***
			document.getElementById("Dbqry_ProcessButton_"+tab).style.display = "";
			document.getElementById("Dbqry_StopButton_"+tab).style.display = "none";
			
			// *** Kill the progress bar *** 
			progress.style.display="none";
			window.clearInterval(intervalHandle);
			
			// *** Clear the auxiliary progress bar file
			var ajax = Utility.initAjax();
			ajax.open("GET", "php/async/readProgress.php?mode=delete&userstamp="+USERSTAMP+"&tab="+tab+"&profile="+profile+"&nocache="+new Date().getTime(), true);
			ajax.send(null);
		}
	}
	
	// *** Change the caption of Process button to Kill button ***
	document.getElementById("Dbqry_ProcessButton_"+tab).style.display = "none";
	document.getElementById("Dbqry_StopButton_"+tab).style.display = "";

	// *** Initialize the progress bar ***
	var progress = document.getElementById("Dbqry_ProgressBar_"+tab);
	progress.innerHTML		= "0%";
	progress.style.width	= "0%";
	progress.style.display	= "";
	
	// *** Make async call to progress bar update and dbqry request
	intervalHandle = window.setInterval(Dbqry_trackProgress, 1000, tab);
	ajaxRequest.open("GET", "php/async/dbqry.php?mode=exec&stamp="+USERSTAMP+"&tab="+tab+"&profile="+profile+"&opts="+opts+"&filter="+filter+"&src="+srcs, true);
	ajaxRequest.send(null);
}
