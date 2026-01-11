<?php

namespace App\Livewire\Submissions;

use Livewire\Component;
use App\Models\Settings\Menu;
use App\Models\Submissions\Submission;
use App\Models\Submissions\SubmissionAuthor;
use App\Models\Submissions\Keyword;
use App\Models\Submissions\SubmissionKeyword;
use Auth;
use DB;

class MakeSubmission extends Component
{
    public $active_menu_id;
    public $active_menu;
    public $submission_id;
    public $title_fa;
    public $title_en;
    

    protected $listeners = ['stepValidated' => 'handleStepValidated', 'prevStep' => 'previousStep','refreshMakeSubmissionComponent' => 'refreshMe'];
    public $currentStep = 1;
    public $next_step_after_validation = null;

    public function refreshMe()
    {
        $this->dispatch('$refresh'); 
    }

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

    public function mount($active_menu_id = null,$submission_id = null)
    {
       
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------
     
        $this->submission_id = $submission_id;
    }


    public function render()
    {
        if($this->submission_id!=''){
            return view('livewire.submissions.make-submission-steps');
        }else{
            return view('livewire.submissions.make-submission');
        }
    }

    public function updated($propertyName)
    {
        if (array_key_exists($propertyName, $this->rules())) {
            $this->validateOnly($propertyName);
        }
    }
    protected function rules()
    {
        $rules = [
            'title_fa' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
        ];

        return $rules;
    }
    // Localized messages
    protected function messages()
    {
        return [
            'title_fa.required' => __('label.title.required'),
            'title_en.required' => __('label.title.required'),
            'title_fa.max' => __('label.title_fa.max'),
            'title_en.max' => __('label.title_en.max'),
        ];
    }
    // Create role
    public function store()
    {
        if (add(Auth::user()->role_ids, $this->active_menu_id)) {
            $this->validate();
            DB::beginTransaction();
            try {
                $submission = Submission::create([
                    'title_fa'     => $this->title_fa,
                    'title_en'     => $this->title_en,
                    'submitter_id' => Auth::user()->id,
                    'submitted_at' => now(),
                ]);
                SubmissionAuthor::create([
                    'submission_id' => $submission->id,
                    'user_id' => auth()->id(),
                    'given_name_fa' => auth()->user()->name_fa,
                    'given_name_en' => auth()->user()->name_en,
                    'family_name_fa' => auth()->user()->family_name_fa,
                    'family_name_en' => auth()->user()->family_name_en,
                    'affiliation_fa' => auth()->user()->affiliation_fa,
                    'affiliation_en' => auth()->user()->affiliation_en,
                    'email' => auth()->user()->email,
                    'phone_no' => auth()->user()->phone_no,
                    'country_id' => auth()->user()->country_id,
                    'province_id' => auth()->user()->province_id,
                    'city_fa' => auth()->user()->city_fa,
                    'city_en' => auth()->user()->city_en,
                    'department_fa' => auth()->user()->department_fa,
                    'department_en' => auth()->user()->department_en,
                    'education_degree_id' => auth()->user()->education_degree_id,
                    'academic_rank_id' => auth()->user()->academic_rank_id,
                    'preferred_research_area_fa' => auth()->user()->preferred_research_area_fa,
                    'preferred_research_area_en' => auth()->user()->preferred_research_area_en,
                    'type_id' => 1,
                    'order' => 1,
                ]);
                DB::commit();
                $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));

                return redirect()->route('make-submission', [
                    'menu_id' => $this->active_menu_id,
                    'submission_id' => $submission->id,
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                $this->dispatch('alert', type: 'error', message: __('label.store_error').' : '.$e->getMessage());
            }
        } else {
            
            $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }
    }

}
