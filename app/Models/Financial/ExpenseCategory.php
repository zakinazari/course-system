<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $fillable = [
        'name',
        'type',
    ];
}
