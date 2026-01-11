<?php

namespace App\Livewire\Front;

use Livewire\Component;
use App\Models\Website\WebMenu;
class FrontNavbar extends Component
{

    protected $listeners = ['setActiveMenuFromPage' => 'setActiveMenu'];
    
    public $active_menu_id;
    public $active_sub_menu_id;
    public $active_sub_menu_sub_id;

    public function setActiveMenu($menu_id)
    {
        $m = WebMenu::find($menu_id);
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

    public function render()
    {
        
        $menus = WebMenu::with([
        'subMenu' => function($q) {
            $q->where('status', 1)->orderBy('order', 'ASC');
        },
        'subMenu.subMenuSub' => function($q) {
            $q->where('status', 1)->orderBy('order', 'ASC');
        }
        ])
        ->where('status', 1)
        ->where('type_id', 1)
        ->orderBy('order', 'ASC')
        ->get();
        return view('livewire.front.front-navbar',compact('menus'));
    }
}
