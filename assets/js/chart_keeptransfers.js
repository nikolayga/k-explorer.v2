var chart_keeptr;

var chartData4 = [];
var AmCharts_path = '/assets/vendors/amcharts/';
var data_path = '/assets/json-data/';


AmCharts.ready(function () {
	// SERIAL CHART
	chart_keeptr = new AmCharts.AmSerialChart();

	//chart_avg.dataProvider = chartData;
	chart_keeptr.categoryField = "date";
	
	// data updated event will be fired when chart is first displayed,
	// also when data will be updated. We'll use it to set some
	// initial zoom
	chart_keeptr.addListener("dataUpdated", zoomChart4);

	// AXES
	// Category
	var categoryAxis = chart_keeptr.categoryAxis;
	categoryAxis.parseDates = true; // in order char to understand dates, we should set parseDates to true
	categoryAxis.minPeriod = "DD"; // as we have data with minute interval, we have to set "mm" here.
	categoryAxis.gridAlpha = 0.07;
	categoryAxis.axisColor = "#f2f9eb";
	categoryAxis.color = $('body').css('color');
	categoryAxis.gridColor = $('body').css('color');
	


	// Value
	var valueAxis = new AmCharts.ValueAxis();
	valueAxis.gridAlpha = 0.07;
	valueAxis.title = "KEEP per date";
	valueAxis.color = $('body').css('color');
	valueAxis.titleColor = $('body').css('color');
	valueAxis.axisColor = "#f2f9eb";
	valueAxis.gridColor = $('body').css('color');
	chart_keeptr.addValueAxis(valueAxis);

	// GRAPH
	var graph3 = new AmCharts.AmGraph();
	graph3.type = "smoothedLine"; // try to change it to "column"  line  smoothedLine
	graph3.valueField = "counter";
	graph3.lineAlpha = 1;
	graph3.lineColor = "#6e98f4";
	graph3.fillAlphas = 0.1; // setting fillAlphas to > 0 value makes it area graph
	graph3.balloonText= "<div style='margin:5px; font-size:16px;'>KEEP: <b>[[value]]</b><br>[[category]]</div>";
	graph3.bullet="round";
	
	chart_keeptr.addGraph(graph3);

	// CURSOR
	var chartCursor = new AmCharts.ChartCursor();
	chartCursor.cursorPosition = "mouse";
	chartCursor.categoryBalloonDateFormat = "DD MMMM YYYY";
	chartCursor.cursorColor = "#3498DB";
	chart_keeptr.addChartCursor(chartCursor);
	

	// SCROLLBAR
	var chartScrollbar = new AmCharts.ChartScrollbar();
	chartScrollbar.autoGridCount = true;
	chartScrollbar.selectedBackgroundColor = "#888888";
	chartScrollbar.color = "#000000";
	
	
		
	chart_keeptr.addChartScrollbar(chartScrollbar);
	
	
	// WRITE
	chart_keeptr.write("chart_keeptransfers");
	
	// generate some random data
	generateChartData4();
});


// generate some random data, quite different range
function generateChartData4() {

	$.getJSON( data_path +"keeptransfers.json", function( data ) {
		$.each( data, function( key, val ) {
			var newDate = new Date(val.date);
			chartData4.push({
				date: newDate,
				counter: val.counter
				
			});
		});
		chart_keeptr.dataProvider = chartData4;
		chart_keeptr.validateData();
		
	});
}

// this method is called when chart is first inited as we listen for "dataUpdated" event
function zoomChart4() {
	// different zoom methods can be used - zoomToIndexes, zoomToDates, zoomToCategoryValues
	chart_keeptr.zoomToIndexes(chartData4.length - 40, chartData4.length - 1);
}
