@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 col-md-offset-0">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-4 col-md-offset-2">
                            With the humidity graph you can check the humidity of all Serbian weather stations. Every 10 seconds the graph is updated to provide the latest data
                            <a href="{{url('humidity')}}" class="btn btn-default form-control" style="margin-top: 20px;"><i class="fa fa-soundcloud"></i> Go to Humidity graph</a>
                        </div>
                        <div class="col-md-4">
                            With this graph, the top 5 of visibility of the stations in the Balkan is provided. Every day is saved until a month back.
                            <a href="{{url('top5visibility')}}" class="btn btn-default form-control" style="margin-top: 20px;"><i class="fa fa-eye"></i> Go to Top 5 graph</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
