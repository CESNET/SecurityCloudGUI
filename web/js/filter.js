var Filter = {
	insertFilter(filterText) {
		document.getElementById("Dbqry_Filter_1").value = filterText;
	}, 
	
	redrawFilterList : function(list) {
		var dom = document.getElementById("SavedFilterList");
		
		dom.innerHTML = "";
		for (var i = 0; i < list.length; i++) {
			dom.innerHTML += "<li><a href='#filter' onclick='Filter.insertFilter(\"" + list[i].filter + "\")'>" + list[i].name + "</a></li>";
		}
	},
	
	loadFilterList : function() {
		var ajax = Utility.initAjax();
	
		ajax.onreadystatechange = function() {
			if(ajax.readyState == 4) {
				var data = JSON.parse(ajax.responseText);
				
				if (data.status != "success") {
					alert("Could not load saved filters!\n" + data.message);
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
					alert("Could not save filter!\n" + data.message);
					return;
				}
				
				Filter.loadFilterList();
			}
		}
		
		ajax.open("GET", "php/async/filterManager.php?action=save&name=" + name + "&filter=" + filter + "&nocache=" + new Date().getTime(), true);
		ajax.send();
	}
}