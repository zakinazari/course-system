<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Model;
use App\Models\CenterSettings\Book;
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
        return $this->belongsTo(Book::class);
    }

    
    public function movements()
    {
        return $this->hasMany(BookInventoryMovement::class, 'book_inventory_id');
    }
}
