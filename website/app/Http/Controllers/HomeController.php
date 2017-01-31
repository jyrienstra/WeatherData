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
    public static function data()
    {
        $file = Storage::disk('weatherdata')->get('130670.csv');

        //Split the .csv by newline.
        $seperated = explode("\n", $file);
        //Get the first value, split by comma and create an array with it. This array contains the measurement types
        $labels = explode(',', array_shift($seperated));
        //Dynamically fill an array with measurements. ['column_name1' => [], 'column_name2' => ]
        for($y = 0; $y < count($labels); $y++) {
            $fullData[strtolower($labels[$y])] = [];
        }

        //Loop through all rows (except for the first one)
        for($i = 0; $i < count($seperated); $i++) {
            //If there is an empty row, skip it
            if(empty(trim($seperated[$i]))) continue;

            //Split the row on commas
            $data = explode(',', $seperated[$i]);
            //loop through the splitted row
            for($x = 0; $x < count($data); $x++) {
                //The index of specific data is located in the same index as the label. is. For example: the first value (index 0) is the temperature.
                //The first value of the $labels array is temperature, so this data has to be filled in the array he has as value.
                $fullData[strtolower($labels[$x])][] = $data[$x];
            }

        }
        return $fullData;
    }

    public function top5visibility(){
        $fullData = HomeController::data();
        return view('top5visibility', compact('fullData', 'timeexpired'));
    }

    public function home(){
       $fullData = HomeController::data();
       return view('home', compact('fullData', 'timeexpired'));
    }
}
