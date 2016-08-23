function gotoPage(page) {
	document.getElementById("MainPageGraphs").style.display = "none";
	document.getElementById("MainPageStats").style.display = "none";
	document.getElementById("MainPageDbqry").style.display = "none";
	
	document.getElementById("MainPage"+page).style.display = "";
}