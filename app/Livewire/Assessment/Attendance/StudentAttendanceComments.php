<?php

namespace App\Livewire\Assessment\Attendance;

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
use App\Models\Assessment\CourseUnit;
use App\Models\Assessment\CourseUnitDay;
use App\Models\Assessment\TeacherAttendance;
use App\Models\Financial\StudentCourseFee;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Auth;
use Carbon\Carbon;
use DB;
class StudentAttendanceComments extends Component
{

    // -------start generals--------------------
    use WithPagination;
    use WithFileUploads;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'student-attnedance-list-addEditModal';
    public $table_name='student_attendances';
    public  $selectedFields = [
            'no',
            'student_code',
            'name', 
            'last_name',
            'father_name',
            'phone_no',
            'father_whats_app',
            'status',
            'absent_days',
            'payment_status',
            'note',
        ];
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
        $this->attendance_date = now()->toDateString();
        $branch_id = Auth::user()->branch_id ?: $this->search['branch_id'];
        $this->loadClassroomAndTeacher($branch_id);
    }
    public
        $student_id,
        $course_id,
        $course,
        $attendance_date,
        $note,
        $status;
    


        public $teacher_status = 'present'; // پیشفرض
        public $teacher_note = null;
        
        // UNIT SYSTEM
        public $total_days = 0;
        public $current_unit_status = 'finished';
        public $current_unit_number = 1;
        // برای radio در UI
        public $unit_status = 'finished';
        public $unit_note = null;

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
            'status' => null,
            'course_type_id' => null,
            'shift_id' => null,
            'teacher_id' => null,
        ];
    public $students=[];
    public $attendances = [];
    public function render()
    {
       if (empty($this->course_id)) {
            $this->students = collect();
        }
        return view('livewire.assessment.attendance.student-attendance-comments',[
             'students' => $this->students ?? collect(),
             'course' => $this->course,
        ]);
    }

    public $has_attendance_records;

    public function loadCourseStudent()
    {
        $this->students = collect();
        $this->course = null;

        $course_id = $this->course_id;
        $date = $this->attendance_date ?? now()->toDateString();

        if (!$course_id) {
            $this->students = collect();
            return;
        }

        $course = Course::with('teacher')
            ->where('id', $course_id)
            ->first();

        $this->course = $course;

        $students = CourseStudent::with([
                'student',
                'course:id,book_id',
                'course.book:id,drop_days'
            ])
            ->where('course_id', $course_id)
            ->get();

        $fees = StudentCourseFee::where('course_id', $course_id)
        ->get()
        ->keyBy('student_id');

        foreach ($students as $i => $cs) {

        
            $record = StudentAttendance::where([
                'student_id' => $cs->student_id,
                'course_id' => $course_id,
                'attendance_date' => $date,
            ])->first();

            // فقط NOTE
            $this->attendances[$cs->student_id] = [
                'status' => $record->status ?? null,
                'note' => $record->note ?? null,
            ];

            $students[$i]->attendance_exists = (bool) $record;
            $students[$i]->attendance_date = $record->attendance_date ?? null;

            // absent days (بدون تغییر)
            $absent_days = StudentAttendance::where('student_id', $cs->student_id)
                ->where('course_id', $course_id)
                ->where('status', 'absent')
                ->count();

            $students[$i]->absent_days = $absent_days;

            // payment
            $fee = $fees[$cs->student_id] ?? null;

            $students[$i]->remaining_amount = $fee?->remaining_amount ?? 0;
            $students[$i]->payment_status = !$fee
                ? 'not_registered'
                : ($fee->remaining_amount == 0 ? 'paid' : 'due');
        }

           $this->has_attendance_records = $students->contains(function ($student) {
                return $student->attendance_exists;
            });

        $this->students = $students;
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
        $this->students = collect();
        $this->course_id = null;
        $this->courses = [];
        $this->courses = Course::with('branch','courseType','program','book','classroom','shift')
        ->where('status','ongoing')
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

    public function updatedCourseId($value)
    {
        if ($value) {
            $this->loadCourseStudent();
        } else {
            $this->students = collect();
            $this->attendances = [];
        }
    }

    public function updatedAttendanceDate()
    {
        $this->loadCourseStudent();
        $this->resetErrorBag('attendance_date');
    }

    public function saveAttendance()
    {
        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        $course_id = $this->course_id ?? null;
        if (!$course_id) return;

        $course = Course::find($course_id);
        if (!$course) return;

        $start = \Carbon\Carbon::parse($course->start_date)->toDateString();

        $this->validate([
            'attendance_date' => 'required|date|after_or_equal:' . $start,
        ]);

        DB::beginTransaction();

        try {

            $date = $this->attendance_date;

            foreach ($this->attendances as $student_id => $data) {

                $note = $data['note'] ?? null;

                $record = StudentAttendance::where([
                    'student_id' => $student_id,
                    'course_id' => $course_id,
                    'attendance_date' => $date,
                ])->first();

                if (!$record) {
                    continue;
                }

                $old_note = $record->note;

                $record->update([
                    'note' => $note,
                    'recorded_by' => Auth::id(),
                ]);

                // ثبت لاگ فقط در صورت تغییر کامنت
                if ($old_note != $note) {

                    SystemLog::create([
                        'user_id' => Auth::id(),
                        'st_id'   => $student_id,
                        'section' => 'Student Attendance Comment | Course ID: '.$course_id.
                                    ' | Date: '.$date.
                                    ' | Old: '.($old_note ?: '-').
                                    ' | New: '.($note ?: '-'),
                        'type_id' => 3,
                    ]);
                }
            }

        
            DB::commit();

            $this->students = collect();

            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));

        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('alert', type: 'error', message: __('label.store_error') . ': ' . $e->getMessage());
        }
    }



    public function exportPdf()
    {
        $this->students = collect();
        $data = $this->getReport();
        $students = $data['students'];
        $fields = $data['fields'];
        $course = Course::find($this->course_id);
        $date = $this->attendance_date;

        $pdf = Pdf::loadView(
            'livewire.assessment.attendance.student-attendance-list-pdf',
            [
                'students' => $students,
                'fields' => $fields,
                'course' => $course,
                'date' => \Carbon\Carbon::parse($date)->format('Y/m/d'),
            ]
        )->setPaper('a4',$this->pdfOrientation)
        ->setOption('defaultFont', 'dejavusans');
        
        return response()->streamDownload(
            fn () => print($pdf->output()),
            'student-attendance-' . Carbon::now()->format('Y-m-d -H-i-A') . '.pdf'
        );
    }

   
    protected function getReport()
    {
       
        $this->students = collect();
        $fields = $this->selectedFields;
    
        // if (auth()->user()->isDeveloper() || auth()->user()->isAdmin()) {
        //     if (!in_array('branch_id', $fields)) {
        //         $fields[] = 'branch_id';
        //     }
        // }

        $course_id = $this->course_id ?? null;
        $date = $this->attendance_date ?? now()->toDateString();

        if (!$course_id) {
            $students = collect();
        } else {
           
            $students = CourseStudent::with('student')
                ->where('course_id', $course_id)
                ->orderBy('id', 'asc') 
                ->get();
            $fees = StudentCourseFee::where('course_id', $course_id)
            ->get()
            ->keyBy('student_id');

            foreach ($students as $i=> $cs) {
                $record = StudentAttendance::where([
                    'student_id' => $cs->student_id,
                    'course_id' => $course_id,
                    'attendance_date' => $date,
                ])->first();

                $students[$i]->note = $record?->note;

                $students[$i]->att_status = $record?->status ?? 'Not Taken';
                
                // محاسبه تعداد روزهای غیبت از شروع کورس تا تاریخ انتخابی، بجز جمعه
                $absent_days = StudentAttendance::where('student_id', $cs->student_id)
                    ->where('course_id', $course_id)
                    ->where('status', 'absent')
                    ->count();

                $students[$i]->absent_days = $absent_days;
                // فیدهای مالی 

                $fee = $fees[$cs->student_id] ?? null;
                $students[$i]->remaining_amount = $fee?->remaining_amount ?? 0;
                $students[$i]->payment_status = !$fee 
                    ? 'not_registered' 
                    : ($fee->remaining_amount == 0 ? 'paid' : 'due');
            }
        }

        return [
            'students' => $students,
            'fields' => $fields,
        ];
        
    }

    public $report_students;
    public function print()
    {
       
        $this->dispatch('show-print-preview');

    }
}
