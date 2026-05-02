<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;
use App\Models\CenterSettings\Book;
class BookSalaryRate extends Model
{
    protected $fillable = [
        'book_id',
        'temporary_contract_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function temporaryContract()
    {
        return $this->belongsTo(TemporaryContract::class);
    }
}
