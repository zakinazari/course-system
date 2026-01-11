<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class Post extends Model
{
    protected $table = 'posts';
    protected $fillable = [
        'title_fa',
        'title_en',
        'content_fa',
        'content_en',
        'status',
        'published_at',
        'image',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
