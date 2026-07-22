<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;

class MakeupSetting extends Model
{
    protected $fillable = [
        'name',
        'fee_amount',
        'exam_valid_days',
        'fee_valid_days',
        'status',
        'note',
        'user_id',
    ];

    protected $casts = [
        'fee_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

}
