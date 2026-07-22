<?php

namespace App\Models\CenterSettings;

use Illuminate\Database\Eloquent\Model;

class BookSpecialDiscount extends Model
{
     protected $fillable = [
        'book_id',
        'type',
        'amount',
        'duration_days',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'duration_days' => 'integer',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
