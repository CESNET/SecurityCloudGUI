var Profile = {
	/**
	 *  @brief Brief
	 *  
	 *  @return Return_Description
	 *  
	 *  @details Details
	 */
	addChannel: function() {
		document.getElementById("ProfilesModalChannels").innerHTML += document.getElementById("ProfilesModalChannelsMacro").innerHTML;
	},
	
			/**
		 *  @brief Brief
		 *  
		 *  @param [in] mode Parameter_Description
		 *  @param [in] profile Parameter_Description
		 *  @return Return_Description
		 *  
		 *  @details Details
		 */
	fillModal: function(mode, profile) {
		var ajax = Utility.initAjax();
		
		ajax.onreadystatechange = function () {
			if (ajax.readyState == 4) {
				document.getElementById("ProfilesModalContent").innerHTML = ajax.responseText;
				
				if (mode == "create") {
					Profile.addChannel();
				}
			}
		}
		
		ajax.open("GET", "php/async/profileModalFill.php?mode="+mode+"&profile="+profile, true);
		ajax.send(null);
	},
	
	/**
	 *  @brief Brief
	 *  
	 *  @return Return_Description
	 *  
	 *  @details Details
	 */
	mergeChannels: function() {
		var result = "";
		var list = document.getElementById("ProfilesModalChannels").getElementsByClassName("channel");
		
		for (var i = 0; i < list.length; i++) {
			if (result != "") {
				result += ";";
			}
			
			var channel = list[i].getElementsByTagName("input");
			result += channel[0].value;
			result += ":"+list[i].getElementsByTagName("textarea")[0].value;
			
			for (var p = 1;  p < channel.length; p++) {
				if(channel[p].checked) {
					result += ":"+channel[p].name;
				}
			}
		}
		
		return result;
	},

	/**
	 *  @brief Brief
	 *  
	 *  @return Return_Description
	 *  
	 *  @details Details
	 */
	creationProcess: function() {
		var name = document.getElementById("ProfilesModalParent").value+"/"+document.getElementById("ProfilesModalName").value;
		var type = document.getElementById("ProfilesModalType").value;
		var chnl = Profile.mergeChannels();
		
		var ajax = Utility.initAjax();
		
		ajax.onreadystatechange = function() {
			if (ajax.readyState == 4) {
				document.getElementById("ProfilesModalContent").innerHTML = ajax.responseText;
				var result = document.getElementById("AsyncQuerryResult").innerHTML;
				
				if (result == "success") {
					location.reload();
				}
			}
		}
		
		ajax.open("GET", "php/async/profileUpdateProcess.php?mode=create&name="+name+"&type="+type+"&channels="+chnl, true);
		ajax.send(null);
	},
	
	/**
	 *  @brief Brief
	 *  
	 *  @return Return_Description
	 *  
	 *  @details Details
	 */
	deletionProcess: function() {
		var name = document.getElementById("ProfileDeleteName").innerHTML;
		var ajax = Utility.initAjax();
		
		ajax.onreadystatechange = function() {
			if (ajax.readyState == 4) {
				document.getElementById("ProfilesModalContent").innerHTML = ajax.responseText;
				var result = document.getElementById("AsyncQuerryResult").innerHTML;
				
				if (result == "success") {
					location.replace("index.php");
				}
			}
		}
		
		ajax.open("GET", "php/async/profileUpdateProcess.php?mode=delete&name="+name, true);
		ajax.send(null);
	}
}