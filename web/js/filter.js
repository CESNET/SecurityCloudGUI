var Filter = {
	insertFilter : function(filterText) {
		document.getElementById("Dbqry_Filter_1").value = filterText;
	}, 
	
	redrawFilterList : function(list) {
		var dom = document.getElementById("SavedFilterList");
		
		dom.innerHTML = "";
		for (var i = 0; i < list.length; i++) {
			dom.innerHTML += "<li class='hz'><a href='#filter' onclick='Filter.insertFilter(\"" + list[i].filter + "\")'>" + list[i].name + "</a> <a href='#' data-toggle='modal' data-target='#DbqryDelFilterModal' data-name='" + list[i].name + "' data-filter='" + list[i].filter + "' align='right'><span class='glyphicon glyphicon-trash' aria-hidden='true'></span></a></li>";
		}
	},
	
	loadFilterList : function() {
		var ajax = Utility.initAjax();
	
		ajax.onreadystatechange = function() {
			if(ajax.readyState == 4) {
				var data = JSON.parse(ajax.responseText);
				
				if (data.status != "success") {
					alert("Could not load saved filters!ERROR: \n" + data.message);
					return;
				}
				
				Filter.redrawFilterList(data.data);
			}
		}
		
		ajax.open("GET", "php/async/filterManager.php?action=load&nocache=" + new Date().getTime(), true);
		ajax.send();
	},
	
	saveFilter : function() {
		var nameDOM = document.getElementById("DbqrySavedFilterName");
		var name = nameDOM.value;
		nameDOM.value = "";
		var filter = document.getElementById("Dbqry_Filter_1").value;
		
		var ajax = Utility.initAjax();
	
		ajax.onreadystatechange = function() {
			if(ajax.readyState == 4) {
				var data = JSON.parse(ajax.responseText);
				
				if (data.status != "success") {
					alert("Could not save filter!\nERROR: " + data.message);
					return;
				}
				
				Filter.loadFilterList();
			}
		}
		
		ajax.open("GET", "php/async/filterManager.php?action=save&name=" + name + "&filter=" + filter + "&nocache=" + new Date().getTime(), true);
		ajax.send();
	},
	
	deleteFilter : function() {
		var name = document.getElementById("DbqryDelFilterModalNameValue").value;
		var filter = document.getElementById("DbqryDelFilterModalFilterValue").value;
		
		var ajax = Utility.initAjax();
	
		ajax.onreadystatechange = function() {
			if(ajax.readyState == 4) {
				var data = JSON.parse(ajax.responseText);
				
				if (data.status != "success") {
					alert("Could not save filter!\nERROR: " + data.message);
					return;
				}
				
				Filter.loadFilterList();
			}
		}
		
		ajax.open("GET", "php/async/filterManager.php?action=delete&name=" + name + "&filter=" + filter + "&nocache=" + new Date().getTime(), true);
		ajax.send();
	}
}