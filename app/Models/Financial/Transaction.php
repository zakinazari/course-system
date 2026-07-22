<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Section;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        'status',
        'from_account_id',
        'to_account_id',
        'approved_by',
        'approved_at',
        'transfer_group_id',
        'module_type',//finance ,book
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
        'created_date' => 'date',
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

    public function fromAccount()
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    public function toAccount()
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'created_by');
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
