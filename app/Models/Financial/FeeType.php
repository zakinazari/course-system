<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;

class FeeType extends Model
{
    protected $fillable = [
        'name',
        'code',
        'fee_amount'
    ];

    public function studentFees()
    {
        return $this->hasMany(StudentOtherFee::class);
    }
}
