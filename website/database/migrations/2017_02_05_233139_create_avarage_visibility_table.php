<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAvarageVisibilityTable extends Migration
{
    /**
     * Run the migrations.
     * For the avarage visibility table
     *
     * @return void
     */
    public function up()
    {
        Schema::create('average_visibility', function (Blueprint $table) {
            $table->increments('station_id');
            $table->float('average_visibility', 8, 2); //float 8 big with 2 decimals
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('average_visibility');
    }
}
