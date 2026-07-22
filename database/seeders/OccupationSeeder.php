<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CenterSettings\Occupation;
class OccupationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Occupation::insert([
            ['name' => 'School Student'],
            ['name' => 'University Student'],
            ['name' => 'Private Org Emp'],
            ['name' => 'Official Worker'],
            ['name' => 'Businessman'],
            ['name' => 'Jobless'],
        ]);
    }


}
