<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Model;

class AboutUs extends Model
{
     protected $fillable = [
        'title_fa',
        'title_en',
        'phone',
        'email',
        'facebook',
        'website',
        'address_fa',
        'address_en',
    ];
}
