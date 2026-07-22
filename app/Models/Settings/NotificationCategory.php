<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;

class NotificationCategory extends Model
{
     protected $fillable = [

        'name',

        'slug',

        'description',

    ];

    public function roles()
    {
       return $this->belongsToMany(
        AccessRole::class,
            'notification_category_role',
            'notification_category_id',
            'role_id'
        );
    }
}
