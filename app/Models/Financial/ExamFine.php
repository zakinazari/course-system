<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Academic\Course;
use App\Models\Academic\Student;
use App\Models\CenterSettings\ExamType;
class ExamFine extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'exam_type_id',
        'amount',
        'status',
        'reason',
        'exam_date',
        'payment_date',
    ];
    protected $casts = [
        'amount' => 'decimal:2',
        'exam_date' => 'date',
        'payment_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function examType()
    {
        return $this->belongsTo(ExamType::class);
    }
}
