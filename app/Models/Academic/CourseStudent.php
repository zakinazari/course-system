<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Assessment\StudentAttendance;
use App\Models\Assessment\StudentCourseResult;
use App\Models\Assessment\StudentExamScore;
class CourseStudent extends Model
{
    public $table = 'course_student';
    
    protected $fillable = [
        'course_id',
        'student_id',
        'status',
        'enrolled_at',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function attendances()
    {
        return $this->hasMany(StudentAttendance::class, 'student_id', 'student_id');
    }

    public function examScores()
    {
        return $this->hasMany(StudentExamScore::class, 'student_id', 'student_id');
    }

    public function courseResult()
    {
        return $this->hasOne(StudentCourseResult::class, 'student_id', 'student_id');
    }

}
