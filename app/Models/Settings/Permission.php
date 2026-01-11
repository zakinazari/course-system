<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'menu_id',
        'role_id',
        'action_id',
    ];
}
