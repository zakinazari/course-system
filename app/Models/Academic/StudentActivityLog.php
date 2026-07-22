<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;

class StudentActivityLog extends Model
{
     protected $fillable = [
        'student_id',
        'course_id',
        'category_id',
        'title',
        'description',
        'activity_date',
        'created_by',
    ];

    protected $casts = [
        'activity_date' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(ActivityCategory::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
