<?php

namespace App\Models\Submissions;

use Illuminate\Database\Eloquent\Model;

class AcceptedAbstract extends Model
{
     protected $fillable = [
        'title_fa',
        'title_en',
        'author_name',
        'author_last_name',
    ];
}
