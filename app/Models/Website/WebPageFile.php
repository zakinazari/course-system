<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Model;

class WebPageFile extends Model
{
     protected $fillable = [
        'page_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
    ];

}
