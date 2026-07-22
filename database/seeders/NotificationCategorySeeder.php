<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Settings\NotificationCategory;
class NotificationCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       NotificationCategory::insert([

            [
                'name' => 'Book Inventory Stock',
                'slug' => 'book_inventory_stock',
                'description' => 'Notifications for low stock and out of stock books.',
            ],

            [
                'name' => 'Course Attendance Missing',
                'slug' => 'course_attendance_missing',
                'description' => 'Notifications for courses with missing daily attendance.',
            ],

            [
                'name' => 'Course Exam Reminder',
                'slug' => 'course_exam_reminder',
                'description' => 'Notifications for upcoming course exams.',
            ],

            [
                'name' => 'Course Start Reminder',
                'slug' => 'course_start_reminder',
                'description' => 'Notifications for courses starting tomorrow.',
            ],

            [
                'name' => 'Course Unit Fallback',
                'slug' => 'course_unit_fallback',
                'description' => 'Notifications when course dates are extended because a unit continues.',
            ],

            [
                'name' => 'Exam Attendance Missing',
                'slug' => 'exam_attendance_missing',
                'description' => 'Notifications for exams with missing attendance records.',
            ],

            [
                'name' => 'New Meeting',
                'slug' => 'new_meeting',
                'description' => 'Notifications for newly created meetings.',
            ],

        ]);
    }
}
