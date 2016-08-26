var Transactions = {
	init: function() {
		var ajax = Utility.initAjax();
		
		/*ajax.onreadystatechange = function () {
			if (ajax.readyState == 4) {
				var out = ajax.responseText;
				alert(out);
			}
		}*/
		
		ajax.open("GET", "php/async/transactions.php?mode=init&stamp="+USERSTAMP, true);
		ajax.send();
	},

	deinit: function() {
		var ajax = Utility.initAjax();
		
		/* ajax.onreadystatechange = function () {
			if (ajax.readyState == 4) {
				var out = ajax.responseText();
				alert(out);
			}
		} */
		
		ajax.open("GET", "php/async/transactions.php?mode=deinit&stamp="+USERSTAMP, true);
		ajax.send();
	}
}