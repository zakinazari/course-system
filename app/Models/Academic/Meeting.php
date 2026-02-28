<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\CenterSettings\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\Auth;
class Meeting extends Model
{
       protected $fillable = [
        'name',
        'last_name',
        'father_name',
        'phone_no',
        'subject',
        'status',
        'date',
        'reference_id',
        'user_id',
        'branch_id',
    ];
    protected $casts = [
        'date' => 'datetime',
    ];
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function reference(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reference_id');
    }

    // شرط شعبه، سکوپ
    protected static function booted()
    {
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
