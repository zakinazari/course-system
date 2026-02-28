<?php

namespace App\Models\CenterSettings;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class PlacementTestSetting extends Model
{
    protected $fillable = [
        'validity_months',
        'has_fee',
        'fee_amount',
    ];

    protected $casts = [
        'has_fee' => 'boolean',
    ];
}
