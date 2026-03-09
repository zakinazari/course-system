<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\CenterSettings\Branch;
use App\Models\Academic\Student;
use App\Models\Academic\Course;
class StudentCourseFee extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'payment_type',
        'fee_amount',
        'discount_type',
        'discount_provider_id',
        'discount_value',
        'discount_reason',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'status',
        'branch_id',
        'user_id',
    ];

    protected $casts = [
        'fee_amount' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];

    
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function installments(): HasMany
    {
        return $this->hasMany(StudentCourseFeeInstallment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(StudentCourseFeePayment::class);
    }
}
