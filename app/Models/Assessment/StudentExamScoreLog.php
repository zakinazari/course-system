<?php

namespace App\Models\Assessment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Academic\Course;
use App\Models\Academic\Student;
use App\Models\CenterSettings\ExamType;
class StudentExamScoreLog extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'exam_type_id',
        'score_old',
        'score_new',
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

    public function examType()
    {
        return $this->belongsTo(ExamType::class);
    }
}
