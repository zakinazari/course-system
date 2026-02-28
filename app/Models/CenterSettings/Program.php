<?php

namespace App\Models\CenterSettings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Program extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'status',
    ];
}
