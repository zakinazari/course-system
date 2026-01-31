<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Gazette extends Model
{
    protected $fillable = [
        'title_fa',
        'title_en',
        'gazette_number',
        'publish_date',
        'status',
    ];

    public function files(): HasMany
    {
        return $this->hasMany(GazetteFile::class);
    }
    public function ruling(): HasMany
    {
        return $this->hasMany(Ruling::class);
    }
}
