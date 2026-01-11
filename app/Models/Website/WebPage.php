<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class WebPage extends Model
{
    
    protected $fillable = [   
        'name_fa',      
        'name_en',      
        'title_fa',      
        'title_en',      
        'content_fa',      
        'content_en',      
        'cover_image',   
    ];

    public function files(): HasMany
    {
        return $this->hasMany(WebPageFile::class,'page_id');
    }
}
