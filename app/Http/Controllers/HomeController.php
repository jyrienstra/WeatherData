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

        $labels = explode(',', array_shift($seperated)); // not used for now

        for($y = 0; $y < count($labels); $y++) {
            $fullData[strtolower($labels[$y])] = [];
        }

        for($i = 0; $i < count($seperated); $i++) {
            if(empty(trim($seperated[$i]))) continue;

            //0 -> date, 1-> time, 2 -> temperature, 3 -> dewpoint
            $data = explode(',', $seperated[$i]);
            for($x = 0; $x < count($data); $x++) {
                $keys = array_keys($fullData);

                $fullData[$keys[$x]][] = $data[$x];
            }
        }

        return view('home', compact('fullData'));
    }
}
