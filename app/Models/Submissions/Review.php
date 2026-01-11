<?php

namespace App\Models\Submissions;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class Review extends Model
{


    protected $fillable = [
        'submission_id',
        'reviewer_id',
        'round',
        'status',
        'editor_message_fa',
        'editor_message_en',
        'decline_reason_fa',
        'decline_reason_en',
        'assigned_at',
        'completed_at',
    ];

    
    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
    public function decision()
    {
        return $this->hasOne(ReviewDecision::class, 'review_id');
    }
    public function file()
    {
        return $this->hasMany(ReviewFile::class, 'review_id');
    }
}
