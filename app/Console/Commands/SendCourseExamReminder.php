<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Settings\Menu;
use App\Models\Academic\Course;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CourseExamReminderNotification;
use Illuminate\Support\Facades\Log;
use App\Models\Settings\NotificationCategory;
class SendCourseExamReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-course-exam-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $menu_id = Menu::where('slug', 'courses')->value('id');

        if (! $menu_id) {
            return Command::FAILURE;
        }


        $tomorrow = now()->addDay()->toDateString();


        $courses = Course::with('book')
            ->where(function ($query) use ($tomorrow) {

                $query->whereDate('mid_exam_date', $tomorrow)
                    ->orWhereDate('final_exam_date', $tomorrow);

            })
            ->get();


        if ($courses->isEmpty()) {
            return Command::SUCCESS;
        }



        $courses_data = $courses->map(function ($course) use ($tomorrow) {

            $exam_type = null;
            $exam_date = null;


            if ($course->mid_exam_date?->toDateString() === $tomorrow) {

                $exam_type = 'mid_term';
                $exam_date = $course->mid_exam_date;

            } elseif ($course->final_exam_date?->toDateString() === $tomorrow) {

                $exam_type = 'final';
                $exam_date = $course->final_exam_date;
            }


            return [

                'id' => $course->id,

                'name' => $course->name,

                'book_name' => $course->book?->name,

                'exam_type' => $exam_type,

                'exam_date' => $exam_date,

                'branch_id' => $course->branch_id,

            ];

        });



        // ==========================
        // Users اصلی سیستم
        // ==========================
        $category = NotificationCategory::where('slug', 'course_exam_reminder')
            ->first();

        if (! $category) {
            return;
        }

        $main_users = User::whereNull('branch_id')
        ->whereHas('role.notificationCategories', function ($q) use ($category) {

            $q->where('notification_categories.id', $category->id);

        })
        ->get();



        if ($main_users->isNotEmpty()) {

            Notification::send(
                $main_users,
                new CourseExamReminderNotification(
                    $courses_data,
                    $menu_id
                )
            );

        }




        // ==========================
        // Users مربوط به Branch
        // ==========================

        $courses_by_branch = $courses_data->groupBy('branch_id');



        foreach ($courses_by_branch as $branch_id => $branch_courses) {


            $branch_users = User::where('branch_id', $branch_id)
            ->whereHas('role.notificationCategories', function ($q) use ($category) {

                $q->where('notification_categories.id', $category->id);

            })
            ->get();



            if ($branch_users->isEmpty()) {
                continue;
            }



            Notification::send(
                $branch_users,
                new CourseExamReminderNotification(
                    $branch_courses,
                    $menu_id
                )
            );

        }



        return Command::SUCCESS;
    }
}
