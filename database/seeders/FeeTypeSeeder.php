<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Financial\FeeType;
class FeeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // FeeType::updateOrCreate(
        //     ['code' => 'book'],
        //     ['name' => 'Book Fee'],
        //     ['fee_amount' =>1000]
        // );

        FeeType::updateOrCreate(
            ['code' => 'registration'],
            [
                'name' => 'Registration Fee',
                'fee_amount' => 500
            ]
        );

        FeeType::updateOrCreate(
            ['code' => 'card'],
            [
                'name' => 'Card Fee',
                'fee_amount' => 100
            ]
        );

        FeeType::updateOrCreate(
            ['code' => 'exam'],
            [
                'name' => 'Exam Fee',
                'fee_amount' => 150
            ]
        );
    }
}
