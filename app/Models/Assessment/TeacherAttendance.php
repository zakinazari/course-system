<?php

namespace App\Models\Assessment;
use App\Models\Academic\Course;
use Illuminate\Database\Eloquent\Model;

class TeacherAttendance extends Model
{

    protected $fillable = [
        'course_id',
        'teacher_id',
        'status',
        'note',
        'unit_note',
        'recorded_by',
        'attendance_date',
        'unit_number',
        'lesson_status',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function unit()
    {
        return $this->belongsTo(CourseUnit::class, 'course_unit_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
