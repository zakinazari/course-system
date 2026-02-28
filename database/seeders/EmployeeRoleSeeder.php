<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Hr\EmployeeRole;
class EmployeeRoleSeeder extends Seeder
{
    
    public function run(): void
    {
        $roles = ['Teacher', 'Staff'];

        foreach ($roles as $role) {
            EmployeeRole::firstOrCreate(['name' => $role]);
        }
    }
}
