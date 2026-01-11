<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;

class MenuType extends Model
{
    public function getTypeNameAttribute()
    {
        $locale = app()->getLocale();
        return $this->{'type_name_'.$locale};
    }
}
