<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Model;
use App\Models\CenterSettings\PhysicalBook;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class BookInventoryMovement extends Model
{
     protected $fillable = [
        'book_inventory_id',
        'quantity_change',
        'balance_after',
        'unit_price',
        'type',
        'transfer_group_id',
        'note',
        'user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function inventory()
    {
        return $this->belongsTo(BookInventory::class, 'book_inventory_id');
    }

    public function warehouse()
    {
        return $this->hasOneThrough(
            Warehouse::class,
            BookInventory::class,
            'id', // Foreign key on inventories
            'id', // Foreign key on warehouse
            'book_inventory_id',
            'warehouse_id'
        );
    }

    public function book()
    {
        return $this->hasOneThrough(
            PhysicalBook::class,
            BookInventory::class,
            'id',
            'id',
            'book_inventory_id',
            'book_id'
        );
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
            $builder->whereHas('inventory.warehouse', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
        });
    }
}
