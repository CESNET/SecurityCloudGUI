/**
 *  @brief Async function for grabbing graph statistics
 *  
 *  @return Nothing, but "StatsContent" object will be updated with result output
 *  
 *  @details Selects all available channels and collects a rrd based stats for selected time.
 */
function collectStatistics() {
	var timeSpec = Graph.curTime1;
	if (Graph.interval) {
		timeSpec += ":"+Graph.curTime2;
	}
	var srcs = ARR_SOURCES[0];
	for (var i = 1; i < ARR_SOURCES.length; i++) {
		srcs += ":"+ARR_SOURCES[i];
	}
	
	timeSpec = encodeURIComponent(timeSpec);
	srcs = encodeURIComponent(srcs);
	
	var ajax = Utility.initAjax();
	
	ajax.onreadystatechange = function () {
		if (ajax.readyState == 4) {
			document.getElementById("StatsContent").innerHTML = ajax.responseText;
		}
	}
	
	ajax.open("GET", "php/async/stats.php?profile="+PROFILE+"&time="+timeSpec+"&src="+srcs, true);
	ajax.send(null);
}

function resizeGraph() {
	Graph.dygraph.resize();
	Graph.initAreaValues();
	Graph.initCursor(["GraphArea_Cursor1", "GraphArea_Cursor2", "GraphArea_CurSpan"]);
	Graph.initTime(graphData[0][0].getTime()/1000, graphData[graphData.length - 1][0].getTime()/1000);
}

function gotoWindow(id) {
	document.getElementById("WindowWorkbench").style.display = "none";
	document.getElementById("TopbarLinkWorkbench").className = "";
	document.getElementById("WindowProfileManager").style.display = "none";
	document.getElementById("TopbarLinkProfileManager").className = "";
	
	document.getElementById("Window" + id).style.display = "";
	document.getElementById("TopbarLink" + id).className = "active";
	
	selectedWindow = id;
	if (id == "Workbench" && pendingResizeEvent) {
		resizeGraph();
		pendingResizeEvent = false;
	}
}

function toggleStats() {
	var elem = document.getElementById("StatsToggleArea");
	var chvr = document.getElementById("StatsToggleChevron");
	
	if (elem.style.display == "none") {
		elem.style.display = "";
		chvr.className = "glyphicon glyphicon-chevron-up";
		collectStatistics();
	}
	else {
		elem.style.display = "none";
		chvr.className = "glyphicon glyphicon-chevron-down";
	}
}

function statsVisible() {
	return document.getElementById("StatsToggleArea").style.display != "none";
}

/**
 *  This function defines the list of items which will be displayed
 *  to user when he requests the whois info.
 */
function isAcceptableWhoisInfo(infoName) {
	var items = [ "inetnum", "netname", "descr", "admin-c", "mnt-by", "role", "address", "phone", "fax-no", "abuse-mailbox" ];
	
	for(var i = 0; i < items.length; i++) {
		if(items[i] == infoName) return true;
	}
	
	return false;
}

/**
 *  Async grab whois information for selected IP address.
 *  Whois is mostly grabbed from ripe.net
 */
function lookupGrabWhois(ipaddr) {
	var ajax = Utility.initAjax();
	
	ajax.onreadystatechange = function () {
		if (ajax.readyState == 4) {
			var json = JSON.parse(ajax.responseText);
			
			var modal = document.getElementById("LookupModalContentWhois");
			for(var p = 0; p < 2; p++) {
				var size = json.contents.objects.object[p].attributes.attribute.length;
				for(var i = 0; i < size; i++) {
					if(!isAcceptableWhoisInfo(json.contents.objects.object[p].attributes.attribute[i].name)) continue;
					
					modal.innerHTML += json.contents.objects.object[p].attributes.attribute[i].name+"\t";
					if (json.contents.objects.object[p].attributes.attribute[i].name.length >= 8) {
						//modal.innerHTMl += "\t\t";
					}
					else {
						modal.innerHTML += "\t";
					}
					modal.innerHTML += json.contents.objects.object[p].attributes.attribute[i].value;
					modal.innerHTML += "\n";
				}
			}
		}
	}

	ajax.open("GET", "php/async/proxy.php?url="+encodeURIComponent("http://rest.db.ripe.net/search.json?query-string="+ipaddr+"&flags=no-filtering"), true);
	ajax.send();
}

function lookupGrabGeolc(ipaddr) {
	var items	= ["city", "country", "countryCode", "lat", "lon", "region", "regionName"];
	var tabs	= ["\t\t", "\t\t", "\t", "\t\t", "\t\t", "\t\t", "\t"];
	var ajax = Utility.initAjax();
	
	ajax.onreadystatechange = function () {
		if (ajax.readyState == 4) {
			var json = JSON.parse(ajax.responseText);
			
			var modal = document.getElementById("LookupModalContentGeolc");
			modal.innerHTML = "";
			
			for(var i = 0; i < items.length; i++) {
				modal.innerHTML += items[i]+tabs[i]+json.contents[items[i]];//+"\n";
				
				if (i == 2) {
					modal.innerHTML += " <img src='blank.gif' class='flag flag-"+json.contents[items[i]].toLowerCase()+"' />";
				}
				modal.innerHTML += "\n";
			}
		}
	}
	
	ajax.open("GET", "php/async/proxy.php?url="+encodeURIComponent("http://ip-api.com/json/"+ipaddr), true);
	ajax.send(null);
}

function lookupGrabRvdns(ipaddr) {
	var ajax = Utility.initAjax();
	
	ajax.onreadystatechange = function () {
		if (ajax.readyState == 4) {
			document.getElementById("LookupModalContentRvdns").innerHTML = "reversedns\t"+ajax.responseText;
		}
	}
	
	ajax.open("GET", "php/async/reverseDns.php?ip="+ipaddr, true);
	ajax.send(null);
}

function lookupGrab(ipaddr) {
	document.getElementById("LookupModalHeading").innerHTML = ipaddr;
	document.getElementById("LookupModalContentWhois").innerHTML = "";
	document.getElementById("LookupModalContentGeolc").innerHTML = "";
	document.getElementById("LookupModalContentRvdns").innerHTML = "";
	
	lookupGrabRvdns(ipaddr);
	lookupGrabWhois(ipaddr);
	lookupGrabGeolc(ipaddr);
	
	document.getElementById("LookupToNERD").href = "https://nerd.cesnet.cz/nerd/ip/" + ipaddr;
}
