<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;
use App\Models\CenterSettings\Branch;
use App\Models\Hr\Employee;
use App\Models\User;
use App\Models\CenterSettings\Section;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class ExpenseBudget extends Model
{
     protected $fillable = [

        'expense_category_id',

        'branch_id',

        'section_id',

        'amount',

        'effective_from',

        'effective_to',

        'note',

        'user_id',

    ];

    protected $casts = [

        'effective_from' => 'date',

        'effective_to' => 'date',

        'amount' => 'decimal:2',

    ];


    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }


    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }


    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query, $date = null)
    {
        $date = $date ?? now()->toDateString();

        return $query
            ->whereDate('effective_from', '<=', $date)
            ->where(function ($q) use ($date) {

                $q->whereNull('effective_to')
                ->orWhereDate('effective_to', '>=', $date);

            });
    }

    protected static function booted()
    {
        
        static::saving(function ($expense) {
            if ($expense->unit_price && $expense->quantity) {
                $expense->total_amount = $expense->unit_price * $expense->quantity;
            }
        });

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
