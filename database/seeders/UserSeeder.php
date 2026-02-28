<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Settings\AccessRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash; 
class UserSeeder extends Seeder
{
   
    public function run()
    {
        // 1️ ساخت رول Developer
        $developerRole = AccessRole::updateOrCreate(
            ['role_name' => 'Developer'],
            ['is_system' => true]
        );

        // 2️ ساخت کاربر Developer
        User::updateOrCreate(
            ['email' => 'developer@system.local'],
            [
                'name' => 'System Developer',
                'password' => Hash::make('very_strong_password'),
                'role_id' => $developerRole->id,
                'branch_id' => null,
            ]
        );

        // 3️ ساخت رول Super Admin
        $adminRole = AccessRole::updateOrCreate(
            ['role_name' => 'Super Admin'],
            ['is_system' => false]
        );

        // 4️ ساخت کاربر Admin
        User::updateOrCreate(
            ['email' => 'admin@app.local'],
            [
                'name' => 'App Admin',
                'password' => Hash::make('admin_password'),
                'role_id' => $adminRole->id,
                'branch_id' => null,
            ]
        );
    }
}
