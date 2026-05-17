<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;

class DailyBranchReport extends Model
{
    protected $fillable = [
        'branch_id',
        'report_date',
        'total_income',
        'total_expense',
        'net_balance',
        'status',
        'submitted_by',
        'submitted_at',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'report_date' => 'date',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'total_income' => 'decimal:2',
        'total_expense' => 'decimal:2',
        'net_balance' => 'decimal:2',
    ];
}
