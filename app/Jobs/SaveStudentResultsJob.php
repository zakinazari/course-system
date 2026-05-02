<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Assessment\StudentCourseResult;
use App\Models\Assessment\StudentExamScore;
use App\Models\Assessment\StudentExamScoreLog;
use App\Models\Assessment\StudentCourseResultLog;
use App\Models\Academic\Course;
class SaveStudentResultsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $course_id;
    public array $results;
    public int $user_id;
    public  $exam_percentages;

    public function __construct($course_id, $results, $user_id, $exam_percentages)
    {
        $this->course_id = $course_id;
        $this->results = $results;
        $this->user_id = $user_id;
        $this->exam_percentages = $exam_percentages; 
    }

    public function handle()
    {
        $course = Course::with('book:id,pass_mark,makeup_mark')->findOrFail($this->course_id);
        $book = $course->book;
        $pass_mark = $book?->pass_mark ?? 50;
        $makeup_mark = $book?->makeup_mark ?? 40;
        $student_totals = [];

        foreach ($this->results as $student_id => $exam_scores) {

            $total = 0;

            foreach ($exam_scores as $exam_type_id => $score) {
            
                $max = $this->exam_percentages[$exam_type_id] ?? 100;
                $score = min(max($score, 0), $max);

                $total += $score;

                 // گرفتن مقدار قبلی
                $old = StudentExamScore::where([
                    'student_id' => $student_id,
                    'course_id' => $this->course_id,
                    'exam_type_id' => $exam_type_id,
                ])->first();

                // ذخیره مقدار جدید
                $new = StudentExamScore::updateOrCreate(
                    [
                        'student_id' => $student_id,
                        'course_id' => $this->course_id,
                        'exam_type_id' => $exam_type_id,
                    ],
                    [
                        'score' => $score,
                        'user_id' => $this->user_id,
                    ]
                );

                // فقط اگر تغییر کرده لاگ بساز
                if ($old && floatval($old->score) !== floatval($score)) {
                    StudentExamScoreLog::create([
                        'student_id'   => $student_id,
                        'course_id'    => $this->course_id,
                        'exam_type_id' => $exam_type_id,
                        'score_old'    => $old->score,
                        'score_new'    => $score,
                        'user_id'      => $this->user_id,
                    ]);
                }
            }

            $student_totals[$student_id] = $total;
        }

        // ذخیره total در student_course_results
        foreach ($student_totals as $student_id => $total) {

            if ($total >= $pass_mark) {
                $status = 'passed';
            } elseif ($total >= $makeup_mark) {
                $status = 'makeup';
            } else {
                $status = 'failed';
            }

            StudentCourseResult::updateOrCreate(
                [
                    'student_id' => $student_id,
                    'course_id' => $this->course_id,
                ],
                [
                    'total' => $total,
                    'status' => $status,
                    'pass_mark_snapshot' => $pass_mark,
                    'makeup_mark_snapshot' => $makeup_mark,
                    'user_id' => $this->user_id,
                ]
            );
        }
    }

}
