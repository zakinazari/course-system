<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;
use App\Models\CenterSettings\Book;
class TemporaryPayrollDetail extends Model
{
     protected $fillable = [
        'temporary_payroll_id',
        'employee_id',
        'book_id',

        'amount_snapshot',
        'total_days_snapshot',
        'daily_rate_snapshot',

        'attendance_count',
        'total_salary',
    ];

    protected $casts = [
        'amount_snapshot' => 'decimal:2',
        'daily_rate_snapshot' => 'decimal:2',
        'total_salary' => 'decimal:2',
        'attendance_count' => 'integer',
        'total_days_snapshot' => 'integer',
    ];

    //  relations
    public function payroll()
    {
        return $this->belongsTo(TemporaryPayroll::class, 'temporary_payroll_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
