<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Academic\CourseType;
class CourseTypeSeeder extends Seeder
{
    
    public function run(): void
    {
        $types = ['In-Person', 'Online'];

        foreach ($types as $type) {
            CourseType::firstOrCreate(['name' => $type]);
        }
    }
}
