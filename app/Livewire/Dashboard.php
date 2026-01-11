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
        if(Auth::user()->isReviewer()){

            $review = [
                'pending'     => Review::where('status','pending')->where('reviewer_id',Auth::Id())->count(),
                'accepted'     => Review::where('status','accepted')->where('reviewer_id',Auth::Id())->count(),
                'completed'     => Review::where('status','completed')->where('reviewer_id',Auth::Id())->count(),
                'declined'     => Review::where('status','declined')->where('reviewer_id',Auth::Id())->count(),
            ];

            $users = [
            'reviewer' => User::whereHas('roles', function($q){
                    $q->where('role_name', 'Reviewer');
                })->count(),
            'author' => User::whereHas('roles', function($q){
                    $q->where('role_name', 'Author');
                })->count(),
            'admin' => User::whereHas('roles', function($q){
                    $q->where('role_name', 'Admin');
                })->count(),
            'all' => User::whereHas('roles', function($q){
                    $q->where('role_name','!=','SuperAdmin');
                })->count(),
            ];
            return view('livewire.dashboard-reviewer',compact('review','users'));
        }
        elseif(Auth::user()->isAuthor()){
            $status = [
                'submitted'     => Submission::where('status','submitted')->where('submitter_id',Auth::Id())->count(),
                'screening' => Submission::where('status', 'screening')->where('submitter_id',Auth::Id())->count(),
                'under_review'  => Submission::where('status', 'under_review')->where('submitter_id',Auth::Id())->count(),
                'revision_required'     => Submission::where('status', 'revision_required')->where('submitter_id',Auth::Id())->count(),
                'accepted'      => Submission::where('status', 'accepted')->where('submitter_id',Auth::Id())->count(),
                'rejected'      => Submission::where('status', 'rejected')->where('submitter_id',Auth::Id())->count(),
                'published'     => Submission::where('status', 'published')->where('submitter_id',Auth::Id())->count(),
            ];

            $users = [
                'reviewer' => User::whereHas('roles', function($q){
                        $q->where('role_name', 'Reviewer');
                    })->count(),
                'author' => User::whereHas('roles', function($q){
                        $q->where('role_name', 'Author');
                    })->count(),
                'admin' => User::whereHas('roles', function($q){
                        $q->where('role_name', 'Admin');
                    })->count(),
                'all' => User::whereHas('roles', function($q){
                        $q->where('role_name','!=','SuperAdmin');
                    })->count(),
                ];

            return view('livewire.dashboard-author',compact('status','users'));
        }else{

            $status = [
                'submitted'     => Submission::where('status','submitted')->count(),
                'screening' => Submission::where('status', 'screening')->count(),
                'under_review'  => Submission::where('status', 'under_review')->count(),
                'revision_required'     => Submission::where('status', 'revision_required')->count(),
                'accepted'      => Submission::where('status', 'accepted')->count(),
                'rejected'      => Submission::where('status', 'rejected')->count(),
                'published'     => Submission::where('status', 'published')->count(),
            ];

            $users = [
                'reviewer' => User::whereHas('roles', function($q){
                        $q->where('role_name', 'Reviewer');
                    })->count(),
                'author' => User::whereHas('roles', function($q){
                        $q->where('role_name', 'Author');
                    })->count(),
                'admin' => User::whereHas('roles', function($q){
                        $q->where('role_name', 'Admin');
                    })->count(),
                'all' => User::whereHas('roles', function($q){
                        $q->where('role_name','!=','SuperAdmin');
                    })->count(),
                ];

            return view('livewire.dashboard',compact('status','users'));
        }
    }
}
