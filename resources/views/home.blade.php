@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 col-md-offset-0">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    <div class="chartWrapper">
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
<script>
    var ctx = document.getElementById("myChart").getContext("2d");
    var data = {
        labels: <?php print_r(json_encode($fullData['time'])); ?>,
        datasets: [{
            label: 'Temperatuur per dag',
            data: <?php print_r(json_encode($fullData['dewpoint'])); ?>,
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
</script>
@endsection
