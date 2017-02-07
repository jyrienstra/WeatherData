<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HumidityController extends Controller
{

    /**
     * HumidityController constructor
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the data at /humidity
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function home() {
		return view('humidity');
    }

    /**
     * Show the data at /humidity/live/data
     *
     * @return JSON
     */
    public function getData($id) {
        return response()->json($this->calculateData($id));
    }

	/**
     * Show the data at /humidity/stations
     *
     * @return JSON
     */
    public function getStations() {
		$sql = DB::table('stations')
                     ->select(DB::raw('name, stn'))
                     ->where('country', 'like', '%SERBIA%')
                     ->get();
		return response()->json($sql);
    }

    /*
     * Check if OS = windows
     *
     * @return true if Windows is the OS
     */
    private static function checkOsIsWindows(){
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            //windows
            return true;
        }

        //probably linux
        return false;
    }

    /**
     * Filter the data needed for this controller
     *
     * @return array
     */
    private function calculateData($id) {

        // The filtered data
        $filteredData = [];

        // Current hour (12, or 22 for example)
        $currentHour = \Carbon\Carbon::now()->hour;

        $windows = true;
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            //os = windows
            $windows = true;
        } else {
            //os = linux
            $windows = false;
        }


        // Search for the file given by the GET parameter
        if(Storage::disk('weatherdata')->exists(date('Y-m-d') . '/'.$id.'.csv')) {

            $file = Storage::disk('weatherdata')->get(date('Y-m-d') . '/'.$id.'.csv');

            // Split the .csv by newline.
            if (HumidityController::checkOsIsWindows()){
                //os = windows
                $seperated = explode("\n", $file);
            } else{
                //os = linux
                $seperated = explode("\n", $file);
            }
            // Get the first value, split by comma and create an array with it. This array contains the measurement types
            $labels = explode(',', array_shift($seperated));



            // Dynamically fill an array with measurements. [ 'column_name1' => [], 'column_name2' => [] ]
            for ($y = 0; $y < count($labels); $y++) {
                if (!isset($fullData)) {
                    $fullData[strtolower($labels[$y])] = [];
                }
            }


            // Loop through all rows (except for the first one)
            for ($i = 0; $i < count($seperated); $i++) {

                //If there is an empty row, skip it
                if (empty(trim($seperated[$i]))) {
                    continue;
                }

                // Split the row on commas
                $data = explode(',', $seperated[$i]);

                // Loop through the splitted row
                for ($x = 0; $x < count($data); $x++) {
                    // The index of specific data is located in the same index as the label. is.
                    // For example: the first value (index 0) is the temperature.
                    // The first value of the $labels array is temperature, so this data has to be filled in the array he has as value.
                    $fullData[strtolower($labels[$x])][] = $data[$x];
                }

            }


            // Loop over the time array
            foreach ($fullData["time"] as $key => $value) {

                // Explode the timestamp (h:m:s)
                $currentHourCSV = explode(':', $value);


                // If the current timestamp is the same as the timestamp in the csv file
                if ($currentHourCSV[0] == $currentHour && $currentHourCSV[2] % 10 == 0) {
                    // Add all the data to the $filteredData array
                    //$filteredData["date"][] = $fullData["date"][$key];
                    $filteredData["time"][] = $fullData["time"][$key];
                    //$filteredData["temperature"][] = $fullData["temperature"][$key];
                    //$filteredData["dewpoint"][] = $fullData["dewpoint"][$key];
                    //$filteredData["visibility"][] = $fullData["visibility"][$key];
                    $filteredData["humidity"][] = $fullData["visibility"][$key];
                }

            }
        }

        //Als de array empty is
        if(count($filteredData) ==  0){
            //Geen data match met het huidige uur dus een lege array
            //Report error
            return false;
        }else{
            //Als de array wel is gevuld
            //Return array
            return $filteredData;
        }
    }

}
