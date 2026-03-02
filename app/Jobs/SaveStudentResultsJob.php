<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Assessment\StudentCourseResult;
use App\Models\Assessment\StudentCourseResultLog;
class SaveStudentResultsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $course_id;
    public array $results;
    public int $user_id;

    public function __construct(int $course_id, array $results, int $user_id)
    {
        $this->course_id = $course_id;
        $this->results = $results;
        $this->user_id = $user_id;
    }

    public function handle()
    {
        $rows = [];
        $logs = [];

        foreach ($this->results as $student_id => $data) {
            $attendance = $data['attendance'] ?? 0;
            $cognitive  = $data['cognitive'] ?? 0;
            $midterm    = $data['midterm'] ?? 0;
            $final      = $data['final'] ?? 0;
            $total      = min(100, $attendance + $cognitive + $midterm + $final);

            $existing = StudentCourseResult::where('student_id', $student_id)
                        ->where('course_id', $this->course_id)
                        ->first();
            // اگر رکورد موجود بود و مقادیر تغییر کرده، لاگ بزن
            if ($existing && (
                $existing->attendance != $attendance ||
                $existing->cognitive != $cognitive ||
                $existing->midterm != $midterm ||
                $existing->final != $final ||
                $existing->total != $total
            )) {
                $logs[] = [
                    'student_course_result_id' => $existing->id,
                    'midterm_old' => $existing->midterm,
                    'final_old' => $existing->final,
                    'cognitive_old' => $existing->cognitive,
                    'attendance_old' => $existing->attendance,
                    'total_old' => $existing->total,
                    'midterm_new' => $midterm,
                    'final_new' => $final,
                    'cognitive_new' => $cognitive,
                    'attendance_new' => $attendance,
                    'total_new' => $total,
                    'user_id' => $this->user_id,
                ];
            }

            $rows[] = [
                'student_id' => $student_id,
                'course_id'  => $this->course_id,
                'attendance' => $attendance,
                'cognitive'  => $cognitive,
                'midterm'    => $midterm,
                'final'      => $final,
                'total'      => $total,
                'user_id' => $existing->user_id ?? $this->user_id,
            ];
        }

        // ذخیره نمرات با upsert
        StudentCourseResult::upsert(
            $rows,
            ['student_id','course_id'],
            ['attendance','cognitive','midterm','final','total','user_id']
        );

        foreach ($logs as $log) {
            StudentCourseResultLog::create($log);
        }

        // \Log::info('Existing student result', [
        //     'student_id' => $student_id,
        //     'existing' => $existing ? $existing->toArray() : null,
        // ]);
    }
}
