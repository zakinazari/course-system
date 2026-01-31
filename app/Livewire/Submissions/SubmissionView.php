<?php

namespace App\Livewire\Submissions;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Models\Settings\Menu;
use App\Models\Submissions\Submission;
use App\Models\Submissions\SubmissionFile;
use App\Models\Submissions\EditorialDecision;
use App\Models\Submissions\Review;
use App\Models\Submissions\ReviewFile;
use App\Models\Issues\Issue;
use App\Models\User;

use Storage;
use DB;
class SubmissionView extends Component
{
    use WithFileUploads;
    public $active_menu_id;
    public $active_menu;
    public $submission_id;
    public $authors;
    public $comments_fa;
    public $comments_en;
    public $reviewer_id;
    public $current_round;
    public $reviews;
    public $file=[];
    public $issues = [];
    public $issue_id;
    public $current_page = 'details';
    public $users=[];
    public $search_reviewer;
    public $screeningModalId = 'submission-view-screeningModal';
    public $rejectModalId = 'submission-view-rejecteModal';
    public $acceptModalId = 'submission-view-acceptModal';
    public $revisionModalId = 'submission-view-revisionModal';
    public $sendForReviewModalId = 'submission-view-sendForReviewModal';
    public $assignToIssueModalId = 'submission-view-assignToIssueModal';
    
    protected $listeners = [
        'stepValidated' => 'handleStepValidated',
        'prevStep' => 'previousStep',
        'modalClosed' => 'closeModal',
        'globalDelete' => 'handleGlobalDelete',
        'refreshSubmissionViewComponent' => 'refreshMe'
    ];
   

    public function setPage($page,$round)
    {
        $this->current_page = $page;
        $this->current_round = $round;
        // session(['current_page' => $page]);
        // session(['current_round' => $round]);
    }


    public function refreshMe()
    {
        $this->dispatch('$refresh'); 
    }
    
