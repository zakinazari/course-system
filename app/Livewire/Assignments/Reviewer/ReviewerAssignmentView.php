<?php

namespace App\Livewire\Assignments\Reviewer;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Models\Settings\Menu;
use App\Models\Submissions\Review;
use App\Models\Submissions\ReviewDecision;
use App\Models\Submissions\ReviewFile;
use App\Models\Submissions\SubmissionAuthor;

use DB;
class ReviewerAssignmentView extends Component
{
     use WithFileUploads;
    public $review_id;
    public $submission_id;
    public $review;
    public $authors;
    public $active_menu_id;
    public $active_menu;
    public $decline_reason_fa;
    public $decline_reason_en;
    public $current_page ='details';
    public $current_round;

    public $recommendation_comments_fa;
    public $recommendation_comments_en;
    public $recommendation;
    public $file=[];


    public $modalId = 'reviewer-assingment-view-addEditModal';
    public $acceptModalId = 'reviewer-assingment-view-acceptModal';
    public $recommendationModalId = 'reviewer-assingment-view-recommendationModal';
    protected $listeners = ['modalClosed' => 'closeModal',
    'refreshReviewerAssignmentViewComponent' => 'refreshMe'];

    public function refreshMe()
    {
        $this->dispatch('$refresh'); 
    }
    public function closeModal($modalId=null){
        $this->resetValidation();
        $this->dispatch('close-modal', id: $modalId);

    }
    public function openModal($modalId=null){
        $this->resetValidation();
        $this->dispatch('open-modal', id: $modalId);
    }
     // Hook for real time error message
    public function updated($propertyName)
    {
       
        if (array_key_exists($propertyName, $this->rules())) {
            $this->validateOnly($propertyName);
        }
    }

    
    
    public function mount($active_menu_id = null ,$review_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->$active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------
        $this->review_id = $review_id;
        $this->review = Review::with('submission:id,title_en,title_fa,submitter_id','submission:submitter','submission.submitter.country','submission.submitter.province','submission.submitter.educationDegree','submission.submitter.academicRank')->findOrFail($review_id);
        $this->current_round = $this->review->round;
        $this->submission_id = $this->review->submission_id;
        $this->authors = $this->review?->submission?->submitter;
         $this->current_page ='details';
    }

    public function setPage($page,$round)
    {
        $this->current_page = $page;
        $this->current_round = $round;
    }

    public function acceptReview()
    {
        try {
            $review = Review::findOrFail($this->review_id);
            $review->update([
                'status'=>'accepted',
            ]);
            $this->closeModal($this->acceptModalId);
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));
            $this->review = $review;
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.store_error').' : ' . $e->getMessage());
        }
    }
    
    public function declineReview()
    {
        try {
            $review = Review::findOrFail($this->review_id);
            $review->update([
                'decline_reason_fa' => $this->decline_reason_fa,
                'decline_reason_en' => $this->decline_reason_en,
                'status'=>'declined',
            ]);
            $this->closeModal($this->modalId);
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));
            $this->review = $review;
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.store_error').' : ' . $e->getMessage());
        }
    }


    protected function rules()
    {
        return [
            'review_id' => 'required',
            'recommendation' => 'required',
            'file' => 'required',
            'file.*' => 'file|max:2048|mimes:pdf,doc,docx,xls,xlsx,csv',
        ];
    }

    protected function messages()
    {
        return [
            'recommendation.required' => __('label.recommendation.required'),
            
            'file.required' => __('label.file_required'),
            'file.*.file' => __('label.file_invalid'),
            'file.*.max' => __('label.file_max',['value'=>2]),
            'file.*.mimes' => __('label.file_mimes',['value'=>'pdf,doc,docx,xls,xlsx,csv']),
        ];
    }
    
    public function updatedFile()
    {
        foreach ($this->file as $i => $f) {
            if (
                !in_array($f->getClientOriginalExtension(), ['pdf','doc','docx','xls','xlsx','csv'])
            ) {
                unset($this->file[$i]);

                $this->addError(
                    "file.$i",
                    __('label.file_mimes',['value'=>'pdf,doc,docx,xls,xlsx,csv'])
                );
            }
        }

        $this->file = array_values($this->file);
    }
    
    public function reviewRecommendationStore()
    {
        $this->validate();
        DB::beginTransaction();
        try {

            ReviewDecision::updateOrCreate(
                [
                    'review_id'=> $this->review_id,
                ],
                [
                    'review_id'=> $this->review_id,
                    'comments_fa' => $this->recommendation_comments_fa,
                    'comments_en' => $this->recommendation_comments_en,
                    'recommendation' => $this->recommendation,
                ],
            );

            $review = Review::findOrFail($this->review_id);
            $review->update([
                'status'=>'completed',
            ]);

            foreach ($this->file as $f) {
                $originalName = $f->getClientOriginalName();
                $mime = $f->getClientMimeType();
                $size = $f->getSize();
                $storedName = Str::random(40) . '.' . $f->getClientOriginalExtension();
                
                $path = $f->storeAs('reviewer-files/' . $this->review_id, $storedName, 'local');
                ReviewFile::create([
                    'review_id' => $this->review_id,
                    'original_name' => $originalName,
                    'file_path' => $path,
                    'size' => $size,
                    'mime' => $mime,
                ]);

                 $f->delete();
            }

            $this->file = [];
            DB::commit();
            $this->closeModal($this->recommendationModalId);

            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));

        } catch (\Exception $e) {
             DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.store_error').' : ' . $e->getMessage());
        }
    }


    public function render()
    {
        $this->review = Review::findOrFail($this->review_id);
        return view('livewire.assignments.reviewer.reviewer-assignment-view');
    }
}
