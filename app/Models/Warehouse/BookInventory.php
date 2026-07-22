<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Model;
use App\Models\CenterSettings\PhysicalBook;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class BookInventory extends Model
{
    protected $fillable = [
        'warehouse_id',
        'book_id',
        'quantity',
    ];

    
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    
    public function book()
    {
        return $this->belongsTo(PhysicalBook::class);
    }

    
    public function movements()
    {
        return $this->hasMany(BookInventoryMovement::class, 'book_inventory_id');
    }

    public function increment($column, $amount = 1, array $extra = [])
    {
        $this->setAttribute(
            $column,
            $this->getAttribute($column) + $amount
        );

        $this->fill($extra);

        $this->save();

        return true;
    }


    public function decrement($column, $amount = 1, array $extra = [])
    {
        $this->setAttribute(
            $column,
            $this->getAttribute($column) - $amount
        );

        $this->fill($extra);

        $this->save();

        return true;
    }

    protected static function booted()
    {

        static::addGlobalScope('branch', function (Builder $builder) {

            if (!Auth::check()) {
                return;
            }

            $user = Auth::user();

            // developer/admin همه را ببینند
            if ($user->isDeveloper() || $user->isAdmin()) {
                return;
            }

            // فیلتر بر اساس warehouse.branch_id
            $builder->whereHas('warehouse', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
        });
    }
}
