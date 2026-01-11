<?php

namespace App\Livewire\Assignments\Reviewer;

use Livewire\Component;
use App\Models\Submissions\Review;
class ReviewerAssignmentDeclinedReasons extends Component
{
    public $reviews=[];
    public function mount($submission_id = null,$round){
        $this->reviews = Review::with('reviewer:id,name')
       ->where('submission_id',$submission_id)
       ->where('round',$round)
       ->where('status','declined')->get();
    }
    public function render()
    {
        return view('livewire.assignments.reviewer.reviewer-assignment-declined-reasons');
    }
}
