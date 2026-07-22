<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class EmployeeSecuritySaving extends Model
{
    protected $fillable = [
        'employee_id',
        'contract_id',
        'contract_type',
        'payroll_id',
        'payroll_type',
        'type',
        'amount',
        'transaction_date',
        'note',
        'user_id',
    ];


    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }


    public function contract()
    {
        return $this->morphTo();
    }


    public function payroll()
    {
        return $this->morphTo();
    }

}
