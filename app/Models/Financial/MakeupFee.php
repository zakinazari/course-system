<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;
use App\Models\Academic\Student;
use App\Models\Academic\Course;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\Auth;
use App\Models\User;
class MakeupFee extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'makeup_setting_id',
        'amount',
        'note',
        'payment_date',
        'user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
