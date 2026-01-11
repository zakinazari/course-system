<?php

namespace App\Livewire\Front\Auth;

use Livewire\Component;
use App\Models\Website\WebMenu;
class Login extends Component
{
    public $active_menu_id;
    public $active_menu;
    public function mount($active_menu_id = null){

        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = WebMenu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------
    }
    public function render()
    {
        return view('livewire.front.auth.login');
    }
}
