<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;
use App\Models\CenterSettings\Branch;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeAttendance extends Model
{
    protected $fillable = [
        'employee_id',
        'branch_id',
        'attendance_date',
        'status',
        'user_id',
    ];

    protected $casts = [
        'attendance_date' => 'date',

    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class);
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
