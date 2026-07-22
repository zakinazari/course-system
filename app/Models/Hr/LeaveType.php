<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $fillable = [

        'name',

        'yearly_limit',

        'is_paid',

        'status'

    ];

    public function employeeLeaves()
    {
        return $this->hasMany(EmployeeLeave::class);
    }
}
