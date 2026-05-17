<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;
use App\Models\CenterSettings\Section;
class FeeType extends Model
{
    protected $fillable = [
        'name',
        'section_id',
        'code',
        'fee_amount'
    ];
    
    public function studentFees()
    {
        return $this->hasMany(StudentOtherFee::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }
}
