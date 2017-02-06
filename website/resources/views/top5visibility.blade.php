@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 col-md-offset-0">
            <div class="panel panel-default">
                <div class="panel-heading">Top 5 balkan</div>

                <div class="panel-body">
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
<script src="{{url('/js/chart.js')}}"></script>
<script>

function updateGraph(data) {
<<<<<<< HEAD
    console.log(data.stations);
    Highcharts.chart('myChart', {
    chart: {
        type: 'bar'
    },
    title: {
        text: 'Top 5 visibility in the Balkan area'
    },
    xAxis: {
        categories: data.station,
        title: {
            text: null
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Visibility (kilometers)',
            align: 'high'
        },
        labels: {
            overflow: 'justify'
        }
    },
    plotOptions: {
        bar: {
            dataLabels: {
                enabled: true
            }
        }
    },
    credits: {
        enabled: false
    },

    series: [{
        name: 'Average Visibility',
        data: data.average
    }]
});
}

window.onload=  function(){
	$.ajax({
		url: 'top5visibility/live/data',
		type: 'GET',
		dataType: 'JSON',
		success: function(res) {
		//console.log(res)
		updateGraph(res)
		}
	});
};

</script>
@endsection
