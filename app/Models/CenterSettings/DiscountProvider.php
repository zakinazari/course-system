<?php

namespace App\Models\CenterSettings;

use Illuminate\Database\Eloquent\Model;

class DiscountProvider extends Model
{
    protected $table = 'discount_providers';

    protected $fillable = [
        'name',
        'last_name',
        'phone_no',
        'monthly_discount_total',
        'status',
    ];

    protected $casts = [
        'monthly_discount_total' => 'decimal:2',
        'status' => 'boolean',
    ];

    public function studentCourseFees()
    {
        return $this->hasMany(StudentCourseFee::class, 'discount_provider_id');
    }

}
