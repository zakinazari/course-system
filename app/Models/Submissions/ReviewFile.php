<?php

namespace App\Models\Submissions;

use Illuminate\Database\Eloquent\Model;

class ReviewFile extends Model
{
    protected $fillable = [
        'review_id',
        'file_path',
        'original_name',
        'mime',
        'type',
        'size',
    ];
}
