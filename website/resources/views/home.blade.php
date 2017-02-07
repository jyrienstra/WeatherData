@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 col-md-offset-0">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-4 col-md-offset-3">
                            With the humidity graph you can check the humidity of all Serbian weather stations. Every 10 seconds the graph is updated to provide the latest data
                            <a href="#" class="btn btn-default">Go to Humidity graph</a>
                        </div>
                        <div class="col-md-4">
                            <a href="#" class="btn btn-default">Go to Top 5 graph</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
