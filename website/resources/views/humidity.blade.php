@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 col-md-offset-0">
            <div class="panel panel-default">
                <div class="panel-heading">Humidity</div>
                <div class="panel-body">
					  <select id="station" class="form-control" onchange="stationChange()"></select>
                      <p id="error"></p>
                    <div class="chartWrapper">
                        <div class="chartAreaWrapper">
                            <div id="myChart" height="500" width="100%"></div>
                        </div>
                        <canvas id="myChartAxis" height="500" width="0"></canvas>
                    </div>
                    <a href="" id="downloadCsv" style="margin-top:5px;" class="btn btn-default pull-right"><span class="fa fa-download"></span> Download current data to csv</a>
                </div>
            </div>
        </div>
    </div>
</div>


<script>

</script>
<script src="http://code.highcharts.com/stock/highstock.js"></script>
<script>

var chart,
    currentStation,
    interval;

function updateGraph(id) {
    $.ajax({
        url: 'humidity/live/data/' + id,
        type: 'GET',
        dataType: 'JSON',
        success: function(res) {
            let xPoint = res.time[res.time.length - 1],
                yPoint = res.humidity[res.humidity.length - 1];

            chart.xAxis[0].categories.push(xPoint);

            chart.series[0].addPoint({
            y: Number(yPoint)
        }, true, true, true);
            chart.redraw();
        }
    });
}

function drawGraph(id){
	$.ajax({
		url: 'humidity/live/data/' + id,
		type: 'GET',
		dataType: 'JSON',
		success: function(res) {
		    if(res == false){
                if(chart != undefined) {
                    chart.destroy();
                }
		        $('#error').text('Er is geen data beschikbaar voor dit station dat overeenkomt met het huidige uur');
            }else{
                $('#error').text('');
                var arrayOfStrings = res.humidity;
                var humidity = arrayOfStrings.map(Number);
                chart = Highcharts.chart('myChart', {

                    title: {
                        text: 'Humidity',
                        x: -20 //center
                    },

                    xAxis: {
                        categories: res.time
                    },
                    yAxis: {
                        title: {
                            text: 'Humidity'
                        },
                        plotLines: [{
                            value: 0,
                            width: 1,
                            color: '#808080'
                        }]
                    },

                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle',
                        borderWidth: 0
                    },
                    series: [{
                        name: 'Time',
                        data: humidity,
                        type: 'spline'
                    }],
                    plotOptions: {
                        series: {
                            animation: false
                        }
                    },
                    scrollbar: {
	    	            enabled: true
	                 },
                     navigator:{
                         enabled:true
                     },
	                rangeSelector: {
                        enabled:false
	                },
                    chart: {
                        zoomType: 'x'
                    },
                });
                //return false;
            }
		}
	});
}
function stationChange() {
	var id = $('#station').val();
    intervalUpdate('unset');
    drawGraph(id);
    intervalUpdate('set', id);
    $('#downloadCsv').attr("href", '<?php $_SERVER['HTTP_HOST']; ?>' + '/humidity/' + id + '/download');

}
window.onload=  function(){
	var elt;
	$.ajax({
		 url: '/humidity/stations',
		 type: 'GET',
		 dataType: 'JSON',
		 success: function(res) {
			var numbers = res;
			var option = '<option>Select station</option>';
			for (var i=0;i<numbers.length;i++){
			   option += '<option value="'+ numbers[i].stn + '">' + numbers[i].name + '</option>';
			}
			$('#station').append(option);
		}
	 });
};

var intervalUpdate = function(state, id) {
    currentStation = id || 0;

    switch(state) {
        case 'set':
            interval = setInterval(function () {
                updateGraph(currentStation);
            }, 10000);
            break;
        case 'unset':
            clearInterval(interval);
            break;
    }
}
</script>
@endsection
