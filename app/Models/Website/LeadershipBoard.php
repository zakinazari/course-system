<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Model;

class LeadershipBoard extends Model
{
    protected $table = 'leadership_board';
    protected $fillable = [
        'title_fa',
        'title_en',
        'content_fa',
        'content_en',
    ];
}
