<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;

class AssetCategory extends Model
{
    protected $fillable =[
        'name',
        'code',
    ];
}
