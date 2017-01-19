function collectStatistics() {
	//var timeSpec = Dbqry_parseSelectedTime();
	var timeSpec = Graph.curTime1;
	if (Graph.interval) {
		timeSpec += ":"+Graph.curTime2;
	}
	var srcs = ARR_SOURCES[0];
	for (var i = 1; i < ARR_SOURCES.length; i++) {
		srcs += ":"+ARR_SOURCES[i];
	}
	//var srcs="./";
	
	timeSpec = encodeURIComponent(timeSpec);
	srcs = encodeURIComponent(srcs);
	
	var ajax = Utility.initAjax();
	
	ajax.onreadystatechange = function () {
		if (ajax.readyState == 4) {
			document.getElementById("StatsContent").innerHTML = ajax.responseText;
			
			var txt = "Statistics for: " + Utility.timestampToNiceReadable(Graph.curTime1);
			if(Graph.interval) {
				txt += " - " + Utility.timestampToNiceReadable(Graph.curTime2);
			}
			document.getElementById("StatsContentHeader").innerHTML = txt;
		}
	}
	
	ajax.open("GET", "php/async/stats.php?profile="+PROFILE+"&time="+timeSpec+"&src="+srcs, true);
	ajax.send(null);
}

/**
 *  This function is responsible for (apparently) changing pages.
 *  Note that dygraph library used for rendering graphs is not
 *  happy when it's hidden with display:none attribute. For that
 *  reason, the Graphs page is only moved outside of visible plane.
 *  This thing may break lots of stuff...
 */
function gotoPage(page) {
	//document.getElementById("MainPageGraphs").style.display = "none";
	var gpage = document.getElementById("MainPageGraphs");
	gpage.style.position = "absolute";
	gpage.style.top = -gpage.scrollHeight+"px";	// Moves outside of visible area
	
	document.getElementById("MainPageStats").style.display = "none";
	document.getElementById("MainPageDbqry").style.display = "none";
	document.getElementById("MainPageProfiles").style.display = "none";
	
	if (page == "Graphs") {
		gpage.style.position = "static";	// return back to page
	}
	else document.getElementById("MainPage"+page).style.display = "";
	
	if (page == "Stats") {
		collectStatistics();
	}
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

function lookupGrabWhois(ipaddr) {
	var ajax = Utility.initAjax();
	
	ajax.onreadystatechange = function () {
		if (ajax.readyState == 4) {
			var json = JSON.parse(ajax.responseText);
			
			var modal = document.getElementById("LookupModalContentWhois");
			modal.innerHTML = "";
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
	lookupGrabRvdns(ipaddr);
	lookupGrabWhois(ipaddr);
	lookupGrabGeolc(ipaddr);
}
