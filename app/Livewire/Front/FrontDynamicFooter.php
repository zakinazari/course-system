<?php

namespace App\Livewire\Front;

use Livewire\Component;
use App\Models\Website\WebMenu;
class FrontDynamicFooter extends Component
{
    public function render()
    {
        $menus = WebMenu::with([
        'subMenu' => function($q) {
            $q->where('status', 1)->orderBy('order', 'ASC')->limit(8);
        },
        'subMenu.subMenuSub' => function($q) {
            $q->where('status', 1)->orderBy('order', 'ASC');
        }
        ])
        ->where('id',2)
        ->where('status', 1)
        ->where('type_id', 1)
        ->orderBy('order', 'ASC')
        ->get();

        return view('livewire.front.front-dynamic-footer',compact('menus'));
    }
}
