<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;

class CourseStudent extends Model
{
    public $table = 'course_student';
    protected $fillable = [
        'course_id',
        'student_id',
        'status',
        'enrolled_at',
    ];

}
