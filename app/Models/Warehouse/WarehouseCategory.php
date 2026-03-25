<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Model;

class WarehouseCategory extends Model
{
    protected $fillable = [
        'name',
    ];

    public function warehouse()
    {
        return $this->hasMany(Warehouse::class,'category_id');
    }
}
