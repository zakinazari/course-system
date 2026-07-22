<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Settings\Menu;
use App\Models\Academic\Course;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CourseStartReminderNotification;
use Illuminate\Support\Facades\Log;
use App\Models\Settings\NotificationCategory;
class SendCourseStartReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-course-start-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';



    public function handle()
    {
        
        $menu_id = Menu::where('slug', 'courses')
            ->value('id');


        if (! $menu_id) {
            return;
        }


        $tomorrow = now()->addDay()->toDateString();


        $courses = Course::whereDate('start_date', $tomorrow)
            ->get();


        if ($courses->isEmpty()) {
            return;
        }



        // ==========================
        // Users اصلی سیستم
        // ==========================

        $category = NotificationCategory::where('slug', 'course_start_reminder')
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
                new CourseStartReminderNotification(
                    $courses,
                    $menu_id
                )
            );

        }



        // ==========================
        // Users مربوط به Branch
        // ==========================

        $courses_by_branch = $courses->groupBy('branch_id');



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
                new CourseStartReminderNotification(
                    $branch_courses,
                    $menu_id
                )
            );

        }

    }
}
