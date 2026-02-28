<?php

namespace App\Models\Assessment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Academic\Course;
use App\Models\Academic\Student;

class StudentAttendance extends Model
{
     protected $fillable = [
        'student_id',
        'course_id',
        'attendance_date',
        'status',
        'note',
        'recorded_by',
    ];

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function course(): HasOne
    {
        return $this->hasOne(Course::class);
    }
}
