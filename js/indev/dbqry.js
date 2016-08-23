function Dbqry_parseQuerryParameter(tab) {
	/* Time window */
	/*var timeSpec=Graph.curTime1;
	if (Graph.interval) {
		timeSpec = "-T "+timeSpec+"#"+Graph.curTime2;
	}
	else {
		timeSpec = "-t "+timeSpec;
	}*/
	var timeSpec="-t "+Graph.curTime1;
	if (Graph.interval) {
		timeSpec += "#"+Graph.curTime2;
	}
	else {
		timeSpec += "#"+Graph.curTime1;
	}
	
	/* Limit to */
	var limitSel = document.getElementById("Option_LimitTo_"+tab);
	var limitTo = limitSel.options[limitSel.selectedIndex].value;
	
	/* Aggregation */
	var aggreg = "";
	if (document.getElementById("Option_AggregateList_"+tab).value != "") {
		aggreg = "-a "+document.getElementById("Option_AggregateList_"+tab).value;// "-a field1,field2,field3"
	}
	
	/* Order by */
	var orderSel = document.getElementById("Option_OrderBy_"+tab);
	var orderBy = "";
	if(orderSel.options[orderSel.selectedIndex].value != "none") {
		orderBy = "-o "+orderSel.options[orderSel.selectedIndex].value;	// "-o field"
		orderSel = document.getElementById("Option_OrderDirection_"+tab);
		orderBy += orderSel.options[orderSel.selectedIndex].value;	// ""/"#asc"/"#des"
	}
	
	/* Output */
	var outputSel = document.getElementById("Option_OutputFormat_"+tab);
	var output = "--output-format="+outputSel.options[outputSel.selectedIndex].value;// "pretty"/"csv"
	
	if (document.getElementById("Option_OutputNoSummary_"+tab).checked) {
		output += " --no-summary";
	}
	
	var str = timeSpec+" "+limitTo+" "+aggreg+" "+orderBy+" "+output;
	return  str;
}

/**
*	Asynchronous call to 'db-manage.php' which attempts to
*	kill process actually running in given tab.
*/
function Dbqry_stopDbRequest(tab) {
	debugLog("Attempting to kill the request");
	
	var ajaxRequest = Utility.initAjax();
	
	ajaxRequest.onreadystatechange = function(){	// callback
		if(ajaxRequest.readyState == 4){
			var output = ajaxRequest.responseText;
			debugLog("stopDbRequest("+tab+") - db-manage output: "+output);
		}
	}
	
	ajaxRequest.open("GET", "php/db-manage.php?mode=kill&stamp="+USERSTAMP+"&tab="+tab, true);
	ajaxRequest.send();
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
function Dbqry_processDbRequest(tab){
	var ajaxRequest = Globals_initAjax();
	
	var filter;
	if (document.getElementById("Db_Filter_"+tab).value.length >= 1) {
		filter = "-f "+document.getElementById("Db_Filter_"+tab).value;
	}
	else {
		filter = "";
	}
	var opts = Dbqry_parseQuerryParameter(tab);
	
	filter	= encodeURIComponent(filter);
	opts	= encodeURIComponent(opts);
	
	debugLog("processDbRequest(...) - Selected options are: "+opts);
	
	// Create a function that will receive data sent from the server
	ajaxRequest.onreadystatechange = function(){	// callback
		if(ajaxRequest.readyState == 4){
			// TODO
			// TODO
			// TODO
			document.getElementById("Db_Output_"+tab).innerHTML = ajaxRequest.responseText;
			document.getElementById("Db_Submit_ProcessButton_"+tab).style.display = "";
			document.getElementById("Db_Submit_KillButton_"+tab).style.display = "none";
		}
	}
	
			// TODO
			// TODO
	document.getElementById("Db_Submit_ProcessButton_"+tab).style.display = "none";
	document.getElementById("Db_Submit_KillButton_"+tab).style.display = "";
	
	ajaxRequest.open("GET", "php/dbqry/database.php?stamp="+USERSTAMP+"&tab="+tab+"&profile="+PROFILE+"&opts="+opts+"&filter="+filter, true);
	ajaxRequest.send(null);
}