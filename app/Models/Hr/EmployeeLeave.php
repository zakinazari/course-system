<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class EmployeeLeave extends Model
{
    protected $fillable = [

        'employee_id',

        'leave_type_id',

        'start_date',

        'end_date',

        'days',

        'status',

        'reason',

        'note',

        'approved_by',

        'approved_at',

        'user_id'

    ];

    protected $casts = [

        'start_date'=>'date',

        'end_date'=>'date',

        'approved_at'=>'datetime',

    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function contract()
    {
        return $this->morphTo();
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class,'approved_by');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
