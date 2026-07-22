<?php

namespace App\Models\CenterSettings;

use Illuminate\Database\Eloquent\Model;

class PhysicalBook extends Model
{
    protected $fillable = [
        'name',
        'book_id',
        'price',
        'minimum_stock',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }
}
