<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use DB;

class calculateHumidity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:calculateHumidity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Read the files produced by the weatherstations and calculate the humidity';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        // Delete: \File::delete(storage_path() . '/humidity-hourly/humidity.csv');
        // Append: \File::append(storage_path() . '/humidity-hourly/humidity.csv', 'content here');

        // Create a new file with no content in it
        \File::put(storage_path() . '/humidity-hourly/humidity.csv', null);

        // Loop recursively over all the files in storage/weatherdata
        $files = \File::allFiles(storage_path() . '/weatherdata');

        // The filtered data
        $filteredData = [];

        // Current hour (12, or 22 for example)
        $currentHour = \Carbon\Carbon::now()->hour;

        // For every file, get the filename and loop over its contents
        foreach ($files as $key => $value) {

            // Get the filename
            $fileName = $value->getFilename();

            // Get the file contents based on the filename
            $file = Storage::disk('weatherdata')->get($fileName);

            // Split the .csv by newline.
            $seperated = explode("\r\n", $file);

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

        // Loop over the time array
        foreach ($fullData["time"] as $key => $value) {

            // Explode the timestamp (h:m:s)
            $currentHourCSV = explode(':', $value);

            // If the current timestamp is the same as the timestamp in the csv file
            if ($currentHourCSV[0] == $currentHour) {
                // Add all the data to the $filteredData array
                $filteredData["date"][] = $fullData["date"][$key];
                $filteredData["time"][] = $fullData["time"][$key];
                $filteredData["temperature"][] = $fullData["temperature"][$key];
                $filteredData["dewpoint"][] = $fullData["dewpoint"][$key];
                $filteredData["visibility"][] = $fullData["visibility"][$key];
                $filteredData["humidity"][] = $fullData["visibility"][$key];
            }

        }

        dd($filteredData);
        return $fullData;
    }
}
