<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

use DB;

class parseTemporaryData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:parseAverageVisibility';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse the data of the past hour';

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
        DB::table('average_visibility')->where('date', date('Y-m-d'))->delete();
        

$performance = time();

}
}
