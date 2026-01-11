<?php

namespace App\Livewire\Assignments\Reviewer;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Settings\Menu;
use App\Models\Submissions\Review;
use Auth;
use DB;
class AllReviewerAssignments extends Component
{

    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $active_menu_id;
    public $active_menu;
    
    // Hook for real time error message
    public function updatingPerPage()
    {
        $this->resetPage();
    }
    public function applySearch()
    {
        $this->resetPage();
    }

    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------
    }

    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'search',
            'submissions',
        ]);
    }

     public $search = [
            'identity' => null,
            'status' => null,
        ];

    public function render()
    {
        $reviews = Review::with('submission','reviewer')
        ->whereHas('submission',function($query){
            $query->when(!empty($this->search['identity']), function ($q) {
                $searchTerm = $this->search['identity'];
                $q->where(function ($q) use ($searchTerm) {
                    $q->where('title_fa', 'like', "%{$searchTerm}%")
                    ->orWhere('title_en', 'like', "%{$searchTerm}%");
                });
            });
        })
        ->when(!empty($this->search['status']), function ($q) {
                 $status = $this->search['status'];
                 $q->where(function ($q) use ($status) {
                    $q->where('status', $status);
                });
            })
        ->where('reviewer_id',Auth::id())
        ->orderBy('created_at','desc')->paginate($this->perPage);
        return view('livewire.assignments.reviewer.all-reviewer-assignments',compact('reviews'));
    }

    public function viewAssignment($menu_id,$review_id)
    {
        return redirect()->route('reviewer-assignment-view', [
            'menu_id' => $this->active_menu_id,
            'review_id' => $review_id,
        ]);
    }
}
