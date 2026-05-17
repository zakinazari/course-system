<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;
use App\Models\CenterSettings\Branch;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class PermanentPayroll extends Model
{
    protected $fillable = [
        'employee_id',
        'branch_id',
        'permanent_contract_id',
        'month_id',
        'year',

        'gross_salary',
        'total_present_days',
        'over_time_hours',
        'over_time_amount',
        'taxi_fare',
        'credit_card',

        'tax',
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

        'total_present_days' => 'decimal:2',
        'over_time_hours' => 'decimal:2',
        'over_time_amount' => 'decimal:2',

        'tax' => 'decimal:2',

        'taxi_fare' => 'decimal:2',
        'credit_card' => 'decimal:2',

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

    public function PermanentContract()
    {
        return $this->belongsTo(PermanentContract::class);
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
