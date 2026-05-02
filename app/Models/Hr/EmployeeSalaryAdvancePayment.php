<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;
use App\Models\CenterSettings\Month;
use App\Models\Hr\Employee;
class EmployeeSalaryAdvancePayment extends Model
{
    protected $fillable = [
        'employee_salary_advance_id',
        'employee_id',
        'month_id',
        'year',
        'amount',
        'payment_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function employeeSalaryAdvance()
    {
        return $this->belongsTo(EmployeeSalaryAdvance::class, 'employee_salary_advance_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function month()
    {
        return $this->belongsTo(Month::class);
    }
}
