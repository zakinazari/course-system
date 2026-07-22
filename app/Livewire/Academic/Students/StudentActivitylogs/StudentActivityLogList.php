<?php

namespace App\Livewire\Academic\Students\StudentActivitylogs;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\CenterSettings\Branch;
use App\Models\Academic\ActivityCategory;
use App\Models\Academic\StudentActivityLog;
use App\Models\Academic\Student;


use Auth;
use Carbon\Carbon;
use DB;

class StudentActivityLogList extends Component
{
     // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'student-activity-log-list-addEditModal';
    public $billModalId = '';
    public $table_name='student-activity-logs';
    public $selectedFields = [];
    public $pdfOrientation = 'landscape';
    public $branches=[];

    protected $listeners = ['modalClosed' => 'closeModal','globalDelete' => 'handleGlobalDelete'];

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

    // Hook for real time error message
    public function updated($propertyName)
    {
        if (array_key_exists($propertyName, $this->rules())) {
            $this->validateOnly($propertyName);
        }
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }
    public function applySearch()
    {
        $this->resetPage();
    }
    
    // ---------------------------------end generals-------------
    public $student;
    public $student_id;
    public $activity_categories =[];
    public function mount($active_menu_id = null, $student_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->student =Student::findOrFail($student_id);
        $this->student_id =$student_id;
      
        $this->activity_categories = ActivityCategory::all();
        $this->date = now()->format('Y-m-d');
    }

        public $activity_id;                   
        public $activity_date;  
        public $title;  
        public $description;  
        public $category_id;  
        public $date;  

    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'activity_categories',
            'student',
            'student_id',
            'date',
        ]);
    }

    public $search = [
            'category' => null,
        ];

    public function render()
    {
        $activities = StudentActivityLog::with('category')
        ->where('student_id',$this->student_id)
        ->when(!empty($this->search['category']), function ($query) {

            $query->where('category_id',$this->search['category']);
        })
        ->orderBy('id','desc')
        ->paginate($this->perPage);

        return view('livewire.academic.students.student-activitylogs.student-activity-log-list',compact('activities'));
    }

     protected function rules()
    {
        return [
            'description' => 'required',
            'student_id' => 'required',
            'category_id' => 'required',
        ];
    }
    // Localized messages
    protected function messages()
    {
        return [
            'description.required' => __('label.description.required'),
            'student.required'   => __('label.student.required'),
            'category_id.required'      => __('label.category.required'),
        ];
    }
    
    // Create role
    public function store()
    {
        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        $this->validate();

        try {

            $activity = StudentActivityLog::create([

                'description' => $this->description,
                'category_id' => $this->category_id,
                'student_id' => $this->student_id,
                'activity_date' => $this->date,
                'created_by' => Auth::user()->id,
            ]);

            // ---start system log-----------
            SystemLog::create([
                'st_id' => $this->student_id,
                'user_id' => Auth::user()->id,
                'section' => __('label.student_activity_logs').' ('.$activity->category?->name.' ID:'.$activity->id.')',
                'type_id' => 2,
            ]);
            // ---end system log-------------
            $this->closeModal();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));
            
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.store_error') . ': ' . $e->getMessage());
        }
    }


    public function edit($id)
    {
        $this->resetValidation(); 
        $this->activity_id = $id;    
        $activity = StudentActivityLog::findOrFail($id);
        $this->description = $activity->description;
        $this->date = $activity->activity_date->format('Y-m-d');
        $this->category_id = $activity->category_id;
        $this->editMode = true;
        $this->dispatch('open-modal', id: $this->modalId);
    }
    // Update role
    public function update()
    {
        if(!edit(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        $this->validate();
        try {

            $activity = StudentActivityLog::findOrFail($this->activity_id);
            $activity->update([
                'description' => $this->description,
                'category_id' => $this->category_id,
                'activity_date' => $this->date,
            ]);
            // ---start system log-----------
            SystemLog::create([
                 'st_id' => $this->student_id,
                'user_id' => Auth::user()->id,
                'section' => __('label.student_activity_logs').' ('.$activity->description.' ID:'.$activity->id.')',
                'type_id' => 3,
            ]);
            // ---end system log-------------
            $this->closeModal();
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
        if(!delete(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        try {
            $activity = StudentActivityLog::findOrFail($id);
             // ---start system log-----------
            SystemLog::create([
                'st_id' => $this->student_id,
                'user_id' => Auth::user()->id,
                'section' => __('label.student_activity_logs').' ('.$activity->description.' ID:'.$activity->id.')',
                'type_id' => 4,
            ]);
            // ---end system log-------------
            $activity->delete();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }
}
