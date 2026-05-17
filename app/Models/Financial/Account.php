<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Section;
class Account extends Model
{
    protected $fillable = [
        'branch_id',
        'name',
        'type',
        'category',
        'is_default',
        'balance',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'balance' => 'decimal:2',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'account_id');
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
