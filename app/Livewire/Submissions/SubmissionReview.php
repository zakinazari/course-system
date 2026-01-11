<?php

namespace App\Livewire\Submissions;

use Livewire\Component;
use Auth;
use DB;
use App\Models\User;
use App\Models\Submissions\Review;
use App\Models\Submissions\Submission;
use Illuminate\Validation\Rule;
class SubmissionReview extends Component
{
    public $editMode = false;
    public $modalId = 'submission-review-addEditModal';
    public $table_name='reviews';
    public $submission_id;
    public $submission;
    public $users=[];
    public $reviewers=[];
    public $reviewer_id;
    public $round;
    public $editor_message_fa;
    public $editor_message_en;
    
    protected $listeners = ['modalClosed' => 'closeModal','globalDelete' => 'handleGlobalDelete','refreshReviewComponent'=>'refreshMe'];
    public function refreshMe()
    {
        $this->dispatch('$refresh'); 
        $this->loadReviewers();
    }
    public function closeModal(){
        $this->resetInputFields();
        $this->resetValidation();
        $this->dispatch('close-modal', id: $this->modalId);

    }
    public function openModal(){
        $this->resetInputFields();
        $this->resetValidation();
        $this->dispatch('open-modal', id: $this->modalId);
    }
    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'modalId',
            'table_name',
            'search',
            'submission_id',
            'submission',
            'reviewers',
            'users',
            'round',
        ]);
    }

    public function updated($propertyName)
    {
        if (array_key_exists($propertyName, $this->rules())) {
            $this->validateOnly($propertyName);
        }
    }

    public function mount($active_menu_id = null,$submission_id = null,$round)
    {
       
        $this->submission_id = $submission_id;
        $this->users = User::all();
        $this->submission = Submission::findOrFail($this->submission_id);
        $this->round = $round;
        $this->loadReviewers();
    }

    protected function rules()
    {
        return [
            'reviewer_id' => [
                'required',
                Rule::unique('reviews')
                    ->where(fn($q) => $q->where('submission_id', $this->submission_id))
                    ->ignore($this->reviewer_id), 
            ],
        ];
    }

    protected function messages()
    {
        return [
            'reviewer_id.required' => __('label.reviewer.required'),
        ];
    }

    public function store(){
        $this->validate();
         try {
            
            $submission = Submission::findOrFail($submission_id);
            Review::create([
                'submission_id' => $this->submission_id,
                'reviewer_id' => $this->reviewer_id,
                'editor_message_fa' => $this->editor_message_fa,
                'editor_message_en' => $this->editor_message_en,
                'editor_message_en' => $submission->round,
                'assigned_at' => now(),
            ]);
            $this->loadReviewers();
            $this->closeModal();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.store_error') . $e->getMessage());
        }
    }

    public function loadReviewers()
    {
        $this->reviewers = Review::with('reviewer')->where('submission_id', $this->submission_id)
                                ->where('round',$this->round)
                                ->latest()
                                ->get();
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
        try {
            $review = Review::findOrFail($id);

            if (!$review) {
                $this->dispatch('alert', type: 'error', message: __('label.not_found'));
                return;
            }
            $review->delete();

            $has_review = Review::where('submission_id',$this->submission_id)->exists();
            if(!$has_review){
                $submission = Submission::findOrFail($this->submission_id);
                $submission->update([
                    'status'=>'screening',
                ]);
            }
        
            $this->loadReviewers();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
            $this->dispatch('refreshSubmissionViewComponent');
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.submissions.submission-review');
    }
}
