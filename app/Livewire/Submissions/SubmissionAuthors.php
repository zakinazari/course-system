<?php

namespace App\Livewire\Submissions;

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\Submissions\SubmissionAuthor;
use App\Models\Submissions\AuthorType;
use App\Models\Settings\Country;
use App\Models\Settings\EducationDegree;
use App\Models\Settings\AcademicRank;
use App\Models\Settings\Province;
class SubmissionAuthors extends Component
{
    public $is_show_details = false;
    public $editMode = false;
    public $modalId = 'submission-authors-addEditModal';
    public $table_name = 'submission_authors';
    public $submission_id;
    public $countries;
    public $author_types;

    public $author_id;
    public $country_id;
    public $author_type_id;
    public $authors = [];
    protected $listeners = ['modalClosed' => 'closeModal','globalDelete' => 'handleGlobalDelete','validate-step' => 'validateStep'];
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
            'search',
            'modalId',
            'submission_id',
            'authors',
            'author_types',
            'countries',
            'education_degrees',
            'academic_ranks',
            'provinces',
        ]);
    }

    public function updated($propertyName)
    {
        if (array_key_exists($propertyName, $this->rules())) {
            $this->validateOnly($propertyName);
        }
    }

    public 
    $given_name_fa,
    $given_name_en,
    $family_name_fa,
    $family_name_en,
    $email,
    $phone_no,
    $affiliation_fa,
    $affiliation_en,
    $city_fa,
    $city_en,
    $author_details;
    public $education_degrees;
    public $academic_ranks;
    public $provinces;
    public $province_id;
    public $education_degree_id;
    public $academic_rank_id;
    public $department_fa;
    public $department_en;
    public $preferred_research_area_fa;
    public $preferred_research_area_en;

    public function mount($submission_id=null){

        $this->submission_id = $submission_id;
        $this->countries = Country::all();
        $this->author_types = AuthorType::all();
        $this->education_degrees = EducationDegree::all();
        $this->academic_ranks = AcademicRank::all();
        $this->provinces = Province::all();
        $this->loadAuthors();
    }

    public function render()
    {
        return view('livewire.submissions.submission-authors');
    }

    public function showDetails($id)
    {
        $this->author_details = SubmissionAuthor::with('educationDegree','academicRank','country','province','type')->findOrFail($id);
        $this->dispatch('open-modal', id: 'modalShowDetails');
        $this->is_show_details = true;
    }
    
    public function loadAuthors()
    {
        $this->authors = SubmissionAuthor::with('country','Type')->where('submission_id', $this->submission_id)
                                ->orderBy('type_id','asc')
                                ->get();
    }

    protected function rules()
    {
        return [
            'given_name_fa' => 'required|string|max:255',
            'family_name_fa' => 'nullable|string|max:255',
            'given_name_en' => 'required|string|max:255',
            'family_name_en' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_no' => 'nullable|string|max:13|regex:/^\d*$/',
            'affiliation_en' => 'nullable|string|max:255',
            'affiliation_fa' => 'nullable|string|max:255',
            'country_id' => 'required',
            'author_type_id' => 'required',
        ];
    }

    public function store(){
        $this->validate();
         try {
                
            SubmissionAuthor::create([
                'submission_id' => $this->submission_id,
                'given_name_fa' => $this->given_name_fa,
                'given_name_en' => $this->given_name_en,
                'family_name_fa' => $this->family_name_fa,
                'family_name_en' => $this->family_name_en,
                'education_degree_id' => $this->education_degree_id,
                'academic_rank_id' => $this->academic_rank_id,
                'department_fa' => $this->department_fa,
                'department_en' => $this->department_en,
                'preferred_research_area_fa' => $this->preferred_research_area_fa,
                'preferred_research_area_en' => $this->preferred_research_area_en,
                'email' => $this->email,
                'phone_no' => $this->phone_no,
                'country_id' => $this->country_id,
                'province_id' => $this->province_id,
                'type_id' => $this->author_type_id,
                'affiliation_fa' => $this->affiliation_fa,
                'affiliation_en' => $this->affiliation_en,
                'city_fa' => $this->city_fa,
                'city_en' => $this->city_en,
            ]);
            $this->loadAuthors();
            $this->closeModal();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.store_error') . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->resetValidation(); 
        $this->author_id = $id;    
        $author = SubmissionAuthor::find($id);
        $this->given_name_fa = $author->given_name_fa;
        $this->family_name_fa = $author->family_name_fa;
        $this->given_name_en = $author->given_name_en;
        $this->family_name_en = $author->family_name_en;
        $this->education_degree_id = $author->education_degree_id;
        $this->academic_rank_id = $author->academic_rank_id;
        $this->department_fa = $author->department_fa;
        $this->department_en = $author->department_en;
        $this->preferred_research_area_fa = $author->preferred_research_area_fa;
        $this->preferred_research_area_en = $author->preferred_research_area_en;
        $this->email = $author->email;
        $this->phone_no = $author->phone_no;
        $this->affiliation_fa = $author->affiliation_fa;
        $this->affiliation_en = $author->affiliation_en;
        $this->country_id = $author->country_id;
        $this->province_id = $author->province_id;
        $this->author_type_id = $author->type_id;

        $this->city_fa = $author->city_fa;
        $this->city_en = $author->city_en;
        $this->editMode = true;
        $this->dispatch('open-modal', id: $this->modalId);
    }

    public function update()
    {
        $this->validate();
        try {
            $author = SubmissionAuthor::findOrFail($this->author_id)->update([
                'submission_id' => $this->submission_id,
                'given_name_fa' => $this->given_name_fa,
                'given_name_en' => $this->given_name_en,
                'family_name_fa' => $this->family_name_fa,
                'family_name_en' => $this->family_name_en,
                'education_degree_id' => $this->education_degree_id,
                'academic_rank_id' => $this->academic_rank_id,
                'department_fa' => $this->department_fa,
                'department_en' => $this->department_en,
                'preferred_research_area_fa' => $this->preferred_research_area_fa,
                'preferred_research_area_en' => $this->preferred_research_area_en,
                'email' => $this->email,
                'phone_no' => $this->phone_no,
                'country_id' => $this->country_id,
                'type_id' => $this->author_type_id,
                'affiliation_fa' => $this->affiliation_fa,
                'affiliation_en' => $this->affiliation_en,
                'province_id' => $this->province_id,
                'city_fa' => $this->city_fa,
                'city_en' => $this->city_en,
            ]);

            $this->closeModal();
             $this->loadAuthors();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_updated'));
        } catch (\Exception $e) {
        
            $this->dispatch('alert', type: 'error', message: __('label.update_error').' : '. $e->getMessage());
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

        try {
            $author = SubmissionAuthor::findOrFail($id);
            if (!$author) {
                $this->dispatch('alert', type: 'error', message: __('label.not_found'));
                return;
            }
            $author->delete();
            $this->loadAuthors();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }

    }

    public function validateStep($step)
    {
        if ($step != 3) return;
        $authors = SubmissionAuthor::where('submission_id', $this->submission_id)->count();
        if ($authors > 0) {
            $this->dispatch('stepValidated', is_valid: true);
        } else {
            $this->dispatch('stepValidated', is_valid: false);
        }
    }

}
