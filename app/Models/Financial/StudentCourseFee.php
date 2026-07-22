<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\CenterSettings\Branch;
use App\Models\Academic\Student;
use App\Models\Academic\Course;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\Auth;
use App\Models\User;
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
        'discount_amount',
        'special_discount_amount',
        'special_discount_status',
        'g_discount_amount',
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
        'discount_amount' => 'decimal:2',
        'g_discount_value' => 'decimal:2',
        'g_discount_amount' => 'decimal:2',
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

     // شرط شعبه، سکوپ
    protected static function booted()
    {
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
    }
}
