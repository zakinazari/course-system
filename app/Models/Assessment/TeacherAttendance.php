<?php

namespace App\Models\Assessment;
use App\Models\Academic\Course;
use App\Models\Hr\Employee;
use App\Models\Hr\LeaveType;
use Illuminate\Database\Eloquent\Model;

class TeacherAttendance extends Model
{

    protected $fillable = [
        'course_id',
        'teacher_id',
        'status',
        'leave_type_id',
        'note',
        'unit_note',
        'recorded_by',
        'attendance_date',
        'unit_number',
        'lesson_status',
    ];

    protected $casts = [
        'attendance_date' => 'date',

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
        return $this->belongsTo(Employee::class, 'teacher_id');
    }


    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }
}
