function darkenColor(colorStr) {
	// Defined in dygraph-utils.js
	var color = Dygraph.toRGB_(colorStr);
	color.r = Math.floor((255 + color.r) / 2);
	color.g = Math.floor((255 + color.g) / 2);
	color.b = Math.floor((255 + color.b) / 2);
	return 'rgb(' + color.r + ',' + color.g + ',' + color.b + ')';
  }

  // This function draws bars for a single series. See
  // multiColumnBarPlotter below for a plotter which can draw multi-series
  // bar charts.
  function barChartPlotter(e) {
	var stacked = e.dygraph.getOption("stackedGraph");
	
	console.log(e.color);
	  
	var ctx = e.drawingContext;
	var points = e.points;
	var y_bottom = e.dygraph.toDomYCoord(0);

	//if (stacked)ctx.fillStyle = darkenColor(e.color);
	if (stacked)ctx.fillStyle = e.color;
	else		{
		ctx.beginPath();
		ctx.strokeStyle = e.color;
	}
	
	// Find the minimum separation between x-values.
	// This determines the bar width.
	/*var min_sep = Infinity;
	for (var i = 1; i < points.length; i++) {
		var sep = points[i].canvasx - points[i - 1].canvasx;
		if (sep < min_sep) min_sep = sep;
	}
	var bar_width = min_sep;*/
	var bar_width = points[1].canvasx - points[0].canvasx;
	
	// Do the actual plotting.
	for (var i = 0; i < points.length; i++) {
		var p = points[i];
		var center_x = p.canvasx;

		if (stacked)ctx.fillRect(center_x - bar_width / 2, p.canvasy, bar_width + 1, y_bottom - p.canvasy);
		else {
			if (i > 0) {
				ctx.moveTo(center_x - bar_width / 2, points[i-1].canvasy);
				ctx.lineTo(center_x - bar_width / 2, p.canvasy);
			}
			
			ctx.lineTo(center_x + bar_width / 2, p.canvasy);
			
			if (i < points.length - 1) {
				ctx.lineTo(center_x + bar_width / 2, points[i+1].canvasy);
				ctx.stroke();
			}
		}
	}
 }