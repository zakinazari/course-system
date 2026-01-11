<?php

namespace App\Livewire\Assignments\Reviewer;

use Livewire\Component;
use App\Models\Settings\Menu;
use App\Models\Submissions\Review;
use App\Models\Submissions\Submission;
use DB;
class ReviewerAssignmentDeclineReason extends Component
{
    public $review_id;
    public $review;
    public $submission;
    public $active_menu_id;
    public $active_menu;
    public $decline_reason_fa;
    public $decline_reason_en;
    public $table_name='reviews';
    public $modalId = 'reviewer-assingment-decline-reason-addEditModal';
    protected $listeners = ['modalClosed' => 'closeModal','globalDelete' => 'handleGlobalDelete'];
    public $editMode = false;
    public function closeModal(){
        // $this->resetValidation();
        $this->dispatch('close-modal', id: $this->modalId);

    }
    public function openModal(){
        // $this->resetValidation();
        $this->dispatch('open-modal', id: $this->modalId);
    }
     // Hook for real time error message
    public function updated($propertyName)
    {
        if (array_key_exists($propertyName, $this->rules())) {
            $this->validateOnly($propertyName);
        }
    }
    public function mount($review_id = null)
    {
        $this->review_id = $review_id;
        $this->review = Review::findOrFail($review_id);
        $this->submission = Submission::findOrFail($this->review->submission_id);
    }

    protected function rules()
    {
        return [
            'review_id' => 'required',
        ];
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
            $this->closeModal();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_updated'));
            $this->review = $review;
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.update_error').' : ' . $e->getMessage());
        }
    }

    public function editReason()
    {
        $review = Review::findOrFail($this->review_id);
        $this->decline_reason_fa = $review->decline_reason_fa;
        $this->decline_reason_en = $review->decline_reason_en;
        $this->editMode = true;
        $this->dispatch('open-modal', id: $this->modalId);
    }

    public function declineReviewUpdate()
    {
        try {
    
            $review = Review::findOrFail($this->review_id);
            $review->update([
                'decline_reason_fa' => $this->decline_reason_fa,
                'decline_reason_en' => $this->decline_reason_en,
                'status'=>'declined',
            ]);
            $this->closeModal();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_updated'));
            $this->review = $review;
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.update_error').' : ' . $e->getMessage());
        }
    }

    public function handleGlobalDelete($payload)
    {

        if (!isset($payload['table']) || $payload['table'] !== $this->table_name) {
            return;
        }

        $this->delete($payload['id']);
    }
    public function delete($id)
    {
         DB::beginTransaction();
        try {

            $review = Review::findOrFail($this->review_id);
            $review->update([
                'status'=>'pending',
                'decline_reason_fa'=> null,
                'decline_reason_en'=> null,
            ]);
            DB::commit();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
            $this->dispatch('refreshReviewerAssignmentViewComponent');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }

    public function render()
    {
        $review = Review::findOrFail($this->review_id);
        return view('livewire.assignments.reviewer.reviewer-assignment-decline-reason',compact('review'));
    }
}
