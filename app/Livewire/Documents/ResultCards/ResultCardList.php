<?php

namespace App\Livewire\Documents\ResultCards;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\Academic\Course;
use App\Models\Academic\CourseStudent;
use App\Models\Academic\CourseType;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Program;
use App\Models\CenterSettings\Book;
use App\Models\CenterSettings\Classroom;
use App\Models\CenterSettings\Shift;
use App\Models\Hr\Employee;
use App\Models\Assessment\StudentAttendance;
use App\Models\Assessment\StudentCourseResult;
use App\Models\Assessment\StudentExamScore;
use App\Jobs\SaveStudentResultsJob;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\Validator;
use Auth;
use Carbon\Carbon;
use DB;
class ResultCardList extends Component
{
    // -------start generals--------------------
    use WithPagination;
    use WithFileUploads;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'student-course-result-entry-addEditModal';
    public $table_name='student_course-result';
    public $selectedFields = [];
    public $pdfOrientation ='landscape';
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
        $this->loadCourseStudent();
        $this->dispatch('$refresh');
    }
    
    // ---------------------------------end generals-------------

    public $branches=[];
    public $programs=[];
    public $books=[];
    public $shifts=[];
    public $course_types=[];
    public $teachers=[];
    public $classrooms=[];
    public $courses=[];
    public $attendances = []; 
    public $branch;
    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------
        $this->branches =  Branch::all();
        $this->programs = Program::where('status','active')->get();
        $this->shifts = Shift::all();
        $this->course_types = CourseType::all();
        $branch_id = Auth::user()->branch_id ?: $this->search['branch_id'];
    
        $this->loadClassroomAndTeacher($branch_id);
        

    }

     public
        $student_id,
        $course_id,
        $attendance_date,
        $status,
        $result_status;
        
     public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'branches',
            'programs',
            'books',
            'shifts',
            'course_types',
            'teachers',
            'classrooms',
        ]);
    }
    public $search = [
            'name' => null,
            'program_id' => null,
            'book_id' => null,
            'branch_id' => null,
            'course_type_id' => null,
            'shift_id' => null,
            'teacher_id' => null,
            'student_code' => null,
        ];

    public $students=[];
    public $results=[];
    public $exam_types=[];
    public $exam_percentages = [];
    public $student_results = [];
    public $exam_period='all';
    public $student_code;

    public $selected_students = [];

    public function render()
    {
        
        return view('livewire.documents.result-cards.result-card-list');
    }

    protected function loadCourseStudent()
    {
        $course_id = $this->course_id;

        $this->students = collect();
        $this->student_results = [];

        $this->selected_students = [];

        if (!$course_id) return;

        // کورس
        $course = Course::with('book.examTypes', 'branch')->find($course_id);

        if (!$course || !$course->book) return;

        // دانش‌آموزان
        $students = CourseStudent::with('student')
            ->where('status','passed')
            ->whereHas('student',function($query){
                $query->when($this->student_code,function($q){

                    $search = $this->student_code;

                    $q->where(function ($qq) use ($search) {
                        $qq->where('name', 'like', "%{$search}%")
                        ->orWhere('student_code', 'like', "%{$search}%");
                    });
                });
            })
            ->whereHas('courseResult')
            ->where('course_id', $course_id)
            ->get();

        if ($students->isEmpty()) return;

        $studentIds = $students->pluck('student_id');

        // نتایج
        $studentTotals = StudentCourseResult::where('course_id', $course_id)
            ->whereIn('student_id', $studentIds)
            ->get()
            ->keyBy('student_id');

        // ساخت لیست نهایی
        $filteredStudents = $students->map(function ($cs) use ($studentTotals) {

            $res = $studentTotals[$cs->student_id] ?? null;

            $total = round($res?->total ?? 0);

           
            $grade = match (true) {
                $total >= 85 => 'A+',
                $total >= 80 => 'A',
                $total >= 75 => 'B+',
                $total >= 70 => 'B',
                $total >= 65 => 'C+',
                $total >= 60 => 'C',
                $total >= 55 => 'D+',
                $total >= 50 => 'D',
                $total >= 1  => 'F',
                default => '',
            };
            $this->student_results[$cs->student_id]['total'] = $total;
            $this->student_results[$cs->student_id]['grade'] = $grade;

            // attach برای sort
            $cs->total = $total;

            return $cs;
        })
        ->sortByDesc('total')
        ->values();

        $this->students = $filteredStudents;
        $this->selected_students = $filteredStudents->pluck('student_id')->toArray();
    }

    public function updatedResulStatus(){
        $this->loadCourseStudent;
    }

    public function updatedCourseId($value)
    {
        if ($value) {
            $this->loadCourseStudent();
        } else {
            $this->students = collect();
            $this->results = [];
            $this->selected_students = [];
        }
    }

    protected function rules()
    {
        $rules =  [
            'course_id' => 'required',

        ];
        return $rules;
    }

    public function updatedSearch()
    {   
        $this->dispatch('reset-select2');
        $this->course_id = null;
        $this->courses = [];
        $this->students = collect();
        $this->selected_students = [];
        $this->courses = Course::with('branch','courseType','program','book','classroom','shift')
        ->whereIn('status',['ongoing','archived'])
        ->when(!empty($this->search['name']), function ($query) {
            $query->where('name', 'like', '%' . $this->search['name'] . '%');
        })
        ->when(!empty($this->search['branch_id']), function ($query) {
            $query->where('branch_id',$this->search['branch_id']);
        })
        ->when(!empty($this->search['program_id']), function ($query) {
            $query->where('program_id',$this->search['program_id']);
        })

        ->when(!empty($this->search['book_id']), function ($query) {
            $query->where('book_id',$this->search['book_id']);
        })
        ->when(!empty($this->search['course_type_id']), function ($query) {
            $query->where('course_type_id',$this->search['course_type_id']);
        })
        ->when(!empty($this->search['shift_id']), function ($query) {
            $query->where('shift_id',$this->search['shift_id']);
        })
        ->when(!empty($this->search['teacher_id']), function ($query) {
            $query->where('teacher_id',$this->search['teacher_id']);
        })->get();
    }

    public function loadProgramBook($program_id)
    {
        $this->books = Book::where('status', 'active')
            ->where('program_id', $program_id)->get();
    }
    
    public function loadClassroomAndTeacher($branch_id)
    {
        $branch_id = Auth::user()->branch_id ?: $branch_id;
        $this->classrooms = Classroom::where('status', 'active')
            ->where('branch_id', $branch_id)->get();

        $this->teachers = Employee::whereHas('employeeRoles', function($query) {
            $query->where('name', 'Teacher');
        })->get();
    }

    public function print()
    {
        
        $this->dispatch('show-print-preview');
    }

    public function selectAll()
    {
        $this->selected_students = $this->students->pluck('student_id')->toArray();
    }

    public function clearSelection()
    {
        $this->selected_students = [];
    }
}
