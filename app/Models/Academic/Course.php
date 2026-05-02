<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Program;
use App\Models\CenterSettings\Book;
use App\Models\CenterSettings\Shift;
use App\Models\CenterSettings\Time;
use App\Models\CenterSettings\Classroom;
use App\Models\Hr\Employee;
use App\Models\Assessment\CourseUnit;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Assessment\TeacherAttendance;

class Course extends Model
{
     protected $fillable = [
        'name',
        'course_code',
        'course_type_id',
        'branch_id',
        'program_id',
        'book_id',
        'classroom_id',
        'shift_id',
        'time_id',
        'total_teaching_days',
        'min_capacity',
        'max_capacity',
        'start_date',
        'end_date',
        'mid_exam_date',
        'final_exam_date',
        'status',
        'teacher_id',
        'image',
        'user_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'mid_exam_date' => 'date',
        'final_exam_date' => 'date',
    ];

    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            'draft'     => 'bg-secondary',
            'scheduled' => 'bg-info',
            'ongoing'   => 'bg-success',
            'completed' => 'bg-success',
            'cancelled' => 'bg-danger',
            default     => 'bg-dark',
        };
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class, 'classroom_id');
    }
    
    public function courseType(): BelongsTo
    {
        return $this->belongsTo(CourseType::class, 'course_type_id');
    }
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }
    public function time(): BelongsTo
    {
        return $this->belongsTo(Time::class, 'time_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'teacher_id');
    }

    public function teachers()
    {
         return $this->belongsToMany(
            Employee::class,    // مدل مرتبط
            'course_teacher',   // جدول pivot
            'course_id',            // کلید خارجی مدل جاری
            'teacher_id'             // کلید خارجی مدل مرتبط
        )->withTimestamps();      // اگر جدول pivot timestamps دارد
    }

    public function students()
    {
        return $this->belongsToMany(Student::class)
                    ->withPivot(['id','status', 'enrolled_at'])
                    ->withTimestamps();
    }

    
    public function teacherAttendances()
    {
        return $this->hasMany(TeacherAttendance::class, 'course_id');
    }

    // شرط شعبه، سکوپ
    protected static function booted()
    {
        //  Global Scope شعبه
        static::addGlobalScope('branch', function (Builder $builder) {

            if (!Auth::check()) {
                return;
            }

            $user = Auth::user();

            if ($user->isDeveloper() || $user->isAdmin()) {
                return;
            }
 
            $builder->where('branch_id', $user->branch_id);
        });

        //  ساخت خودکار course_name
        static::creating(function ($course) {

            DB::transaction(function () use ($course) {

                $branchCode = $course->branch?->code;
                $bookAbb = $course->book?->abbreviation;
                $classroom = $course?->classroom?->name;
                $time = $course->time?->start_time->format('h:i A');
                $teacher =$course->teacher?->last_name;
                $course->name =
                    $branchCode .'-'. $bookAbb .'-'.$teacher.'-'. $classroom.'-'.$time;
            });
        });

        static::updating(function ($course) {
            
            if ($course->isDirty('branch_id')) {
                DB::transaction(function () use ($course) {
                    $branchCode = $course->branch?->code;
                    $bookAbb = $course->book?->abbreviation;
                    $classroom = $course?->classroom?->name;
                    $time = $course->time?->start_time->format('h:i A');
                    $teacher =$course->teacher?->last_name;
                    $course->name =
                    $branchCode .'-'. $bookAbb .'-'.$teacher.'-'. $classroom.'-'.$time;
                });
            }
        });
    }
}
