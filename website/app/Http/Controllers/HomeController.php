<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

use DB;

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
    public function data()
    {

        $file = Storage::disk('weatherdata')->get(date('Y-m-d') . '/130670.csv');

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
        $stations = DB::table('balkan_stations')->get()->toArray();
        
        for($i = 0; $i < count($stations); $i++) {
            try {
                $file = Storage::disk('weatherdata')->get(date('Y-m-d', time()) . '/' . $stations[$i] . '.csv');
            }
            catch(FileNotFoundException $e) {
                continue;
            }

            // Split the .csv by newline.
            $seperated = explode("\r\n", $file);

            // Get the first value, split by comma and create an array with it. This array contains the measurement types
            $labels = explode(',', array_shift($seperated));

            // Dynamically fill an array with measurements. [ 'column_name1' => [], 'column_name2' => [] ]
            for($y = 0; $y < count($labels); $y++) {
                $fullData[strtolower($labels[$y])] = [];
            }

            $totalVisibility = 0;
            $index = 0;
            // Loop through all rows (except for the first one)
            for($i = 0; $i < count($seperated); $i++) {

                //If there is an empty row, skip it
                if(empty(trim($seperated[$i]))) {
                    continue;
                }

                // Split the row on commas
                $data = explode(',', $seperated[$i]);

                $totalVisibility += $data[4];
                $index++;

            }

            DB::table('average_visibility')->insert([
                'station_id' => $weatherdata[$foo],
                'average_visibility' => $totalVisibility / $index,
                'date' => date('Y-m-d')
            ]);
        }

        $visibility = DB::table('average_visibility')
                            ->where('date', date('Y-m-d'))
                            ->orderBy('average_visibility', 'desc')
                            ->limit(5)
                            ->get()
                            ->toJson();
        return $visibility;
    }

    public function home(){
       $fullData = HomeController::data();
       return view('home', compact('fullData', 'timeexpired'));
    }
}
