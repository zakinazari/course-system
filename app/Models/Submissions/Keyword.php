<?php

namespace App\Models\Submissions;

use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    protected $fillable = [
        'keyword_en',
        'keyword_fa',
    ];
    public function submissions()
    {
        return $this->belongsToMany(Submission::class, 'submission_keywords', 'keyword_id', 'submission_id')
            ->withTimestamps();
    }
}
