<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;

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

            //$test = [];
            //$currentMinute = \Carbon\Carbon::now()->minute;
            //$test["hour"] = $currentHour;
            //$test["minute"] = $currentMinute;

            $fullData = $this->parseCSV(date('Y-m-d'), $id);
            $filteredData = [];

            //dd($test);
            //dd( count($fullData["time"]) - 3600 . ' and ' . count($fullData["time"]) );

            $i = count($fullData["time"]) - 3601;

            if($i > 0) {
                for (; $i < count($fullData["time"]); $i += 10) {
                    $filteredData["time"][] = $fullData["time"][$i];
                    $filteredData["humidity"][] = $fullData["humidity"][$i];
                }
            }
            else {
                $yesterday = $this->parseCSV(date('Y-m-d', strtotime('yesterday')), $id);

                $i = $i * -1;

                for($bla = count($yesterday) - 1; $bla > $i; $bla--) {
                    $filteredData["time"][] = $yesterday[$bla];
                    $filteredData["humidity"][] = $yesterday[$bla];
                }
            }

            // Loop over the time array
            // foreach ($fullData["time"] as $key => $value) {
            //
            //     // Explode the timestamp (h:m:s)
            //     $currentHourCSV = explode(':', $value);
            //
            //     // if (($currentHourCSV[0] == $test["hour"] || $currentHourCSV[0] == ($test["hour"] - 1)) && $currentHourCSV[1] >= $test["minute"]) {
            //     //     // Add all the data to the $filteredData array
            //     //     //$filteredData["date"][] = $fullData["date"][$key];
            //     //     //$filteredData["time"][] = date('h:m:s', strtotime($fullData["time"][$key]) + 60 * 60);
            //     //     $filteredData["time"][] = $fullData["time"][$key];
            //     //     //$filteredData["temperature"][] = $fullData["temperature"][$key];
            //     //     //$filteredData["dewpoint"][] = $fullData["dewpoint"][$key];
            //     //     //$filteredData["visibility"][] = $fullData["visibility"][$key];
            //     //     $filteredData["humidity"][] = $fullData["visibility"][$key];
            //     // }
            //
            //
            //     // If the current timestamp is the same as the timestamp in the csv file
            //     if ($currentHourCSV[0] == $currentHour && $currentHourCSV[2] % 10 == 0) {
            //     // if(Carbon::now()->diffInHours(Carbon::parse($value)->addHour(1)) <= 1 && $currentHourCSV[2] % 10 == 0) {
            //     //     Add all the data to the $filteredData array
            //     //     $filteredData["date"][] = $fullData["date"][$key];
            //     //     $filteredData["time"][] = date('h:m:s', strtotime($fullData["time"][$key]) + 60 * 60);
            //         $filteredData["time"][] = $fullData["time"][$key];
            //     //     $filteredData["temperature"][] = $fullData["temperature"][$key];
            //     //     $filteredData["dewpoint"][] = $fullData["dewpoint"][$key];
            //     //     $filteredData["visibility"][] = $fullData["visibility"][$key];
            //         $filteredData["humidity"][] = $fullData["visibility"][$key];
            //     }
            //
            // }

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

    private function parseCSV($date, $id) {
        // The filtered data
        $filteredData = [];

        // Current hour (12, or 22 for example)
        $currentHour = Carbon::now()->hour;

        $windows = true;
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            //os = windows
            $windows = true;
        } else {
            //os = linux
            $windows = false;
        }


        // Search for the file given by the GET parameter
        if(Storage::disk('weatherdata')->exists($date . '/'.$id.'.csv')) {

            $file = Storage::disk('weatherdata')->get($date . '/'.$id.'.csv');

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
        }

        return $fullData;
    }

    /*
    * Download data to csv
    * Usage: /top5visibility/{id}/download
    *
    * @param $id Stationnumber
    */
    public function downloadData($id){
        //Set headers so it downloads to csv
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=unwdmi_humidity_data.csv');

        //fileopen = output
        $output = fopen('php://output', 'w');

        //get the data from data() function
        $data = HumidityController::calculateData($id);

        //write header
        fputcsv($output, array('stn', 'time', 'humidity'));
        //write data to csv
        for($i=0;$i<count($data['time']); $i++){
            $stn = $id;
            $time = $data['time'][$i];
            $humidity = $data['humidity'][$i];
            //add a line
            fputcsv($output, array($stn, $time, $humidity));
        }
        fclose($output);
    }

}
