<?php

namespace App\Models\Assessment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Academic\Course;
use App\Models\Academic\Student;
class StudentCourseResultLog extends Model
{
    protected $table = 'student_course_result_logs';

    protected $fillable = [
        'student_course_result_id', 
        'midterm_old',
        'final_old',
        'cognitive_old',
        'attendance_old',
        'total_old',
        'status_old',
        'pass_mark_snapshot_old',
        'excellent_mark_snapshot_old',


        'midterm_new',
        'final_new',
        'cognitive_new',
        'attendance_new',
        'total_new',
        'status_new',
        'pass_mark_snapshot_new',
        'excellent_mark_snapshot_new',
        'user_id', 
    ];

    public function result()
    {
        return $this->belongsTo(StudentCourseResult::class, 'student_course_result_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
