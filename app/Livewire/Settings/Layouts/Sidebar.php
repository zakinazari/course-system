<?php

namespace App\Livewire\Settings\Layouts;

use Livewire\Component;
use App\Models\Settings\Menu;
use App\Models\Settings\MenuSection;
use Auth;
class Sidebar extends Component
{
    protected $listeners = ['setActiveMenuFromPage' => 'setActiveMenu'];
    
    public $menu_section= [];
    public $active_menu_id;
    public $active_sub_menu_id;
    public $active_sub_menu_sub_id;

    public function setActiveMenu($menu_id)
    {
        $m = Menu::find($menu_id);
        if (!$m) return; 
        if ($m->type_id == 1) {
            $this->active_menu_id = $m->id;
            $this->active_sub_menu_id = null;
            $this->active_sub_menu_sub_id = null;
        } elseif ($m->type_id == 2) {
            $this->active_menu_id = $m->parent_id ?? null; 
            $this->active_sub_menu_id = $m->id;
            $this->active_sub_menu_sub_id = null;
        } elseif ($m->type_id == 3) {
            $this->active_menu_id = $m->grand_parent_id ?? null; 
            $this->active_sub_menu_id = $m->parent_id ?? null;
            $this->active_sub_menu_sub_id = $m->id;
        }
    }

    public function mount()
    {
       
    }

    public function render()
    {
        $currentUser = auth()->user();

        // اگر Developer است، همه منوها بدون محدودیت نشان داده شود
        if ($currentUser->isDeveloper()) {
            $this->menu_section = MenuSection::with([
                'menu' => fn($query) => $query->where('status', 1)
                    ->orderBy('order', 'ASC')
                    ->with([
                        'subMenu' => fn($q) => $q->where('status', 1)
                            ->orderBy('order', 'ASC')
                            ->with([
                                'subMenuSub' => fn($qq) => $qq->where('status', 1)
                                    ->orderBy('order', 'ASC')
                            ])
                    ])
            ])->orderBy('order', 'ASC')->get();

            return view('livewire.settings.layouts.sidebar');
        }

        // اگر Developer نیست (Admin یا کاربران عادی)
        $role_ids = $currentUser->role_ids;

        $this->menu_section = MenuSection::with([
            'menu' => function($query) use ($role_ids) {
                $query->where('status', 1)
                    ->whereHas('permission', fn($q) => $q->whereIn('role_id', $role_ids)->where('action_id', 1))
                    ->orderBy('order', 'ASC')
                    ->with([
                        'subMenu' => function($q) use ($role_ids) {
                            $q->where('status', 1)
                            ->whereHas('permission', fn($qq) => $qq->whereIn('role_id', $role_ids)->where('action_id', 1))
                            ->orderBy('order', 'ASC')
                            ->with([
                                'subMenuSub' => function($qqq) use ($role_ids) {
                                    $qqq->where('status', 1)
                                        ->whereHas('permission', fn($qqqq) => $qqqq->whereIn('role_id', $role_ids)->where('action_id', 1))
                                        ->orderBy('order', 'ASC');
                                }
                            ]);
                        }
                    ]);
            }
        ])->orderBy('order', 'ASC')->get();

        return view('livewire.settings.layouts.sidebar');
    }
}
