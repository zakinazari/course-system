<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Model;

class ScientificBoardMember extends Model
{

    protected $fillable = [
        'name_fa',
        'name_en',
        'board_id',
    ];

}
