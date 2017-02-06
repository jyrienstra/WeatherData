@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 col-md-offset-0">
            <div class="panel panel-default">
                <div class="panel-heading">Humidity</div>

                <div class="panel-body">
                    <div class="chartWrapper">
					<div id="result" style="color:red"></div>
                        <div class="chartAreaWrapper">
                            <canvas id="myChart" height="500" width="10000"></canvas>
                        </div>
                        <canvas id="myChartAxis" height="500" width="0"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{url('/js/chart.js')}}"></script>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
<script>

function updateGraph(data) {
	var ctx = document.getElementById("myChart").getContext("2d");
    var data = {
        labels: data.time,
        datasets: [{
            label: 'Temperatuur per dag',
            data: data.humidity,
            fillColor: "rgba(220,220,220,0.2)",
            strokeColor: "rgba(220,220,220,1)",
            pointColor: "rgba(220,220,220,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(220,220,220,1)",
        }],
        options: {
        }
    };
    new Chart(ctx).Line(data, {
        onAnimationComplete: function () {
            var sourceCanvas = this.chart.ctx.canvas;

                var copyWidth = this.scale.xScalePaddingLeft - 5;
                // the +5 is so that the bottommost y axis label is not clipped off
                // we could factor this in using measureText if we wanted to be generic
                var copyHeight = this.scale.endPoint + 5;
                var targetCtx = document.getElementById("myChartAxis").getContext("2d");
                targetCtx.canvas.width = copyWidth;
                targetCtx.drawImage(sourceCanvas, 0, 0, copyWidth, copyHeight, 0, 0, copyWidth, copyHeight);
        }
    });
}
function getData(){

	$.ajax({
		url: 'humidity/live/data',
		type: 'GET',
		dataType: 'JSON',
		success: function(res) {
        console.log(res);
		updateGraph(res)
		}
	});
}

window.onload=  function(){
	getData();
	myVar = setInterval(getData, 10000);
};

</script>
@endsection
