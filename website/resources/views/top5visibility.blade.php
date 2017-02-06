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
                            <canvas id="myChart" height="500" width="100%"></canvas>
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
	var ctx = document.getElementById("myChart").getContext("2d");
    var data = {
        labels: data.station,
        datasets: [{
            label: 'Temperatuur per dag',
            data: data.average,
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
    new Chart(ctx).Bar(data, {
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
