//Global Constants


	var soilMoisture = false;
	var rainfall;
	var chart;

//End global constants

function toggleHighchartVisibility(){
	if($("#highchartContainer").is(":visible")){
		$("#highchartContainer").hide();
	}
	else{
		$("#highchartContainer").show();
	}
}

function resetHighchart(){
	drawDefaultHighchart();
	$("#highchartContainer").show();
}

function drawDefaultHighchart(){
	var defaultChart = true;
	var chart = $('#highchartContainer').highcharts({
            chart: {
                zoomType: 'xy'
            },
            title: {
                text: 'Average Monthly Soil Moisture and Rainfall'
            },
            subtitle: {
                text: 'Source: weather.com'
            },
            xAxis: [{
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
            }],
            yAxis: [{ // Primary yAxis
                labels: {
					id: 'temp-axis',
                    format: '{value}°C',
                    style: {
                        color: '#89A54E'
                    }
                },
                title: {
                    text: 'Temperature',
                    style: {
                        color: '#89A54E'
                    }
                }
            }, { // Secondary yAxis
                
				id: 'rain-axis',
				title: {
					text: 'Rainfall',
                    style: {
                        color: '#4572A7'
                    }
                },
                labels: {
                    format: '{value} mm',
                    style: {
                        color: '#4572A7'
                    }
                },
                opposite: true
            }],
            tooltip: {
                shared: true
            },
            legend: {
                layout: 'vertical',
                align: 'left',
                x: 120,
                verticalAlign: 'top',
                y: 100,
                floating: true,
                backgroundColor: '#FFFFFF'
            },
            series: [{
				id: 'rain',
                name: 'Rainfall',
                color: '#4572A7',
                type: 'column',
                yAxis: 1,
                data: [49.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4],
                tooltip: {
                    valueSuffix: ' mm'
                }
    
            }, {
				id: 'temp',
                name: 'moisture',
                color: '#89A54E',
                type: 'spline',
                data: [7.0, 6.9, 9.5, 14.5, 18.2, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6],
                tooltip: {
                    valueSuffix: '°C'
                }
            }]
        });
		
		$("#highchartContainer").show();
		chart = $('#highchartContainer').highcharts();
		rainfall=true;
		soilMoisture=false;

}


function getMoisturePH(){
	$.ajax({
		url: "ChartDataQuery.php",
		type: 'GET',
		data: {'chartType':'Soil-pH-perDay'},
		success: function (data){
		console.log(data);
		}
	
	});
}

function getWateringDays(){
	$.ajax({
		url: "ChartDataQuery.php",
		type: 'GET',
		data: {'chartType':'Watering_Days'},
		success:function(data){
			console.log(data);
			var mositurePhData = jQuery.parseJSON(data);
			var chart = $('#highchartContainer').highcharts();
			chart.series[0].setData([10,10,10,10,10]);
		}
	
	});
}

function toggleSoilMositure(){
	if(!soilMoisture){
	var chart = $('#highchartContainer').highcharts();
	var series = chart.get('temp');
	series.hide();
	$.ajax({
		url: "ChartDataQuery.php",
		type: 'GET',
		data: {'chartType':'Soil-pH-perDay'},
		success: function (data){
		var temp = jQuery.parseJSON(data);
		var string = "[";
		for(var i = 0; i < temp.length; i++){
			if(i != 0){
				string = string + ',';
			}
			string = string+ temp[i]["Moisture"];
		}
		string = string +"]";
		console.log(string);
		chart.addSeries({
				id: 'moisture',
				title: 'moisture',
				name: 'moisture',
				type: 'spline',
				//yAxis: '1',
                data: string
            });
		//temp.forEach(function(){
		//	console.log("test");
	//	
	//	})
		//chart.get('moisture').addPoint(45);
		console.log(data);
		}
	
	});
	soilMoisture = !soilMoisture;
	}
	else{
		var series = chart.get('moisture');
		series.hide();
		soilMoisture = !soilMoisture;
	
	}
	
}

function toggleRainfall(){
	var chart = $('#highchartContainer').highcharts();
	//var series = chart.get('rain');
	if (rainfall) {
			var series = chart.get('rain');
            series.hide();
			//chart.get('rain-title').remove();
			chart.get('rain-axis').remove();
			rainfall = false;
			//            $button.html('Show series');
    } else {
		chart.addAxis({ // Secondary yAxis
			id: 'rain-axis',
			title: {
				text: 'Rainfall'
			},
			lineWidth: 2,
			lineColor: '#08F',
			opposite: true
		});
            chart.addSeries({
				id: 'rain',
				name: 'Rainfall',
				type: 'column',
				color: '#08F',
                yAxis: 'rain-axis',
                data: [49.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4]
            });
    
           
		rainfall=true;
//            $button.html('Hide series');
    
}	
}