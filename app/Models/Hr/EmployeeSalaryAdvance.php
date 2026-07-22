<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;
use App\Models\CenterSettings\Month;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Section;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
class EmployeeSalaryAdvance extends Model
{
     protected $fillable = [
        'employee_id',
        'branch_id',
        'section_id',
        'total_amount',
        'remaining_amount',
        'advance_date',
        'auto_deduct',
        'status',
        'note',
        'user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'advance_date' => 'date',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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
