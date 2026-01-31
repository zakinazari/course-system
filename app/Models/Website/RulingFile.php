<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Model;

class RulingFile extends Model
{
    protected $fillable = [
        'ruling_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    public function ruling()
    {
        return $this->belongsTo(Ruling::class);
    }
}
