<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Assessment\StudentCourseResult;
use App\Models\Assessment\StudentCourseResultLog;
use App\Models\Academic\Course;
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
        $course = Course::with('book:id,pass_mark,excellent_mark')
            ->findOrFail($this->course_id);
        $book = $course->book;

        $rows = [];
        $logs = [];

        foreach ($this->results as $student_id => $data) {
            $attendance = $data['attendance'] ?? 0;
            $cognitive  = $data['cognitive'] ?? 0;
            $midterm    = $data['midterm'] ?? 0;
            $final      = $data['final'] ?? 0;
            $total      = min(100, $attendance + $cognitive + $midterm + $final);
            
            $pass_mark_snapshot      = $book->pass_mark;
            $excellent_mark_snapshot = $book->excellent_mark;
            $status = $this->calculateStatus($total, $book);


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
                    'status_old' => $existing->status,
                    'pass_mark_snapshot_old' => $existing->pass_mark_snapshot,
                    'excellent_mark_snapshot_old' => $existing->excellent_mark_snapshot,

                    'midterm_new' => $midterm,
                    'final_new' => $final,
                    'cognitive_new' => $cognitive,
                    'attendance_new' => $attendance,
                    'total_new' => $total,
                    'status_new' => $status,
                    'pass_mark_snapshot_new' => $pass_mark_snapshot,
                    'excellent_mark_snapshot_new' => $excellent_mark_snapshot,

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
                'status'      => $status,
                'pass_mark_snapshot'   => $pass_mark_snapshot,
                'excellent_mark_snapshot'   => $excellent_mark_snapshot,
                'user_id' => $existing->user_id ?? $this->user_id,
            ];
        }

        // ذخیره نمرات با upsert
        StudentCourseResult::upsert(
            $rows,
            ['student_id','course_id'],
            ['attendance','cognitive','midterm','final','total','user_id','status','pass_mark_snapshot','excellent_mark_snapshot']
        );

        foreach ($logs as $log) {
            StudentCourseResultLog::create($log);
        }

        // \Log::info('Existing student result', [
        //     'student_id' => $student_id,
        //     'existing' => $existing ? $existing->toArray() : null,
        // ]);
    }

    private function calculateStatus(float $total, $book): ?string
    {
      
        if (!is_null($book->pass_mark) && $total >= $book->pass_mark) {
            return 'passed';
        }elseif(!is_null($book->pass_mark)){
            return 'failed';
        }
        return null;
    }
}
