<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CenterSettings\Shift;
use App\Models\User;
use App\Models\Settings\Menu;
use App\Models\Academic\Course;
use App\Models\Assessment\StudentAttendance;
use App\Notifications\CourseAttendanceMissingNotification;
use Illuminate\Support\Facades\Notification;
use DB;
use App\Models\Settings\NotificationCategory;
class CheckCourseAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-course-attendance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification for courses without attendance after shift end time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $menu_id = Menu::where('slug', 'courses')
            ->value('id');


        if (! $menu_id) {
            return;
        }


       
        $shifts = Shift::with('times')->get();


        foreach ($shifts as $shift) {


            $last_time = $shift->times->last();


            if (! $last_time) {
                continue;
            }


            $shift_end = now()->startOfDay()
                ->setTimeFromTimeString(
                    $last_time->end_time->format('H:i:s')
                );


            if (now()->lessThan($shift_end)) {
                continue;
            }



            $courses = Course::where('shift_id', $shift->id)
                ->where('status', 'ongoing')
                ->whereDate('start_date', '<=', today())
                ->whereDate('end_date', '>=', today())
                ->get();



            if ($courses->isEmpty()) {
                continue;
            }



            $missing_attendance_courses = $courses->filter(function ($course) {

                return ! StudentAttendance::where('course_id', $course->id)
                    ->whereDate('attendance_date', today())
                    ->exists();

            });



            if ($missing_attendance_courses->isEmpty()) {
                continue;
            }



            /*
            |--------------------------------------------------------------------------
            | Central Users
            |--------------------------------------------------------------------------
            */

             $category = NotificationCategory::where('slug', 'course_attendance_missing')
            ->first();

            if (! $category) {
                return;
            }

            $central_already_sent = \DB::table('notifications')
                ->where('type', 'App\Notifications\CourseAttendanceMissingNotification')
                ->whereDate('created_at', today())
                ->where('data', 'like', '%"shift_id":'.$shift->id.'%')
                ->where('data', 'like', '%"branch_id":null%')
                ->exists();



            if (! $central_already_sent) {


                $central_users = User::whereNull('branch_id')
                ->whereHas('role.notificationCategories', function ($q) use ($category) {

                    $q->where('notification_categories.id', $category->id);

                })
                ->get();



                if ($central_users->isNotEmpty()) {

                    Notification::send(
                        $central_users,
                        new CourseAttendanceMissingNotification(
                            $shift,
                            $missing_attendance_courses,
                            $menu_id
                        )
                    );

                }

            }


            /*
            |--------------------------------------------------------------------------
            | Branch Users
            |--------------------------------------------------------------------------
            */


            $missing_courses_by_branch = $missing_attendance_courses
                ->groupBy('branch_id');



            foreach ($missing_courses_by_branch as $branch_id => $branch_courses) {

                
                $branch_already_sent = \DB::table('notifications')
                    ->where('type', 'App\Notifications\CourseAttendanceMissingNotification')
                    ->whereDate('created_at', today())
                    ->where('data', 'like', '%"shift_id":'.$shift->id.'%')
                    ->where('data', 'like', '%"branch_id":'.$branch_id.'%')
                    ->exists();



                if ($branch_already_sent) {
                    continue;
                }



                $branch_users = User::where(function ($q) use ($branch_id) {

                $q->where('branch_id', $branch_id)
                    ->orWhereNull('branch_id');

                })
                ->whereHas('role.notificationCategories', function ($q) use ($category) {

                    $q->where('notification_categories.id', $category->id);

                })
                ->get();



                if ($branch_users->isEmpty()) {
                    continue;
                }



                Notification::send(
                    $branch_users,
                    new CourseAttendanceMissingNotification(
                        $shift,
                        $branch_courses,
                        $menu_id
                    )
                );

            }

        }
    }
}
