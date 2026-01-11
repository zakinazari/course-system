<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class ScientificBoard extends Model
{
     protected $table="scientific_board";

    protected $fillable = [
        'name_fa',
        'name_en',
    ];

    public function members(): HasMany
    {
        return $this->hasMany(ScientificBoardMember::class,'board_id');
    }
}
