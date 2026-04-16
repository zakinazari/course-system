<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Model;
use App\Models\CenterSettings\PhysicalBook;
class BookInventoryMovement extends Model
{
     protected $fillable = [
        'book_inventory_id',
        'quantity_change',
        'balance_after',
        'unit_price',
        'type',
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
}
