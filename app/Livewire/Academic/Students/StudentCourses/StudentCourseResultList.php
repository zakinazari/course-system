<?php

namespace App\Livewire\Academic\Students\StudentCourses;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\CenterSettings\Branch;
use App\Models\Academic\Student;
use App\Models\Academic\CourseStudent;
use App\Models\Academic\Course;
use Auth;
use DB;
class StudentCourseResultList extends Component
{
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'student-course-list-addEditModal';
    public $table_name='student_course_list';
    public $selectedFields = [];
    public $pdfOrientation = 'landscape';
    public $branches=[];

    protected $listeners = ['modalClosed' => 'closeModal','globalDelete' => 'handleGlobalDelete'];

    public function closeModal(){
        $this->resetInputFields();
        $this->resetValidation();
        $this->dispatch('close-modal', id: $this->modalId);
         $this->dispatch('reset-select2');

    }

    public function openModal(){
        $this->resetInputFields();
        $this->resetValidation();
        $this->dispatch('open-modal', id: $this->modalId);
         $this->dispatch('reset-select2');
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

    public function mount($active_menu_id = null, $student_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->student =Student::findOrFail($student_id);

    }

     public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'student',
        ]);
    }

    public $search = [
            'name' => null,
            'status' => null,
        ];

    public function render()
    {
        $course_results = $this->student->courseResults()
        ->with(['course','course.book','course.teacher:id,name,last_name',
            'examScores' => function ($q) {
                $q->where('student_id', $this->student->id)
                ->with('examType');
            }
        ])
        ->paginate($this->perPage);

        return view('livewire.academic.students.student-courses.student-course-result-list',compact('course_results'));
    }

     protected function rules()
    {
        
        return [
           'target_course_id'=>'required',
        ];
    }
    // Localized messages
    protected function messages()
    {
        return [
            'target_course_id.required'   => __('label.course_id.required'),
        ];
    }
}
