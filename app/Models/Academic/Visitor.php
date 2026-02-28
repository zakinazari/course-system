<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\VisitPurpose;
use App\Models\CenterSettings\ReferralSource;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\Auth;
class Visitor extends Model
{
    protected $fillable = [
        'name',
        'last_name',
        'father_name',
        'phone_no',
        'visit_date',
        'visit_purpose_id',
        'referral_source_id',
        'user_id',
        'branch_id',
        'memo',
    ];
    protected $casts = [
        'visit_date' => 'datetime',
    ];
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function referralSource(): BelongsTo
    {
        return $this->belongsTo(ReferralSource::class, 'referral_source_id');
    }

    public function visitPurpose(): BelongsTo
    {
        return $this->belongsTo(VisitPurpose::class, 'visit_purpose_id');
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
