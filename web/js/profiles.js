var Profile = {
	/**
	 *  @brief Adds a channel into "Add subprofile" dialog
	 *  
	 *  @return Nothing
	 *  
	 *  @details Dialog for the new channel is appended to the end of the list of the existing channels. Currently known bug: All previously defined channel settings will be reset.
	 */
	addChannel: function() {
		var list = document.getElementById("ProfilesModalChannels").getElementsByClassName("channel");
		var backup = new Array (list.length);
		
		/* Make a copy of every already defined field */
		for (var i = 0; i < list.length; i++) {
			var channel = list[i].getElementsByTagName("input");
			backup[i] = new Array(channel.length + 1);
			
			backup[i][0] = channel[0].value;
			backup[i][1] = list[i].getElementsByTagName("textarea")[0].value;
			for (var p = 1;  p < channel.length; p++) {
				backup[i][p+1] = channel[p].checked;
			}
		}
		
		/* Append a new channel (this will reset all defined fields) */
		document.getElementById("ProfilesModalChannels").innerHTML += document.getElementById("ProfilesModalChannelsMacro").innerHTML;
		
		/* Restore values of fields from backup */
		for (var i = 0; i < list.length; i++) {
			var channel = list[i].getElementsByTagName("input");
			
			channel[0].value = backup[i][0];
			list[i].getElementsByTagName("textarea")[0].value = backup[i][1];
			for (var p = 1;  p < channel.length; p++) {
				channel[p].checked = backup[i][p+1];
			}
		}
	},
	
		/**
		 *  @brief Async call for profile view/create/delete modal content
		 *  
		 *  @param [in] mode "create", "view" or "delete"
		 *  @param [in] profile Profile in question in format /live/path/to/profile
		 *  
		 *  @return Nothing, output is inserted into "ProfileModalContent" element
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
	 *  @brief Creates a string for url passing containing all defined channels
	 *  
	 *  @return Formatted string in format channel_name:channel_filter:src1:...:srcN;channel2_name:....
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
	 *  @brief Async call to create certain profile
	 *  
	 *  @return On success, the page is reloaded with the currently selected profile.
	 *  
	 *  @details Called from "Add subprofile" dialog. Collects all data regarding the new profile, merges them and then calls the routing to add those into the ipfixcol profiles.xml, reloading the configuration via SUGUSR1 signal.
	 */
	creationProcess: function() {
		var name = document.getElementById("ProfilesModalParent").value+"/"+document.getElementById("ProfilesModalName").value;
		var type = document.getElementById("ProfilesModalType").value;
		var chnl = Profile.mergeChannels();
		
		var ajax = Utility.initAjax();
		
		ajax.onreadystatechange = function() {
			if (ajax.readyState == 4) {
				document.getElementById("ProfilesModalResponse").innerHTML = ajax.responseText;
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
	 *  @brief Async call to delete certain profile
	 *  
	 *  @return On success, whole page is reloaded with live profile, otherwise error is printed into the modal dialog
	 *  
	 *  @details On success, reloads the ipfixcol configuration.
	 */
	deletionProcess: function() {
		var name = document.getElementById("ProfileDeleteName").innerHTML;
		var ajax = Utility.initAjax();
		
		ajax.onreadystatechange = function() {
			if (ajax.readyState == 4) {
				document.getElementById("ProfilesModalResponse").innerHTML = ajax.responseText;
				var result = document.getElementById("AsyncQuerryResult").innerHTML;
				
				if (result == "success") {
					location.replace("index.php");
				}
			}
		}
		
		ajax.open("GET", "php/async/profileUpdateProcess.php?mode=delete&name="+name, true);
		ajax.send(null);
	},
	
	/**
	 *  @brief Switches the profiles while keeping the resolution and time window
	 *  
	 *  @return nothing
	 *  
	 *  @details On success, this reloads the whole page, loading the data for another profile
	 *  but keeping the time window and zoom. (Currently not keeping the visualisation settings).
	 */
	changeLocation: function(name) {
		// We have name (path) to profile
		// Now we have to create a link to change location to
		// At this point we only need the timestamps (and the resolution)
		var address = "index.php?profile=" + name;
		address += "&begin=" + timestampBgn;
		address += "&end=" + timestampEnd;
		address += "&res=" + resolutionPtr;
		
		// alert(address);
		location.assign(address);
	},
}
