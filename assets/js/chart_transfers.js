var chart_tr;

var chartData1 = [];
var AmCharts_path = '/assets/vendors/amcharts/';
var data_path = '/assets/json-data/';


AmCharts.ready(function () {
	// SERIAL CHART
	chart_tr = new AmCharts.AmSerialChart();

	//chart_avg.dataProvider = chartData;
	chart_tr.categoryField = "date";
	
	// data updated event will be fired when chart is first displayed,
	// also when data will be updated. We'll use it to set some
	// initial zoom
	chart_tr.addListener("dataUpdated", zoomChart1);

	// AXES
	// Category
	var categoryAxis = chart_tr.categoryAxis;
	categoryAxis.parseDates = true; // in order char to understand dates, we should set parseDates to true
	categoryAxis.minPeriod = "DD"; // as we have data with minute interval, we have to set "mm" here.
	categoryAxis.gridAlpha = 0.07;
	categoryAxis.axisColor = "#f2f9eb";
	categoryAxis.color = $('body').css('color');
	categoryAxis.gridColor = $('body').css('color');
	


	// Value
	var valueAxis = new AmCharts.ValueAxis();
	valueAxis.gridAlpha = 0.07;
	valueAxis.title = "Transactions per date";
	valueAxis.color = $('body').css('color');
	valueAxis.titleColor = $('body').css('color');
	valueAxis.axisColor = "#f2f9eb";
	valueAxis.gridColor = $('body').css('color');
	chart_tr.addValueAxis(valueAxis);

	// GRAPH
	var graph1 = new AmCharts.AmGraph();
	graph1.type = "smoothedLine"; // try to change it to "column"  line  smoothedLine
	graph1.valueField = "counter";
	graph1.lineAlpha = 1;
	graph1.lineColor = "#6e98f4";
	graph1.fillAlphas = 0.1; // setting fillAlphas to > 0 value makes it area graph
	graph1.balloonText= "<div style='margin:5px; font-size:16px;'>Deposits: <b>[[value]]</b><br>[[category]]</div>";
	graph1.bullet="round";
	
	chart_tr.addGraph(graph1);

	// CURSOR
	var chartCursor = new AmCharts.ChartCursor();
	chartCursor.cursorPosition = "mouse";
	chartCursor.categoryBalloonDateFormat = "DD MMMM YYYY";
	chartCursor.cursorColor = "#3498DB";
	chart_tr.addChartCursor(chartCursor);
	

	// SCROLLBAR
	var chartScrollbar = new AmCharts.ChartScrollbar();
	chartScrollbar.autoGridCount = true;
	chartScrollbar.selectedBackgroundColor = "#888888";
	chartScrollbar.color = "#000000";
	
	
		
	chart_tr.addChartScrollbar(chartScrollbar);
	
	
	// WRITE
	chart_tr.write("chart_transfers");
	
	// generate some random data
	generateChartData1();
});


// generate some random data, quite different range
function generateChartData1() {

	$.getJSON( data_path +"transfers.json", function( data ) {
		$.each( data, function( key, val ) {
			var newDate = new Date(val.date);
			chartData1.push({
				date: newDate,
				counter: val.counter
				
			});
		});
		chart_tr.dataProvider = chartData1;
		chart_tr.validateData();
		
	});
}

// this method is called when chart is first inited as we listen for "dataUpdated" event
function zoomChart1() {
	// different zoom methods can be used - zoomToIndexes, zoomToDates, zoomToCategoryValues
	chart_tr.zoomToIndexes(chartData1.length - 40, chartData1.length - 1);
}
