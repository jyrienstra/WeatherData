<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $file = Storage::disk('weatherdata')->get('data.csv');


        //first line is filled with labels,
        //After that: x, y
        $seperated = explode("\r\n", $file);

        $labels = json_encode(array_shift($seperated)); // not used for now
        $x = [];
        $y = [];

        for($i = 0; $i < count($seperated); $i++) {
            if(empty(trim($seperated[$i]))) continue;

            //x,y
            $data = explode(',', $seperated[$i]);
            $x[] = $data[0];
            $y[] = $data[1];
        }
        $x = json_encode($x);
        $y = json_encode($y);

        return view('home', compact('labels', 'x', 'y'));
    }
}
