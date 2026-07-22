<?php

namespace App\Models\Hr;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Section;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class PermanentContract extends Model
{
    protected $fillable = [
        'position_id',
        'employee_id',
        'branch_id',
        'section_id',
        'basic_salary',
        'taxi_fare',
        'credit_card',
        'start_date',
        'end_date',
        'status',
        'security_saving_amount',
        'security_saving_monthly_amount',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    
    public function position()
    {
        return $this->belongsTo(Position::class);
    }
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaves()
    {
        return $this->morphMany(
            EmployeeLeave::class,
            'contract'
        );
    }

    public function securitySavings()
    {
        return $this->morphMany(
            EmployeeSecuritySaving::class,
            'contract'
        );
    }

    public function getSecuritySavingBalanceAttribute()
    {
        return $this->securitySavings()
            ->where('type', 'deposit')
            ->sum('amount')
            -
            $this->securitySavings()
            ->whereIn('type', ['refund', 'deduction'])
            ->sum('amount');
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
