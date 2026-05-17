<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Unit;
use App\Models\Hr\Employee;
use App\Models\User;
use App\Models\CenterSettings\Section;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class Expense extends Model
{
    protected $fillable = [
        'branch_id',
        'section_id',
        'expense_category_id',
        'shop_id',
        'employee_id',

        'name',

        'unit_price',
        'quantity',
        'total_amount',

        'unit_id',

        'note',
        'expense_date',

        'user_id',
    ];

    protected $casts = [
        'expense_date' => 'date',
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

    // ---------------- Category ----------------
    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    // ---------------- Unit ----------------
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // ---------------- User (created by) ----------------
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ---------------- Transactions (optional but powerful) ----------------
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'source_id')
            ->where('source_type', 'Expense');
    }

     // ---------------- Unit ----------------
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function purchasedByEmployee()
    {
        return $this->belongsTo(Employee::class,'employee_id');
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
