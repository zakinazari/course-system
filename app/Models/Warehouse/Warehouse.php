<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\CenterSettings\Branch;
class Warehouse extends Model
{
     protected $fillable = [
        'name',
        'category_id',
        'branch_id'
    ];

    public function category()
    {
        return $this->belongsTo(WarehouseCategory::class, 'category_id');
    }

    
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    
    public function inventories()
    {
        return $this->hasMany(BookInventory::class);
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
