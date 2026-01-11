<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Settings\MenuType;
class WebMenu extends Model
{
    protected $fillable = [
        'name_fa',
        'name_en',
        'status',
        'order',
        'parent_id',
        'grand_parent_id',
        'type_id',
        'page_id',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(MenuType::class, 'type_id');
    }
    public function page(): BelongsTo
    {
        return $this->belongsTo(WebPage::class, 'page_id');
    }

    public function subMenu():HasMany
    {
        return $this->hasMany(WebMenu::class, 'parent_id')->where('type_id', 2)->where('status', 1)->orderBy('order', 'ASC');
    }

    public function subMenuSub():HasMany
    {
        return $this->hasMany(WebMenu::class, 'parent_id')->where('type_id', 3)->where('status', 1)->orderBy('order', 'ASC');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(WebMenu::class, 'parent_id');
    }

    public function grandParent(): BelongsTo
    {
        return $this->belongsTo(WebMenu::class, 'grand_parent_id');
    }

    public function children()
    {
        return $this->hasMany(WebMenu::class, 'parent_id')->where('status', 1)->orderBy('order', 'asc');
    }
}
