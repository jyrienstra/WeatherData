@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 col-md-offset-0">
            <div class="panel panel-default">
                <div class="panel-heading">Top 5 balkan</div>

                <div class="panel-body">
                    <div class="form-group">
                        <h3>You are currently viewing the date of {{date('d-m-Y', strtotime($requestDate))}}</h3>
                        <label for="date">Select date</label>

                        <select id="date" name="date" class="form-control" />
                        @foreach ($dates as $date)
                            @if($requestDate == null && $date == date('Y-m-d'))
                                <option selected>{{date('d-m-Y', strtotime($date))}}</option>
                            @elseif ($requestDate == $date)
                                <option selected>{{date('d-m-Y', strtotime($date))}}</option>
                            @else
                                <option>{{date('d-m-Y', strtotime($date))}}</option>
                            @endif
                        @endforeach
                        </select>
                    </div>
                    <script>
                    $('#date').change(function () {
                        location.href = '<?php $_SERVER['HTTP_HOST']; ?>' + '/top5visibility/' + $('#date').val();
                    })
                    </script>
                    <div class="chartWrapper">
                        <div class="chartAreaWrapper">
                            <div id="myChart" height="500" width="100%"></div>
                        </div>
                    </div>
                    <hr />
                    <a href="" id="downloadCsv" style="margin-top:5px;" class="btn btn-default pull-right"><span class="fa fa-download"></span> Download current data to csv</a>
                        <script>
                            $('#downloadCsv').attr("href", '<?php $_SERVER['HTTP_HOST']; ?>' + '/top5visibility/' + $('#date').val() + '/download');
                        </script>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{url('/js/chart.js')}}"></script>
<script>

function updateGraph(data) {
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
        }],
    });
}

window.onload=  function(){
	$.ajax({
		url: location.href.replace(/\/$/, "") + '/live/data',
		type: 'GET',
		dataType: 'JSON',
		success: function(res) {
            if(res.length < 1) {
                $('#downloadCsv').hide();
                $('.chartAreaWrapper').append('<div class="alert">There is no data for this day</div>');
            }
            else {
                $('#downloadCsv').show();
                updateGraph(res)
            }
		//console.log(res)

		}
	});
};

</script>
@endsection
