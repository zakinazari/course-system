<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Academic\CourseWaitingList;
use App\Models\CenterSettings\Book;
use App\Models\Academic\CourseStudent;
use App\Models\Assessment\StudentCourseResult;
use App\Models\Assessment\StudentExamScore;
use App\Models\Assessment\StudentExamScoreLog;
use App\Models\Assessment\StudentCourseResultLog;
use App\Models\Academic\Course;
use Illuminate\Support\Facades\DB;
class SaveStudentResultsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $course_id;
    public array $results;
    public int $user_id;
    public  $exam_percentages;
    public  $exam_period;

    public function __construct($course_id, $results, $user_id, $exam_percentages,$exam_period)
    {
        $this->course_id = $course_id;
        $this->results = $results;
        $this->user_id = $user_id;
        $this->exam_percentages = $exam_percentages; 
        $this->exam_period = $exam_period; 
    }

    public function handle()
    {
        DB::transaction(function () {

            $course = Course::with('book','book.examTypes')->findOrFail($this->course_id);
            $book = $course->book;
            $pass_mark = $book?->pass_mark ?? 50;
            $makeup_mark = $book?->makeup_mark ?? 40;
    
            $student_totals = [];

            // ============checking-------------------------
            foreach ($this->results as $student_id => $exam_scores) {

                $future_total = StudentExamScore::where('student_id', $student_id)
                    ->where('course_id', $this->course_id)
                    ->sum('score');

                foreach ($exam_scores as $exam_type_id => $score) {

                    $max = $this->exam_percentages[$exam_type_id] ?? 100;
                    $score = min(max($score, 0), $max);

                    $old_score = StudentExamScore::where([
                        'student_id' => $student_id,
                        'course_id' => $this->course_id,
                        'exam_type_id' => $exam_type_id,
                    ])->value('score') ?? 0;

                    $future_total = $future_total - $old_score + $score;
                }

                if ($future_total > 100) {
                    throw new \Exception(
                        "Total score cannot exceed 100 for student ID {$student_id}"
                    );
                }
            }
            
            foreach ($this->results as $student_id => $exam_scores) {

    
                foreach ($exam_scores as $exam_type_id => $score) {
                
                    $max = $this->exam_percentages[$exam_type_id] ?? 100;
                    $score = min(max($score, 0), $max);


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

                $student_totals[$student_id] = true;
            }

            // ذخیره total در student_course_results
            foreach ($student_totals as $student_id => $total) {

                $total = StudentExamScore::where('student_id', $student_id)
                ->where('course_id', $this->course_id)
                ->sum('score');
            
                /**
                 * =========================
                 * CHECK MIDTERM / FINAL
                 * =========================
                 */
    
                if ($this->exam_period==='midterm') {

                    // فقط یک status خاص
                    $status = 'in_progress';

                } else {

                    if ($total >= $pass_mark) {
                        $status = 'passed';
                    } elseif ($total >= $makeup_mark) {
                        $status = 'makeup';
                    } else {
                        $status = 'failed';
                    }
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

                
                // ====================================
                // change the status of student in course
                // ====================================

                if ($this->exam_period !== 'midterm') {

                    CourseStudent::where('student_id', $student_id)
                        ->where('course_id', $this->course_id)
                        ->update([
                            'status' => $status,
                        ]);

                    // ====================================
                    // Waiting List
                    // ====================================

                    CourseWaitingList::where('student_id', $student_id)
                    ->where('program_id', $book->program_id)
                    ->delete();

                    if ($status === 'passed') {

                        $next_book = Book::where('program_id', $book->program_id)
                            ->where('level_number', $book->level_number + 1)
                            ->first();

                        if ($next_book) {

                            CourseWaitingList::updateOrCreate(
                                [
                                    'student_id' => $student_id,
                                    'program_id' => $book->program_id,
                                    'book_id'    => $next_book->id,
                                ],
                                [
                                    'branch_id' => $course->branch_id,
                                    'shift_id'  => $course->shift_id,
                                    'status'    => $status,
                                    'user_id'   => $this->user_id,
                                ]
                            );
                        }

                    } else {

                        CourseWaitingList::updateOrCreate(
                            [
                                'student_id' => $student_id,
                                'program_id' => $book->program_id,
                                'book_id'    => $book->id,
                            ],
                            [
                                'branch_id' => $course->branch_id,
                                'shift_id'  => $course->shift_id,
                                'status'    => $status,
                                'user_id'   => $this->user_id,
                            ]
                        );
                    }
                }
            }
        });
    }

}
