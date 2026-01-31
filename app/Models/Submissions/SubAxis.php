<?php

namespace App\Models\Submissions;

use Illuminate\Database\Eloquent\Model;

class SubAxis extends Model
{
    protected $fillable = [
        'title_fa',
        'title_en',
        'main_axis_id',
    ];

    public function mainAxis()
    {
        return $this->belongsTo(MainAxis::class);
    }
}
