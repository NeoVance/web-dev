<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class GamesTableSeeder extends Seeder
{
    /**
     * Run the database seeder
     */
    public function run()
    {
        DB::table('games')->insert([
            'title' => 'My first awesome game!',
            'turn' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        
        DB::table('games')->insert([
            'title' => 'My second awesome game!',
            'turn' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}