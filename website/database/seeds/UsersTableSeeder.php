<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds of this class
     *
     * @return void
     */
    public function run()
    {
        /**
         * Insert x amount of test users in the users table.
         */
        for ($i = 0; $i < 5 ; $i++) {
            DB::table("users")->insert([
                "name" 	=> "test" . $i,
                "email" => "test" . $i . "@domain.com",
                "password" => bcrypt(("secret" . $i)),
                "created_at" =>	Carbon\Carbon::now(),
                "updated_at" => Carbon\Carbon::now()
            ]);
        }
    }
}
