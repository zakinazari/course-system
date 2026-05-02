<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
class MonthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          $months = [
            ['number' => 1,  'name' => 'Hamal'],
            ['number' => 2,  'name' => 'Sawr'],
            ['number' => 3,  'name' => 'Jawza'],
            ['number' => 4,  'name' => 'Saratan'],
            ['number' => 5,  'name' => 'Asad'],
            ['number' => 6,  'name' => 'Sunbula'],
            ['number' => 7,  'name' => 'Mizan'],
            ['number' => 8,  'name' => 'Aqrab'],
            ['number' => 9,  'name' => 'Qaws'],
            ['number' => 10, 'name' => 'Jadi'],
            ['number' => 11, 'name' => 'Dalwa'],
            ['number' => 12, 'name' => 'Hut'],
        ];

        DB::table('months')->insert($months);
    }
}
