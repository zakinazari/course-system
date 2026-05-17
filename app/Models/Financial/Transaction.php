<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Section;
use App\Models\User;
class Transaction extends Model
{
    protected $fillable = [
        'account_id',
        'branch_id',
        'section_id',
        'type',
        'amount',
        'category',
        'source_type',
        'source_id',
        'action',
        'transaction_date',
        'created_by',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
    ];

     // ---------------- Branch ----------------
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // ---------------- Section ----------------
    public function section()
    {
        return $this->belongsTo(Section::class);
    }
    // ---------------- Account ----------------
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
