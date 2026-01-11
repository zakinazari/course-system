<?php

namespace App\Livewire\Front;

use Livewire\Component;
use App\Models\Website\WebMenu;
use App\Models\Website\Post;
class SinglePost extends Component
{
    public $active_menu_id;
    public $active_menu;
    public $single_post;
    public function mount($slug = null,$post_id = null)
    {
        $this->active_menu_id = WebMenu::where('slug',$slug)->value('id');
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $this->active_menu_id);
        $this->active_menu = WebMenu::with(['parent', 'grandParent', 'subMenu'])->find($this->active_menu_id);
        // -------------start for activing menu in sidebar ----------------------
        $this->single_post = Post::findOrFail($post_id);
    }

    public function render()
    {
        return view('livewire.front.single-post');
    }
}
