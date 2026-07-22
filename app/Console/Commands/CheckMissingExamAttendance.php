<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Settings\Menu;
use App\Models\Academic\Course;
use App\Models\Assessment\ExamAttendance;
use App\Notifications\ExamAttendanceMissingNotification;
use Illuminate\Support\Facades\Notification;
use DB;
use App\Models\Settings\NotificationCategory;
class CheckMissingExamAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-missing-exam-attendance';

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
        $menu_id = Menu::where('slug', 'courses')
            ->value('id');

        if (! $menu_id) {
            return Command::FAILURE;
        }

        $category = NotificationCategory::where('slug', 'exam_attendance_missing')
            ->first();

        if (! $category) {
            return;
        }

        $today = today()->toDateString();

        $courses = Course::where(function ($query) use ($today) {

                $query->whereDate('mid_exam_date', $today)
                    ->orWhereDate('final_exam_date', $today);

            })
            ->get();

        if ($courses->isEmpty()) {
            return Command::SUCCESS;
        }

        $missing_exam_attendances = collect();

        foreach ($courses as $course) {

            /*
            |--------------------------------------------------------------------------
            | Mid-Term Exam
            |--------------------------------------------------------------------------
            */

            if ($course->mid_exam_date?->toDateString() === $today) {

                $exists = ExamAttendance::where('course_id', $course->id)
                    ->whereHas('examType', function ($q) {

                        $q->where('exam_period', 'midterm');

                    })
                    ->exists();

                if (! $exists) {

                    $missing_exam_attendances->push([

                        'id' => $course->id,

                        'name' => $course->name,

                        'branch_id' => $course->branch_id,

                        'exam_type' => 'mid_term',

                    ]);

                }

            }

            /*
            |--------------------------------------------------------------------------
            | Final Exams
            |--------------------------------------------------------------------------
            */

            if ($course->final_exam_date?->toDateString() === $today) {

                $exists = ExamAttendance::where('course_id', $course->id)
                    ->whereHas('examType', function ($q) {

                        $q->where('exam_period', 'final');

                    })
                    ->exists();

                if (! $exists) {

                    $missing_exam_attendances->push([

                        'id' => $course->id,

                        'name' => $course->name,

                        'branch_id' => $course->branch_id,

                        'exam_type' => 'final',

                    ]);

                }

            }

        }

        if ($missing_exam_attendances->isEmpty()) {
            return Command::SUCCESS;
        }

        /*
        |--------------------------------------------------------------------------
        | Main Users
        |--------------------------------------------------------------------------
        */

        $category = NotificationCategory::where('slug', 'course_attendance_missing')
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
                new ExamAttendanceMissingNotification(
                    $missing_exam_attendances,
                    $menu_id
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Branch Users
        |--------------------------------------------------------------------------
        */

        $courses_by_branch = $missing_exam_attendances
            ->groupBy('branch_id');

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
                new ExamAttendanceMissingNotification(
                    $branch_courses,
                    $menu_id
                )
            );

        }

        return Command::SUCCESS;
    }
}
