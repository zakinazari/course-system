<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;
use App\Models\Academic\Student;
use App\Models\CenterSettings\Branch;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\Auth;
use App\Models\User;
class StudentOtherFee extends Model
{
     protected $fillable = [
        'student_id',
        'fee_type_id',
        'branch_id',
        'amount',
        'payment_date',
        'notes',
        'user_id'
    ];

     protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function feeType()
    {
        return $this->belongsTo(FeeType::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
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
