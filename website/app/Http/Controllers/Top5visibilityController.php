<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

use DB;

class Top5visibilityController extends Controller
{

    /**
     * top5visibilityController constructor
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
    public function home($requestDate = null) {
        $requestDate = is_null($requestDate) ? date('Y-m-d') : date('Y-m-d', strtotime($requestDate));

        $data = $this->calculateData($requestDate);

        $dates = DB::table('average_visibility')->select(DB::raw('DISTINCT date'))->orderBy('date', 'asc')->get();

        return view('top5visibility', compact('data', 'dates', 'requestDate'));
    }

    /**
     * Show the data at /top5/live/data
     *
     * @return JSON
     */
    public function getData(Request $request, $date = null) {

        return response()->json($this->calculateData($date !== null ? date('Y-m-d', strtotime($date)) : date('Y-m-d')));
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
    private function calculateData($date) {
        $performace = microtime();
        $stations = DB::table('balkan_stations')->get()->toArray();


        if($date == date('Y-m-d')) {

            DB::table('average_visibility')->where('date', $date)->delete();

            foreach($stations as $id => $station) {
                try {
                    $file = Storage::disk('weatherdata')->get(date('Y-m-d') . '/' . $station->balkan_station . '.csv');
                }
                catch(FileNotFoundException $e) {
                    continue;
                }

                // Split the .csv by newline.
                if(self::checkOsIsWindows()){
                    //os = windows
                    $seperated = explode("\r\n", $file);
                }else{
                    //os = linux
                    $seperated = explode("\n", $file);
                }

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
                if($index) {
                    DB::table('average_visibility')->insert([
                        'station_id' => $station->balkan_station,
                        'average_visibility' => $totalVisibility / $index,
                        'date' => date('Y-m-d')
                    ]);
                }
            }
        }

        $visibility = DB::table('average_visibility')
                            ->where('date', $date)
                            ->join('stations', 'average_visibility.station_id', '=', 'stations.stn')
                            ->orderBy('average_visibility.average_visibility', 'desc')
                            ->limit(5)
                            ->get();

        $ikbengewooneenarray = [];
        for($bart = 0; $bart < count($visibility); $bart++) {
            $ikbengewooneenarray['station'][] = ucfirst(strtolower($visibility[$bart]->name)) . ', ' . ucfirst(strtolower($visibility[$bart]->country));
            $ikbengewooneenarray['average'][] = round($visibility[$bart]->average_visibility, 2);
            $ikbengewooneenarray['performace'] = microtime() - $performace;
        }

        return $ikbengewooneenarray;
    }



    /**
     * Get top 5 from a date
     *
     * @return array with the top 5 of the given date
     */
    public function getTop5($requestDate = null) {
        $dates = DB::table('average_visibility')->select(DB::raw('DISTINCT date'))->get();
        $data = $this->calculateData($requestDate !== null ? date('Y-m-d', strtotime($requestDate)) : date('Y-m-d'));
        return $data;
    }

    /*
    * Download data to csv
    * Usage: /top5visibility/{date}/download
    *
    */
    public function downloadData($requestDate){
        //Set headers so it downloads to csv
        //header('Content-Type: text/csv; charset=utf-8');
        //header('Content-Disposition: attachment; filename=unwdmi_data.csv');

        //fileopen = output
        $output = fopen('php://output', 'w');

        //get the data from data() function
        $data = Top5visibilityController::getTop5($requestDate);

        dd($data);
        //write header
        fputcsv($output, array('date', 'station', 'avarage'));
        //write data to csv
        for($i=0;$i<count($data['station']); $i++){
            $date = $requestDate;
            $station = $data['station'][$i];
            $avarage = $data['average'][$i];
            //add a line
            fputcsv($output, array($date, $station, $avarage));
        }
        fclose($output);
    }
}
