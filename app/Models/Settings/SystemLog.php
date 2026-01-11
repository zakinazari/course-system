<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
      protected $fillable = [
        'user_id',
        'section',
        'type_id',
       
    ];
}
