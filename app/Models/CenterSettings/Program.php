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
        'section_id',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }
}
