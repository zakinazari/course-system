<?php

namespace App\Models\Financial;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Section;
use App\Models\Hr\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class AssetMovement extends Model
{
    protected $fillable = [
        'asset_id',
        'employee_id',
        'section_id',
        'branch_id',
        'type',
        'movement_date',
        'note',
        'user_id',
    ];

    protected $casts = [
        'movement_date' => 'date',
    ];


    // Asset
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    // Employee
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Section
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    // Branch
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // User who performed the action
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
