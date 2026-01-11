<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Model;

class Index extends Model
{
    protected $table = 'indexes';
    protected $fillable = [
        'name',
        'url',
        'image',
    ];
}
