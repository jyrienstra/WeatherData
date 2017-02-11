@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 col-md-offset-0">
            <div class="panel panel-default">
                <div class="panel-heading">Top 5 balkan</div>
                <div class="panel-body">
					<div id="form"></div>
                    <div class="chartWrapper">
                        <div class="chartAreaWrapper">
                            <div id="myChart" height="500" width="100%"></div>
                        </div>
                        <canvas id="myChartAxis" height="500" width="0"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>

</script>
<script src="{{url('/js/chart.js')}}"></script>
<script>

function updateGraph(data) {
var arrayOfStrings = data.humidity;
var humidity = arrayOfStrings.map(Number);
Highcharts.chart('myChart', {
	
    title: {
        text: 'Humidity',
        x: -20 //center
    },
    
    xAxis: {
        categories: data.time
    },
    yAxis: {
        title: {
            text: 'Time'
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
        name: 'Humidity',
        data: humidity,
    }]
});
}
function drawGraph(id){
	$.ajax({
		url: 'humidity/live/data/' + id,
		type: 'GET',
		dataType: 'JSON',
		success: function(res) {
		console.log(res)
		updateGraph(res)
		}
	});
}
function stationChange() {
	var id = document.getElementById("station").value;
    drawGraph(id);     
	setTimeout(drawGraph(id), 10000)
}
window.onload=  function(){
	$.ajax({
		url: 'humidity/stations',
		type: 'GET',
		dataType: 'JSON',
		success: function(res) {
		document.getElementById("log").innerText= res;
		}
	});
	
};
</script>
@endsection
