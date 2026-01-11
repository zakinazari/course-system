<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class MenuSection extends Model
{

    public function menu():HasMany
    {
        return $this->hasMany(Menu::class, 'section_id')->where('type_id',1)
        ->where('status', 1)
        ->orderBy('order', 'asc');
    }

    public function getSectionNameAttribute()
    {
        $locale = app()->getLocale();
        return $this->{'section_name_'.$locale};
    }
}
