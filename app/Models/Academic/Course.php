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
use App\Models\CenterSettings\Classroom;
use App\Models\Hr\Employee;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        'start_time',
        'end_time',
        'total_teaching_days',
        'min_capacity',
        'max_capacity',
        'start_date',
        'end_date',
        'mid_exam_date',
        'final_exam_date',
        'status',
        'image',
        'user_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'mid_exam_date' => 'date',
        'final_exam_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
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
                $classroom = $course->classroom?->name;
                $shift = $course->shift?->name;
                $start_date = $course->start_date->format('Y-m-d');
                $course->name =
                    $branchCode .'-'. $bookAbb .'-'. $classroom.'-'.$shift.'-'.$start_date;
            });
        });

        static::updating(function ($course) {
            
            if ($course->isDirty('branch_id')) {
                DB::transaction(function () use ($course) {
                    $branchCode = $course->branch?->code;
                    $bookAbb = $course->book?->abbreviation;
                    $classroom = $course->classroom?->name;
                    $shift = $course->shift?->name;
                    $start_date = $course->start_date->format('Y-m-d');
                    $course->name =
                    $branchCode .'-'. $bookAbb .'-'. $classroom .'-'.$shift.'-'.$start_date;
                });
            }
        });
    }
}
