<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Menu extends Model
{

    protected $fillable = [
        'name_pa',
        'name_fa',
        'name_en',
        'url',
        'icon',
        'category',
        'status',
        'order',
        'parent_id',
        'grand_parent_id',
        'type_id',
        'section_id',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(MenuType::class, 'type_id');
    }
    public function section(): BelongsTo
    {
        return $this->belongsTo(MenuSection::class, 'section_id');
    }

    public function subMenu():HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')->where('type_id', 2)->where('status', 1)->orderBy('order', 'ASC');
    }

    public function subMenuSub():HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')->where('type_id', 3)->where('status', 1)->orderBy('order', 'ASC');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }
    public function grandParent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'grand_parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->where('status', 1)->orderBy('order', 'asc');
    }

    public function permission():HasMany
    {
        return $this->hasMany(Permission::class, 'menu_id');
    }

    public function getNameAttribute()
    {
        $locale = app()->getLocale();
        return $this->{'name_'.$locale};
    }
}
