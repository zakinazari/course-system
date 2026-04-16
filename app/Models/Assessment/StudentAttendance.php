<?php

namespace App\Models\Assessment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Academic\Course;
use App\Models\Academic\Student;
use App\Models\Hr\Employee;

class StudentAttendance extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'teacher_id',
        'attendance_date',
        'status',
        'note',
        'recorded_by',
    ];

    protected $casts = [
        'attendance_date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->BelongsTo(Student::class);
    }
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'teacher_id');
    }

    public function course(): BelongsTo
    {
        return $this->BelongsTo(Course::class);
    }

}
