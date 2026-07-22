<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class StudentCourseFeePayment extends Model
{
     protected $fillable = [
        'student_course_fee_id',
        'installment_id',
        'amount',
        'payment_date',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function studentCourseFee()
    {
        return $this->belongsTo(StudentCourseFee::class);
    }

    public function installment()
    {
        return $this->belongsTo(StudentCourseFeeInstallment::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
