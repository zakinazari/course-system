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
use App\Models\Assessment\StudentAttendance;
use Auth;
use DB;
class StudentCourseAttendanceList extends Component
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
    public $student_courses = [];
    public function mount($active_menu_id = null, $student_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->student =Student::findOrFail($student_id);
        $this->student_courses = $this->student->courses()
        ->with('book:id,name')
        ->orderBy('pivot_enrolled_at', 'desc')->get();

        // $this->search['from'] = now()->format('Y-m-d');
        // $this->search['to'] = now()->format('Y-m-d');
        
    }

     public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'student',
            'student_courses',
        ]);
    }

    public $search = [
            'course_id' => null,
            'from' => null,
            'to' => null,
        ];

    public function render()
    {
        $search = $this->search;
        $attendances = StudentAttendance::with([
            'course',
            'course.book:id,name',
            'course.time',
            'course.teacher:id,name,last_name'
        ])
        ->where('student_id',$this->student->id)
        ->where('course_id',$search['course_id'])
        // ->when(!empty($search['course_id']), function($q) use ($search){
        //         $q->where('course_id',$search['course_id']);
        //     })
        ->when(!empty($search['from']) && !empty($search['to']), function($q) use ($search){
                $q->where('attendance_date','>=',$search['from']);
                $q->where('attendance_date','<=',$search['to']);
            })
        ->orderBy('attendance_date','desc')
        ->paginate($this->perPage);
        return view('livewire.academic.students.student-courses.student-course-attendance-list',compact('attendances'));
    }

    protected function rules()
    {
        
        return [
       
        ];
    }

}
