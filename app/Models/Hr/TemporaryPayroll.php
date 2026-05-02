<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;
use App\Models\CenterSettings\Branch;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class TemporaryPayroll extends Model
{
     protected $fillable = [
        'employee_id',
        'temporary_contract_id',
        'branch_id',
        'month_id',
        'year',

        'gross_salary',
        'total_teaching_days',
        'total_present_days',

        'tax',

        'taxi_fare',
        'credit_card',
        'food_deduction',
        'advance_deduction',

        'total_deductions',
        'net_salary',
        'payment_date',
        'paid_by',

        'status',
        'user_id',
    ];

    protected $casts = [
        'gross_salary' => 'decimal:2',
        'total_teaching_days' => 'decimal:2',
        'total_present_days' => 'decimal:2',

        'tax' => 'decimal:2',

        'taxi_fare' => 'decimal:2',
        'credit_card' => 'decimal:2',
        'food_deduction' => 'decimal:2',
        'advance_deduction' => 'decimal:2',

        'total_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'payment_date' => 'date',
    ];

    // ======================
    // Relationships
    // ======================

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function temporaryContract()
    {
        return $this->belongsTo(TemporaryContract::class);
    }


    protected static function booted()
    {
        //  Global Scope شعبه
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
