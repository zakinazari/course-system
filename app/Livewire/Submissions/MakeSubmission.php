<?php

namespace App\Livewire\Submissions;

use Livewire\Component;
use App\Models\Settings\Menu;
use App\Models\Submissions\Submission;
use App\Models\Submissions\SubmissionAuthor;
use App\Models\Submissions\MainAxis;
use App\Models\Submissions\SubAxis;
use App\Models\Submissions\AcceptedAbstract;
use App\Models\Submissions\Keyword;
use App\Models\Submissions\SubmissionKeyword;
use Auth;
use DB;
use App;

class MakeSubmission extends Component
{
    public $active_menu_id;
    public $active_menu;
    public $submission_id;
    public $title_fa;
    public $title_en;
    public $main_axes = [];
    public $sub_axes = [];
    public $main_axis_id;
    public $sub_axis_id;
    
    public $accepted_abstracts = [];
    public $accepted_abstract_id;
    

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
        $this->main_axes = MainAxis::all();
        // $this->accepted_abstracts = AcceptedAbstract::limit(2)->get();
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
            'main_axis_id' => 'required',
            'sub_axis_id' => 'required',
            'accepted_abstract_id' => 'required',
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
            'main_axis_id.required' => __('label.main_axis.required'),
            'sub_axis_id.required' => __('label.sub_axis.required'),
            'accepted_abstract_id.required' => __('label.accepted_abstract_id.required'),
        ];
    }
    // Create role
    public function store()
    {
       
        if (add(Auth::user()->role_ids, $this->active_menu_id)) {
            if ($this->accepted_abstract_id) {
                $abs = AcceptedAbstract::findOrFail($this->accepted_abstract_id);

                $this->title_fa = app()->getLocale() == 'fa'
                    ? $abs->title_fa
                    : $abs->title_en;
            }

            $this->validate();
            DB::beginTransaction();
            try {

                $author = Auth::user();
                $submission = Submission::create([
                    'title_fa'     => $this->title_fa,
                    'title_en'     => $this->title_en,
                    'submitter_id' => $author->id,
                    'main_axis_id' => $this->main_axis_id,
                    'sub_axis_id' => $this->sub_axis_id,
                    'accepted_abstract_id' => $this->accepted_abstract_id,
                    'submitted_at' => now(),
                ]);
                SubmissionAuthor::create([
                    'submission_id' => $submission->id,
                    'user_id' => $author->id,
                    'given_name_fa' => $author->name_fa,
                    'given_name_en' => $author->name_en,
                    'family_name_fa' => $author->family_name_fa,
                    'family_name_en' => $author->family_name_en,
                    'affiliation_fa' => $author->affiliation_fa,
                    'affiliation_en' => $author->affiliation_en,
                    'email' => $author->email,
                    'phone_no' => $author->phone_no,
                    'country_id' => $author->country_id,
                    'province_id' => $author->province_id,
                    'city_fa' => $author->city_fa,
                    'city_en' => $author->city_en,
                    'department_fa' => $author->department_fa,
                    'department_en' => $author->department_en,
                    'education_degree_id' => $author->education_degree_id,
                    'academic_rank_id' => $author->academic_rank_id,
                    'preferred_research_area_fa' => $author->preferred_research_area_fa,
                    'preferred_research_area_en' => $author->preferred_research_area_en,
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

    public function loadSubAxes($main_axis_id)
    {
        $this->sub_axes = SubAxis::where('main_axis_id', $main_axis_id)->get();
    }

    public function setTitle($id)
    {
        $abs= AcceptedAbstract::findOrFail($id);
        $this->title_fa = (App::getlocale() == 'fa') ? $abs->title_fa: $abs->title_en;
    }

}
