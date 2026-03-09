<?php

namespace App\Models\Assessment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Academic\Course;
use App\Models\Academic\Student;
class StudentCourseResult extends Model
{
    protected $table = 'student_course_results';

    protected $fillable = [
        'student_id',
        'course_id',
        'midterm',
        'attendance',
        'cognitive',
        'final',
        'total',
        'status',
        'pass_mark_snapshot',
        'excellent_mark_snapshot',
        'is_finalized',
        'finalized_at',
        'finalized_by',
        'user_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function logs()
    {
        return $this->hasMany(StudentCourseResultLog::class, 'student_course_result_id');
    }
}
