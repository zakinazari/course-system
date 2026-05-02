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
class StudentAttendanceList extends Component
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
        $status;
    


        public $teacher_status = 'present'; // پیشفرض
        public $teacher_note = null;

        // UNIT SYSTEM
        public $total_days = 0;
        public $current_unit_status = 'finished';
        public $current_unit_number = 1;
        // برای radio در UI
        public $unit_status = 'finished';

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
        return view('livewire.assessment.attendance.student-attendance-list',[
             'students' => $this->students ?? collect(),
             'course' => $this->course,
        ]);
    }

    protected function loadCourseStudent()
    {
        $this->students = collect();
        $this->course = null;
        $course_id = $this->course_id;
        $date = $this->attendance_date ?? now()->toDateString();

        if (!$course_id) {
            $this->students = collect();
            return;
        }

        // =========================
        // 1. Load Course with Teacher
        // =========================
        $course = Course::with('teacher')
            ->where('id', $course_id)
            ->first();

        $this->course = $course;

        // =========================
        // 1. تعداد روزها (Day)
        // =========================
        $this->total_days = TeacherAttendance::where('course_id', $course_id)
            ->distinct('attendance_date')
            ->count('attendance_date');

        // =========================
        // 2. رکارد امروز
        // =========================
        $today = TeacherAttendance::where('course_id', $course_id)
            ->where('attendance_date', $date)
            ->first();

        // =========================
        // 3. رکارد قبلی (قبل از امروز)
        // =========================
        $previous = TeacherAttendance::where('course_id', $course_id)
            ->where('attendance_date', '<', $date)
            ->orderByDesc('attendance_date')
            ->first();

        if ($today) {

            // اگر امروز ثبت شده → همان را نشان بده
            $this->current_unit_number = $today->unit_number ?? 1;
            $this->unit_status = $today->lesson_status ?? 'finished';
            $this->teacher_note = $today->note ?? null;
            $this->teacher_status = $today->status ?? 'present';

        } else {

            // اگر امروز هنوز ثبت نشده → پیش‌بینی کن

            if (!$previous) {
                $this->current_unit_number = 1;
            } else {

                if ($previous->lesson_status === 'ongoing') {
                    $this->current_unit_number = $previous->unit_number;
                } else {
                    $this->current_unit_number = $previous->unit_number + 1;
                }
            }

            // پیشفرض‌ها برای روز جدید
            $this->unit_status = 'finished';
            $this->teacher_status = 'present';
            $this->teacher_note = null;
        }

        // =========================
        // 3. Load Students
        // =========================
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

            $this->attendances[$cs->student_id] = $record->status ?? 'present';
            $students[$i]->attendance_date = $record->attendance_date ?? null;

            // absent days
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

            // =========================
            // 1. STUDENT ATTENDANCE (UNCHANGED)
            // =========================
            $absent_counts = StudentAttendance::where('course_id', $course_id)
            ->where('status', 'absent')
            ->selectRaw('student_id, COUNT(*) as total')
            ->groupBy('student_id')
            ->pluck('total', 'student_id');
            foreach ($this->attendances as $student_id => $status) {

                StudentAttendance::updateOrCreate(
                    [
                        'student_id' => $student_id,
                        'course_id' => $course_id,
                        'attendance_date' => $date,
                    ],
                    [
                        'status' => $status,
                        'recorded_by' => Auth::id(),
                    ]
                );


                $absent_days = $absent_counts[$student_id] ?? 0;
                // اگر امروز absent ثبت شده → باید حساب شود
                if ($status === 'absent') {
                    $absent_days++;
                }

                $drop_days = $course->book?->drop_days;

                if ($drop_days && $absent_days >= $drop_days) {
                    DB::table('course_student')
                        ->where('student_id', $student_id)
                        ->where('course_id', $course_id)
                        ->update(['status' => 'dropped']);
                }

            }

            // =========================
            // 2. UNIT NUMBER LOGIC (NEW)
            // =========================
            $previous = TeacherAttendance::where('course_id', $course_id)
                ->where('attendance_date', '<', $date) 
                ->orderByDesc('attendance_date')
                ->first();

            if (!$previous) {
                $unit_number = 1;
            } else {

                if ($previous->lesson_status === 'ongoing') {
                    // ادامه همان یونت
                    $unit_number = $previous->unit_number;
                } else {
                    // یونت قبلی تمام شده → یونت جدید
                    $unit_number = $previous->unit_number + 1;
                }
            }

            // =========================
            // 3. TEACHER ATTENDANCE (UPDATED)
            // =========================
            TeacherAttendance::updateOrCreate(
                [
                    'course_id' => $course_id,
                    'teacher_id' => $course->teacher_id,
                    'attendance_date' => $date,
                ],
                [
                    'status' => $this->teacher_status ?? 'present',
                    'lesson_status' => $this->unit_status ?? 'finished',
                    'unit_number' => $unit_number,
                    'note' => $this->teacher_note,
                    'recorded_by' => Auth::id(),
                ]
            );

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

    public function exportExcel()
    {
         $this->students = collect();
        $data = $this->getReport();
        $students = $data['students'];
        $fields = $data['fields'];
        $course = Course::find($this->course_id);
        $date = $this->attendance_date;

        return Excel::download(
            new class($students, $fields,$course,$date) implements FromCollection, WithHeadings, WithEvents {

                protected $students;
                protected $fields;
                protected $course;
                protected $date;

                public function __construct($students, $fields,$course,$date)
                {
                    $this->students  = $students;
                    $this->fields = $fields;
                    $this->course = $course;
                    $this->date = $date;
                }

                public function collection()
                {
                    return $this->students->map(function ($sc, $index) {

                        $row = [];

                        foreach ($this->fields as $field) {
                            switch ($field) {
                                case 'no': $row[] = $index + 1; break;
                                case 'student_code': $row[] = $sc->student?->student_code; break;
                                case 'name': $row[] = $sc->student?->name; break;
                                case 'last_name': $row[] = $sc->student?->last_name; break;
                                case 'father_name': $row[] = $sc->student?->father_name; break;
                                case 'status': $row[] = $sc->att_status; break;
                                case 'absent_days': $row[] = $sc->absent_days; break;
                                case 'payment_status':
                
                                    if ($sc->payment_status === 'not_registered') {
                                        $row[] = 'Not Registered';
                                    } elseif ($sc->payment_status === 'paid') {
                                        $row[] = 'Fully Paid';
                                    } else {
                                       
                                        $row[] = __('label.due') . ': ' . number_format($sc->remaining_amount);
                                    }
                                    break;
                                default: $row[] = '';
                            }
                        }

                        return $row;
                    });
                }

                
                public function headings(): array
                {
                     $headers = [
                        'no'             => __('label.no'),
                        'student_code'   => __('label.student_code'),
                        'name'           => __('label.name'),
                        'last_name'      => __('label.last_name'),
                        'father_name'    => __('label.father_name'),
                        'status'         => __('label.status'),
                        'absent_days'         => __('label.absent_days'),
                        'payment_status'         => __('label.payment_status'),
                    ];

                    $translatedFields = [];

                    foreach ($this->fields as $field) {
                        $translatedFields[] = $headers[$field] ?? $field;
                    }

                    return [
                        [__('label.center_name')],
                        [__('label.student_attendance')],
                        [$this->course?->name],
                        [__('label.date').':'.$this->date],
                        [],
                        $translatedFields
                    ];
                }

                public function registerEvents(): array
                {
                    return [
                        AfterSheet::class => function(AfterSheet $event) {

                            $sheet = $event->sheet->getDelegate();

                            $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($this->fields));

                            /*
                            |--------------------------------------------------------------------------
                            | Merge Rows
                            |--------------------------------------------------------------------------
                            */
                            $sheet->mergeCells("A1:{$lastColumn}1");
                            $sheet->mergeCells("A2:{$lastColumn}2");
                            $sheet->mergeCells("A3:{$lastColumn}3");
                            $sheet->mergeCells("A4:{$lastColumn}4");

                            /*
                            |--------------------------------------------------------------------------
                            | Center Align All Header Lines
                            |--------------------------------------------------------------------------
                            */
                            $sheet->getStyle("A1:{$lastColumn}4")
                                ->getAlignment()
                                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                            /*
                            |--------------------------------------------------------------------------
                            | Font Styling
                            |--------------------------------------------------------------------------
                            */
                            $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(16);
                            $sheet->getStyle("A2:A4")->getFont()->setBold(true)->setSize(13);

                            /*
                            |--------------------------------------------------------------------------
                            | Add Logo (Left Side, Not Over Text)
                            |--------------------------------------------------------------------------
                            */
                            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                            $drawing->setName('Logo');
                            $drawing->setPath(public_path('logo.png'));
                            $drawing->setHeight(60);
                            $drawing->setCoordinates("{$lastColumn}1"); 
                            // $drawing->setCoordinates("{$lastColumn}1"); 
                            $drawing->setCoordinates('A1');
                            $drawing->setOffsetX(10);
                            $drawing->setWorksheet($sheet);

                            /*
                            |--------------------------------------------------------------------------
                            | Auto Size Columns
                            |--------------------------------------------------------------------------
                            */
                            foreach (range('A', $lastColumn) as $col) {
                                $sheet->getColumnDimension($col)->setAutoSize(true);
                            }
                        }
                    ];
                }

            },
            'student-attendance-' . now()->format('Y-m-d-H-i') . '.xlsx'
        );
    }

    protected function getReport()
    {
        $defaultFields = [
            'no',
            'student_code',
            'name', 
            'last_name',
            'father_name',
            'status',
            'absent_days',
            'payment_status',
        ];

        $fields = !empty($this->selectedFields)
            ? $this->selectedFields
            : $defaultFields;

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

                $students[$i]->att_status = $record->status ?? 'Not Taken';
                
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
}
