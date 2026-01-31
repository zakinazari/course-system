<?php

namespace App\Livewire\Assignments\Reviewer;

use Livewire\Component;
use App\Models\Submissions\ReviewDecision;
use App\Models\Submissions\ReviewFile;
use App\Models\Submissions\Review;
use Storage;
class ReviewerAssignmentDecision extends Component
{
    public $submission_id;
    public $reviewer_recommendations=[];
    public $reviews=[];
    public $review_uploaded_files=[];

    public function mount($submission_id = null,$round)
    {
       $this->reviews = Review::with('decision','reviewedFiles','reviewer:id,name')
       ->where('submission_id',$submission_id)
       ->where('round',$round)
       ->where('status','completed')->get();
    }

    public function render()
    {
        return view('livewire.assignments.reviewer.reviewer-assignment-decision');
    }

    public function downloadFile($file_id)
    {
        $file = ReviewFile::findOrFail($file_id);

        $path = $file->file_path;

        if (!Storage::disk('local')->exists($path)) {
            $this->dispatch('alert', 
                type: 'error',
                message: 'File not found!'
            );
            return;
        }

        return Storage::disk('local')->download($path, $file->original_name);
    }
}
