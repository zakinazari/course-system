<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Model;

class Ruling extends Model
{
     protected $fillable = [
        'gazette_id',
        'title_fa',
        'title_en',
        'ruling_number',
        'ruling_date',
        'type',
    ];

    public function gazette()
    {
        return $this->belongsTo(Gazette::class);
    }
    public function files()
    {
        return $this->hasMany(RulingFile::class);
    }
}
