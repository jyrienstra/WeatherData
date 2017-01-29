<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class HumidityController extends Controller
{

    /**
     * Filter the data needed for this controller
     *
     * @return
     */
    public function data() {
        $file = Storage::disk('weatherdata')->get('130670.csv');

        // Split the .csv by newline.
        $seperated = explode("\n", $file);

        // Get the first value, split by comma and create an array with it. This array contains the measurement types
        $labels = explode(',', array_shift($seperated));

        // Dynamically fill an array with measurements. [ 'column_name1' => [], 'column_name2' => [] ]
        for($y = 0; $y < count($labels); $y++) {
            $fullData[strtolower($labels[$y])] = [];
        }

        // Loop through all rows (except for the first one)
        for($i = 0; $i < count($seperated); $i++) {

            //If there is an empty row, skip it
            if(empty(trim($seperated[$i]))) {
                continue;
            }

            // Split the row on commas
            $data = explode(',', $seperated[$i]);

            // Loop through the splitted row
            for($x = 0; $x < count($data); $x++) {
                // The index of specific data is located in the same index as the label. is.
                // For example: the first value (index 0) is the temperature.
                // The first value of the $labels array is temperature, so this data has to be filled in the array he has as value.
                $fullData[strtolower($labels[$x])][] = $data[$x];
            }

        }

        // Current hour (12, or 22 for example)
        $currentHour = \Carbon\Carbon::now()->hour;

        // Loop over the time array
        foreach ($fullData["time"] as $key => $value) {

            // Explode the timestamp (h:m:s)
            $currentHourCSV = explode(':', $value);

            // If the current timestamp is the same as the timestamp in the csv file
            if($currentHourCSV[0] == $currentHour) {
                // @todo Add it to the fulldata array, based on a cronjob
            }

        }

        return $fullData;
    }

    /**
     * Show the data at /humidity
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function home() {
        return $this->data(); // debugging
        return view('humidity');
    }
}
