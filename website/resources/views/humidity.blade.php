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

    <!-- chartjs @todo get the data from the controller and paste the javascript code here -->

@endsection