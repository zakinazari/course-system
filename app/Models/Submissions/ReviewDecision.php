<?php

namespace App\Models\Submissions;

use Illuminate\Database\Eloquent\Model;

class ReviewDecision extends Model
{
     protected $fillable = [
        'review_id',
        'recommendation',
        'comments_fa',
        'comments_en',
        'assigned_at',
        'completed_at',
    ];
}
