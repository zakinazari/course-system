<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Book;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class GeneralDiscount extends Model
{
    protected $fillable = [
        'branch_id',
        'program_id',
        'book_id',
        'discount_amount',
        'memo',
        'status',
        'user_id',
    ];

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

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'book_id');
    }
}