    public function closeModal($modalId=null){
        $this->resetInputFields();
        // $this->resetValidation();
        $this->dispatch('close-modal', id: $modalId);

    }
    public function openModal($modalId=null){
        $this->resetInputFields();
        // $this->resetValidation();
        $this->dispatch('open-modal', id: $modalId);
    }
     // Hook for real time error message
    public function updated($propertyName)
    {
        if (array_key_exists($propertyName, $this->rules())) {
            $this->validateOnly($propertyName);
        }
    }

    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'screeningModalId',
            'acceptModalId',
            'rejectModalId',
            'revisionModalId',
            'sendForReviewModalId',
            'submission_id',
            'users',
            'authors',
            'current_page',
            'current_round',
            'reviews',
            'issues',
        ]);
    }

  
    public function mount($active_menu_id = null,$submission_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------
        $this->users = User::with('roles')
            ->whereHas('roles', function($query) {
                $query->where('role_name', 'Reviewer');
            })
            ->get();
        $this->current_page = 'details';
        $submission = Submission::findOrFail($submission_id);
        $this->current_round = $submission->round;
        $this->issues = Issue::where('status','unpublished')->get();
    }

    public function getRecommendationCountByRound($round)
    {
        return Review::where('submission_id', $this->submission_id)
                ->where('round', $round)
                ->where('status', 'completed')
                ->count();
    }

    public function getDeclinedCountByRound($round)
    {
        return Review::where('submission_id', $this->submission_id)
                    ->where('round', $round)
                    ->where('status', 'declined')
                    ->count();
    }


    public $currentStep = 1;
    public $next_step_after_validation = null;
    public function goToStep($step)
    {
    
        $this->dispatch('validate-step', step: $this->currentStep);
        $this->next_step_after_validation = $step;
    }

    public function handleStepValidated($is_valid)
    {
        if ($is_valid) {
            if ($this->next_step_after_validation) {
                $this->currentStep = $this->next_step_after_validation;
                $this->next_step_after_validation = null;
            } else {
                if ($this->currentStep < 4) {
                    $this->currentStep++;
                }
            }
        } else {
    
            if($this->currentStep==1){
                $this->dispatch('alert', type: 'error', message: __('label.details_step_required_message'));
            }elseif($this->currentStep==2){
                $this->dispatch('alert', type: 'error', message: __('label.file_upload_step_required_message'));
            }elseif($this->currentStep==3){
                $this->dispatch('alert', type: 'error', message: __('label.contributors_step_required_message'));

            }
            
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }
    
    public function render()
    {
        $submission = Submission::with('submitter','submitter.country','submitter.province','submitter.educationDegree','submitter.academicRank')
        ->findOrFail($this->submission_id);
        $this->authors = $submission?->submitter;
        $this->reviews = Review::where('submission_id',$this->submission_id)->exists();
        return view('livewire.submissions.submission-view',compact('submission'));
    }


    public function screeningSubmission()
    {

        try {
            $submission = Submission::findOrFail($this->submission_id);
            $submission->update([
                'status'=>'screening',
            ]);
            $this->closeModal($this->screeningModalId);
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));
            $this->submisison = $submission;
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.store_error').' : ' . $e->getMessage());
        }
    }
    
    public function acceptSubmission()
    {
    
         DB::beginTransaction();
        try {
            $submission = Submission::findOrFail($this->submission_id);
            $submission->update([
                'status'=>'accepted',
            ]);
            EditorialDecision::updateOrCreate(
                [
                    'submission_id'=> $this->submission_id,
                    'round'=> $submission->round,
                ],
                [
                    'submission_id'=> $this->submission_id,
                    'comments_fa' => $this->comments_fa,
                    'comments_en' => $this->comments_en,
                    'round'=> $submission->round,
                    'decision' => 'accept',
                    'editor_id' => auth()->id(),
                ],
            );

            DB::commit();
            $this->closeModal($this->acceptModalId);
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));
            $this->dispatch('refreshEditorDecisionComponent');
            $this->submisison = $submission;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.store_error').' : ' . $e->getMessage());
        }
    }

    public function rejectSubmission()
    {
       
         DB::beginTransaction();
        try {
            $submission = Submission::findOrFail($this->submission_id);
            $submission->update([
                'status'=>'rejected',
            ]);
            EditorialDecision::updateOrCreate(
                [
                    'submission_id'=> $this->submission_id,
                    'round'=> $submission->round,
                ],
                [
                    'submission_id'=> $this->submission_id,
                    'comments_fa' => $this->comments_fa,
                    'comments_en' => $this->comments_en,
                    'round'=> $submission->round,
                    'decision' => 'reject',
                    'editor_id' => auth()->id(),
                ],
            );

            DB::commit();
            $this->closeModal($this->rejectModalId);
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));
            $this->dispatch('refreshEditorDecisionComponent');
            $this->submisison = $submission;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.store_error').' : ' . $e->getMessage());
        }
    }

    public function revisionSubmission()
    {
       
         DB::beginTransaction();
        try {
            $submission = Submission::findOrFail($this->submission_id);
            $submission->update([
                'status'=>'revision_required',
            ]);
            EditorialDecision::updateOrCreate(
                [
                    'submission_id'=> $this->submission_id,
                    'round'=> $submission->round,
                ],
                [
                    'submission_id'=> $this->submission_id,
                    'comments_fa' => $this->comments_fa,
                    'comments_en' => $this->comments_en,
                    'round'=> $submission->round,
                    'decision' => 'revision_required',
                    'editor_id' => auth()->id(),
                ],
            );

            DB::commit();
            $this->closeModal($this->revisionModalId);
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));
            $this->dispatch('refreshEditorDecisionComponent');
            $this->submisison = $submission;
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.store_error').' : ' . $e->getMessage());
        }
    }


    public function updatedSearchReviewer()
    {
        $this->users = User::with('roles')
            ->whereHas('roles', function($query) {
                $query->where('role_name', 'Reviewer');
            })
            ->where('name', 'like', '%' . $this->search_reviewer . '%')
            ->get();

       
        $matchedUser = $this->users->firstWhere('name', $this->search_reviewer);
        if ($matchedUser) {
            $this->reviewer_id = $matchedUser->id;
        } else {
            $this->reviewer_id = null; 
        }
    }

    protected function rules()
    {
        return [
            'submission_id' => 'required',
            'reviewer_id' => 'required',
            'file' => 'required|array',
            'file.*' => 'file|max:5120|mimes:pdf,doc,docx',
        ];
    }
    protected function messages()
    {
        return [

            'reviewer_id.required' => __('label.reviewer.required'),
            'file.required' => __('label.file_required'),
            'file.*.file' => __('label.file_invalid'),
            'file.*.max' => __('label.file_max',['value'=>5]),
            'file.*.mimes' => __('label.file_mimes',['value'=>'pdf,doc,docx']),
        ];
    }



    public function sendForReviewSubmission()
    {
        $this->validate();
        DB::beginTransaction();
        try {
            $submission = Submission::findOrFail($this->submission_id);
            $submission->update([
                'status'=>'under_review',
            ]);
            $review = Review::updateOrCreate(
                [
                    'submission_id'=> $this->submission_id,
                    'reviewer_id' => $this->reviewer_id,
                    'round'=> $submission->round,
                ],
                [
                    'submission_id' => $this->submission_id,
                    'reviewer_id' => $this->reviewer_id,
                    'editor_message_fa' => $this->comments_en,
                    'editor_message_en' => $this->comments_en,
                    'round'=> $submission->round,
                    'assigned_at' => now(),
                ],
            );

            foreach ($this->file as $f) {
                $originalName = $f->getClientOriginalName();
                $mime = $f->getClientMimeType();
                $size = $f->getSize();
                $storedName = Str::random(40) . '.' . $f->getClientOriginalExtension();
                
                $path = $f->storeAs('files-for-review/' . $review->id, $storedName, 'local');
                ReviewFile::create([
                    'review_id' => $review->id,
                    'original_name' => $originalName,
                    'file_path' => $path,
                    'type' => 'for_review',
                    'size' => $size,
                    'mime' => $mime,
                ]);

                $f->delete();
            }

            $this->file = [];

            DB::commit();
            $this->closeModal($this->sendForReviewModalId);
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));
            $this->dispatch('refreshReviewComponent');
            $this->submisison = $submission;

            $this->search_reviewer = '';
            $this->reviewer_id = null;
            $this->users = User::with('roles')
                ->whereHas('roles', function($query) {
                    $query->where('role_name', 'Reviewer');
                })
                ->get();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.store_error').' : ' . $e->getMessage());
        }
    }



    public function assignToIssue()
    {
        $this->resetValidation(); 
        $submission = Submission::findOrFail($this->submission_id);
        $this->issue_id = $submission->issue_id;
        $this->dispatch('open-modal', id: $this->assignToIssueModalId);
    }

    public function assignToIssueSubmission()
    {
     
         DB::beginTransaction();
        try {

            $submission = Submission::findOrFail($this->submission_id);
            $submission->update([
                'issue_id'=>$this->issue_id? $this->issue_id:null,
            ]);
        
            DB::commit();
            $this->closeModal($this->assignToIssueModalId);
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));
            $this->dispatch('refreshReviewComponent');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.store_error').' : ' . $e->getMessage());
        }
    }
}
