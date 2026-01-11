<?php

namespace App\Models\Issues;

use Illuminate\Database\Eloquent\Model;
use App\Models\Submissions\Submission;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Issue extends Model
{
    protected $fillable = [
        'volume',        
        'number',       
        'title_fa',      
        'title_en',      
        'cover_image',   
        'status',
        'published_at',
    ];

    public function submissions():HasMany
    {
        return $this->hasMany(Submission::class,'issue_id','id');
    }
}
