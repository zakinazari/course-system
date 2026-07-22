<?php

namespace App\Models\CenterSettings;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = [
        'name',
    ];

    public function times()
    {
        return $this->hasMany(Time::class)->orderBy('start_time');
    }
}
