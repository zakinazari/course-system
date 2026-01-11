<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Model;

class GazetteFile extends Model
{
    protected $fillable = [
        'gazette_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
    ];


    public function gazette(): BelongsTo
    {
        return $this->belongsTo(Gazette::class);
    }
}
