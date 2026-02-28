<?php

namespace App\Livewire\Academic\Enrollments;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\Academic\Course;
use App\Models\Academic\Student;
use App\Models\Academic\PlacementTest;
use App\Models\Academic\CourseWaitingList;
use App\Models\Academic\CourseType;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Program;
use App\Models\CenterSettings\Book;
use App\Models\CenterSettings\Classroom;
use App\Models\CenterSettings\Shift;
use App\Models\Hr\Employee;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Validation\Rule;
class SpecialCourseList extends Component
{
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 12;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'special-course-list-addEditModal';
    public $table_name='courses';
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
    }
    
    // ---------------------------------end generals-------------

    public $branches=[];
    public $programs=[];
    public $books=[];
    public $shifts=[];
    public $course_types=[];
    public $teachers=[];
    public $classrooms=[];
    public $student_id;
    public $student;
    public $placement_test_id;
    public  $waiting_list_id;
    public function mount($active_menu_id = null,$student_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------
        $this->student_id = $student_id;
        $this->student = Student::find($student_id);
        $this->branches =  Branch::all();
        $this->programs = Program::where('status','active')->get();
        $this->shifts = Shift::all();
        
        $this->course_types = CourseType::all();

        $this->search['program_id'] = request()->query('program_id');
        $this->search['book_id']    = request()->query('book_id');
        $this->search['branch_id']  = request()->query('branch_id');
        $this->search['shift_id']  = request()->query('shift_id');

        $this->placement_test_id = rescue(
            fn () => request()->query('pt_id') ? decrypt(request()->query('pt_id')) : null,
            null
        );

        $this->waiting_list_id = rescue(
            fn () => request()->query('wl_id') ? decrypt(request()->query('wl_id')) : null,
            null
        );
        
        if ($this->search['program_id']) {
            $this->loadProgramBook($this->search['program_id']);
        }else{
            $this->loadProgramBook($this->program_id);
        }

        if ($this->search['branch_id']) {
            $this->loadClassroomAndTeacher($this->search['branch_id']);
        }else{
            $this->loadClassroomAndTeacher($this->branch_id);
        }
    }

      public $name,$course_id,$program_id,$book_id,$shift_id,

        $branch_id,
        $classroom_id,
        $teacher_ids=[];
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
            'student_id',
            'student',
            'placement_test_id',
            'waiting_list_id',
        ]);
    }
    public $search = [
            'name' => null,
            'program_id' => null,
            'book_id' => null,
            'branch_id' => null,
            'status' => null,
            'course_type_id' => null,
            'shift_id' => null,
            'teacher_id' => null,
        ];

    public function render()
    {
        
         $courses = Course::with('branch','courseType','program','book','classroom','shift')
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
            $query->whereHas('teachers',function($q){
                $q->where('teacher_id',$this->search['teacher_id']);
            });
        })
        ->orderBy('id','desc')
        ->paginate($this->perPage);

        return view('livewire.academic.enrollments.special-course-list',compact('courses'));
    }

    protected function rules()
    {
        
        return [
            'student_id' => [
                'required',
                Rule::unique('course_student')
                    ->where(function ($query) {
                        return $query->where('course_id', $this->course_id);
                    })
            ],
        ];
    }
    // Localized messages
    protected function messages()
    {
        return [

            'course_id.required'   => __('label.branch.required'),
            'student_id.required'   => __('label.student.required'),
            'student_id.unique'   => __('label.student.unique'),
        ];
    }

    public function setIds($course_id, $student_id)
    {
        $this->course_id = $course_id;
        $this->student_id = $student_id;
    }
    
    public function store()
    {
        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        $this->validate();
        DB::beginTransaction();

        try {

            //  قفل روی کورس برای جلوگیری از ثبت همزمان
            $course = Course::where('id', $this->course_id)
                ->lockForUpdate()
                ->firstOrFail();

          
            $is_full = DB::table('course_student')
                ->where('course_id', $course->id)
                ->selectRaw('COUNT(*) as total')
                ->havingRaw('total >= ?', [$course->max_capacity])
                ->exists();

            if ($course->max_capacity !== null && $is_full) {
                DB::rollBack();
                return $this->dispatch('alert', type: 'error', message: __('label.course_capacity_full'));
            }

            $course->students()->attach($this->student_id, [
                'status' => 'active',
                'enrolled_at' => now(),
            ]);
            
            if($this->placement_test_id){// placement student
                PlacementTest::find($this->placement_test_id)->update([
                    'status'=>'enrolled',
                ]);
            }   

            DB::commit();

            $this->closeModal();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.store_error') . ': ' . $e->getMessage());
        }
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

        $this->teachers = Employee::where('status','new')->get();
        
        $this->teacher_ids = [];
    }

    public function setPlacementTestInfo()
    {
        $this->resetValidation(); 
        $test = PlacementTest::find($this->placement_test_id);
        $this->student_id = $test->student_id;
        $this->branch_id = $test->branch_id;
        $this->program_id = $test->program_id;
        $this->book_id = $test->book_id;
        $this->shift_id = $test->shift_id;
    }

    public function storeToWaitingList()
    {
        
        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        $this->validate([
            'student_id'         => 'required|exists:students,id',
            'program_id'         => 'required|exists:programs,id',
            'book_id'            => 'required|exists:books,id',
            'shift_id'           => 'required|exists:shifts,id',
            'branch_id'          => 'required|exists:branches,id',

        ], [
            'student_id.required'   => __('label.student.required'),
            'branch_id.required'   => __('label.branch.required'),
            'program_id.required'   => __('label.program.required'),
            'book_id.required'   => __('label.book.required'),
            'shift_id.required'   => __('label.shift.required'),
        ]);
        DB::beginTransaction();
        try {
            
            $waiting = CourseWaitingList::firstOrCreate(
                [
                    'student_id' => $this->student_id,
                    'branch_id' =>  Auth::user()->branch_id ?: $this->branch_id,
                    'program_id' => $this->program_id,
                    'book_id' => $this->book_id,
                    'shift_id' => $this->shift_id,
                    'course_type_id' => 1,
                ],
                [
                    'status' => 'waiting',
                    'user_id' => Auth::Id(),
                ]
            );
            
           if (! $waiting->wasRecentlyCreated) {
            
                DB::rollBack(); 
                $this->dispatch('close-modal', id: 'add_to_waiting_id');
                return $this->dispatch('alert',
                    type: 'warning',
                    message: __('label.student_already_exists_in_waiting_list')
                );
            }

             // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'st_id' => $waiting->student?->id,
                'section' => __('label.waiting').' ('.$waiting->student?->name.' ID:'.$waiting->id.')',
                'type_id' => 2,
            ]);
            // ---end system log-------------
            DB::commit();
            $this->dispatch('close-modal', id: 'add_to_waiting_id');
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));
            return redirect()->route('waiting-list', [
                'menu_id' => 38,//waiting list menu id =38
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.store_error') . ': ' . $e->getMessage());
        }
    }
}
