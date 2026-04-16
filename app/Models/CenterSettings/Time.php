<?php

namespace App\Models\CenterSettings;

use Illuminate\Database\Eloquent\Model;

class Time extends Model
{
    protected $fillable = [
        'shift_id',
        'start_time',
        'end_time',
    ];

    
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }
}
