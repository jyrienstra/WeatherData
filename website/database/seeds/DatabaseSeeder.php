<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	// Run the database seed for the UsersTableSeeder class
        $this->call(UsersTableSeeder::class);
    }
}
