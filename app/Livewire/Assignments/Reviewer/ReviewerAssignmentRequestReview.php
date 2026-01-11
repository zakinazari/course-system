<?php

namespace App\Livewire\Assignments\Reviewer;

use Livewire\Component;
use App\Models\Submissions\Review;
use App\Models\Submissions\SubmissionFile;
use Storage;
class ReviewerAssignmentRequestReview extends Component
{
    public $review_id;
    
    public function mount($review_id = null)
    {
        $this->review_id = $review_id;
    }

    public function render()
    {
        $review = Review::with('submission')->findOrFail($this->review_id);
        $uploaded_files = SubmissionFile::where('submission_id', $review->submission_id)
                                ->where('round',$review->round)
                                ->latest()
                                ->get();
        return view('livewire.assignments.reviewer.reviewer-assignment-request-review',compact('review','uploaded_files'));
    }

    public function downloadFile($file_id)
    {
        $file = SubmissionFile::findOrFail($file_id);

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
