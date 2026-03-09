<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;

class StudentCourseFeeInstallment extends Model
{
     protected $fillable = [
        'student_course_fee_id',
        'due_date',
        'amount',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
    ];

    public function studentCourseFee()
    {
        return $this->belongsTo(StudentCourseFee::class);
    }

    public function payments()
    {
        return $this->hasOne(StudentCourseFeePayment::class,'installment_id');
    }
}
