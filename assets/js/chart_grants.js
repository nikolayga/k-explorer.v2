var data_path = '/assets/json-data/';
$.getJSON( data_path +"grants_stat.json", function( json ) {
	var chart = AmCharts.makeChart("chart_grants", {
	  "type": "pie",
	  "fontSize": 14,
	  "startDuration": 2,
	  "theme": "none",
	  "addClassNames": true,
	  "legend":{
		"position":"bottom",
		"marginRight":50,
		"autoMargins":false,
		"spacing":100,
		"valueWidth":100
	  },
	  "innerRadius": "30%",
	  "defs": {
		"filter": [{
		  "id": "shadow",
		  "width": "200%",
		  "height": "200%",
		  "feOffset": {
			"result": "offOut",
			"in": "SourceAlpha",
			"dx": 0,
			"dy": 0
		  },
		  "feGaussianBlur": {
			"result": "blurOut",
			"in": "offOut",
			"stdDeviation": 5
		  },
		  "feBlend": {
			"in": "SourceGraphic",
			"in2": "blurOut",
			"mode": "normal"
		  }
		}]
	  },
	  "dataProvider": json,
	  "valueField": "keep",
	  "titleField": "type",
	  "colorField": "color",
	  "depth3D": 10,
	  "angle": 15,
	  "export": {
		"enabled": true
	  }
	});
	
	chart.addListener("init", handleInit);

	chart.addListener("rollOverSlice", function(e) {
	  handleRollOver(e);
	});

	function handleInit(){
	  chart.legend.addListener("rollOverItem", handleRollOver);
	}

	function handleRollOver(e){
	  var wedge = e.dataItem.wedge.node;
	  wedge.parentNode.appendChild(wedge);
	}

});


