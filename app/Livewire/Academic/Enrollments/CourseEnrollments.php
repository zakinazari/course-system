<?php

namespace App\Livewire\Academic\Enrollments;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\Academic\Course;
use App\Models\Academic\CourseStudent;
use App\Models\Academic\Student;
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
class CourseEnrollments extends Component
{
       // -------start generals--------------------
    use WithPagination;
    public $perPage = 12;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'course-enrollment-addEditModal';
    public $table_name='course_student';
    public $selectedFields = [];
    public $pdfOrientation ='landscape';
    public $course =[];
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

    public $course_id;
    public $student_id;
    public function mount($active_menu_id = null,$course_id,$student_id=null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->course_id = $course_id;
        $this->student_id = $student_id;
    }

    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'course',
            'course_id',
        ]);
    }
    public $search = [
            'name' => null,
            'status' => null,
        ];


    public function render()
    {   
        $this->course = Course::with('students','branch','courseType','program','book','classroom','shift')
        ->findOrFail($this->course_id);

        return view('livewire.academic.enrollments.course-enrollments',[
            'students' => $this->course->students()
            ->when(!empty($this->search['identity']), function ($query) {
                $search = $this->search['identity'];

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('student_code', 'like', "%{$search}%");
                });
            })
            ->when(!empty($this->search['status']), function ($query) {
                $status = $this->search['status'];
                $query->whereHas('courses', function ($q) use ($status) {
                    $q->where('course_student.status', 'like', "%{$status}%");
                });
            })
            ->paginate($this->perPage),
        ]);
    }

    protected function rules()
    {
        
        if ($this->pivot_id) {
            return [
                'status' => ['required'],
            ];
        }

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
            'status.required'   => __('label.status.required'),
        ];
    }
    
    // Create role
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
                ->where('status', 'active')
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

            DB::commit();

            $this->closeModal();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.store_error') . ': ' . $e->getMessage());
        }
    }

    public $pivot_id;
    public $status;

    public function edit($id)
    {
        $this->resetValidation(); 
        $this->pivot_id = $id;
        $pivot = DB::table('course_student')->find($id);
        $this->status = $pivot->status; 
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

         DB::table('course_student')
            ->where('id', $this->pivot_id)
            ->update([
                'status' => $this->status,
            ]);
        
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
            $courseStudent = CourseStudent::findOrFail($id);
            $student = Student::find($courseStudent->student_id);
            $course = Course::find($courseStudent->course_id);
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => 'Course ('.$course->name.') Student ('.$student->name.' ID:'.$student->student_id.')',
                'type_id' => 4,
            ]);
            $courseStudent->delete();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }

   public function exportPdf()
    {
        $defaultFields = [
            'no',
            'student_code',
            'name',
            'last_name',
            'father_name',
            'enrolled_at',
            'status',
        ];

         $fields = !empty($this->selectedFields)
            ? $this->selectedFields
            : $defaultFields;

        $course = Course::with('students')->findOrFail($this->course_id);

        $studentsQuery = $course->students()

            ->when(!empty($this->search['identity']), function ($query) {
                $search = $this->search['identity'];

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('student_code', 'like', "%{$search}%");
                });
            })

            ->when(!empty($this->search['status']), function ($query) {
                $status = $this->search['status'];
                $query->where('course_student.status', 'like', "%{$status}%");
            })

            ->orderBy('students.id', 'desc');

        $students = $studentsQuery->get();

        $pdf = Pdf::loadView(
            'livewire.academic.courses.course-student-list-pdf',
            [
                'students' => $students,
                'course' => $course,
                'fields' => $fields
            ]
        )->setPaper('a4',$this->pdfOrientation)
        ->setOption('defaultFont', 'dejavusans');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'course-student-list-' . Carbon::now()->format('Y-m-d -H-i-A') . '.pdf'
        );
    }

    public function loadProgramBook($program_id)
    {
        $this->books = Book::where('status', 'active')
            ->where('program_id', $program_id)->get();
    }
    
    public function loadClassroomAndTeacher($branch_id)
    {
        $this->classrooms = Classroom::where('status', 'active')
            ->where('branch_id', $branch_id)->get();

        $this->teachers = Employee::where('status','new')->get();
        
        $this->teacher_ids = [];
    }
}
