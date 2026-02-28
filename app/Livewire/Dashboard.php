<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Settings\Menu;
use App\Models\User;
use App\Models\Submissions\Submission;
use App\Models\Submissions\Review;
use Auth;
class Dashboard extends Component
{
    public $active_menu_id = 1;
    public $active_menu;
    
    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', 1);
        // $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ---------------------
    }
    public function render()
    {
        $status = [
            'submitted'     =>1,
            'screening' => 2,
            'under_review'  => 3,
            'revision_required'     =>4 ,
            'accepted'      => 5,
            'rejected'      => 6,
            'published'     => 8,
        ];

        $users = [
            'reviewer' =>1,
            'author' => 2,
            'admin' => 3,
            'all' => 7,
            ];

        return view('livewire.dashboard',compact('status','users'));
    }
}
