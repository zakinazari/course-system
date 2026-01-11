<?php

namespace App\Models\Submissions;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class EditorialDecision extends Model
{
     protected $fillable = [
        'submission_id',
        'editor_id',
        'decision',
        'comments_fa',
        'comments_en',
        'round',
    ];

    public function editor()
    {
        return $this->belongsTo(User::class, 'editor_id');
    }
    
}
