<?php

namespace App\Livewire\Assessment\ExamAttendance;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\CenterSettings\ExamType;
use App\Models\Academic\Course;
use App\Models\Academic\CourseStudent;
use App\Models\Academic\CourseType;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Program;
use App\Models\CenterSettings\Book;
use App\Models\CenterSettings\Classroom;
use App\Models\CenterSettings\Shift;
use App\Models\Financial\StudentCourseFee;
use App\Models\Hr\Employee;
use App\Models\Assessment\ExamAttendance;
use App\Models\Financial\ExamFine;
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
class ExamAttendanceList extends Component
{
    // -------start generals--------------------
    use WithPagination;
    use WithFileUploads;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'exam-attnedance-list-addEditModal';
    public $table_name='exam_attendances';
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
    public $exam_types=[]; 
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
        $this->exam_types = ExamType::all();
        $this->search['attendance_date'] = now()->toDateString();
        $branch_id = Auth::user()->branch_id ?: $this->search['branch_id'];
        $this->loadClassroomAndTeacher($branch_id);
    }
    public
        $student_id,
        $course_id,
        $attendance_date,
        $status;

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
            'exam_types',
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
            'course_id' => null,
            'attendance_date' =>null,
            'exam_type_id' =>null,
        ];
    public $students=[];
    public $attendances = [];

    public function render()
    {
        return view('livewire.assessment.exam-attendance.exam-attendance-list',
            [
                'students' => $this->students ?? collect()
            ]
        );
    }

    public $exam_type_message = null;
    public $course_message = null;
    public function updatedSearchExamTypeId(){
         $this->reset('exam_type_message');
    }
    protected function loadCourseStudent(){

        $course_id = $this->search['course_id'] ?? null;
        $exam_type_id = $this->search['exam_type_id'] ?? null;
        $date = $this->search['attendance_date'] ?? now()->toDateString();
         $this->exam_type_message = null; 
         $this->course_message = null; 
        if (!$course_id || !$exam_type_id) {
            $students = collect();

            if (!$course_id) {
                $this->course_message = __('label.course_id.required');
            } elseif (!$exam_type_id) {
                $this->exam_type_message = __('label.exam_type.required');
            }
            return;

        } else {
           
            $students = CourseStudent::with('student')
                ->where('status','!=','dropped')
                ->where('course_id', $course_id)
                ->get();

            $fees = StudentCourseFee::where('course_id', $course_id)
            ->get()
            ->keyBy('student_id');

            // کسیکه ثبت نام نکرده باشد از لیست حذف شود
            $students = CourseStudent::with('student')
                ->where('course_id', $course_id)
                ->get()
                ->filter(function ($cs) use ($fees) {
                    return isset($fees[$cs->student_id]); 
                })
                ->values();

            foreach ($students as $i=>$cs) {
                $record = ExamAttendance::where([
                    'student_id' => $cs->student_id,
                    'course_id' => $course_id,
                    'exam_type_id' => $exam_type_id,
                ])->first();

                $this->attendances[$cs->student_id] = $record->status ?? 'present';
                $students[$i]->attendance_date = $record->exam_date?? '';
            }
        }
        $this->students =$students;
    }

    protected function rules()
    {
        $rules =  [
            'search.course_id' => 'required',

        ];
        return $rules;
    }

    public function updatedSearch()
    {
        
        $this->students = collect();
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


    public function updatedSearchCourseId()
    {
        $this->students = collect();
        $this->resetErrorBag('search.attendance_date');
        $this->reset('course_message');
    }

    public function updatedSearchAttendanceDate()
    {
        $this->resetErrorBag('search.attendance_date');
    }
    public function saveAttendance()
    {
        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        $course_id = $this->search['course_id'] ?? null;
        $exam_type_id = $this->search['exam_type_id'] ?? null;

        if (!$course_id) return;

        $course = Course::find($course_id);
        if (!$course) return;
        DB::beginTransaction();
        try {

            foreach ($this->attendances as $student_id => $status) {
                ExamAttendance::updateOrCreate(
                    [
                        'student_id' => $student_id,
                        'course_id' => $course_id,
                        'exam_type_id' => $exam_type_id,
                    ],
                    [
                        'status' => $status,
                        'exam_date' => now(),
                        'recorded_by' => Auth::id(),
                    ]
                );
                
                if ($status === 'absent') {

                    ExamFine::firstOrCreate(
                        [
                            'student_id' => $student_id,
                            'course_id' => $course_id,
                            'exam_type_id' => $exam_type_id,
                        ],
                        [
                            'amount' => $course->book?->exam_fine_amount,
                            'status' => 'unpaid',
                            'reason' => null,
                            'exam_date' => now(),
                        ]
                    );

                }
            }

            
            $this->students = collect();
            DB::commit();
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
        $course = Course::find($this->search['course_id']);
        $exam_type = ExamType::find($this->search['exam_type_id']);
        $date = $this->search['attendance_date'];

        $pdf = Pdf::loadView(
            'livewire.assessment.exam-attendance.exam-attendance-list-pdf',
            [
                'students' => $students,
                'fields' => $fields,
                'course' => $course,
                'exam_type' => $exam_type,
                'date' => \Carbon\Carbon::parse($date)->format('Y/m/d'),
            ]
        )->setPaper('a4',$this->pdfOrientation)
        ->setOption('defaultFont', 'dejavusans');
        
        return response()->streamDownload(
            fn () => print($pdf->output()),
            'exam-attendance-' . Carbon::now()->format('Y-m-d -H-i-A') . '.pdf'
        );
    }

    public function exportExcel()
    {
         $this->students = collect();
        $data = $this->getReport();
        $students = $data['students'];
        $fields = $data['fields'];
        $course = Course::find($this->search['course_id']);
        $date = $this->search['attendance_date'];

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
        ];

        $fields = !empty($this->selectedFields)
            ? $this->selectedFields
            : $defaultFields;

        $course_id = $this->search['course_id'] ?? null;
        $exam_type_id = $this->search['exam_type_id'] ?? null;
        $date = $this->search['attendance_date'] ?? now()->toDateString();

        if (!$course_id) {
            $students = collect();
        } else {
           
            $students = CourseStudent::with('student')
                ->where('status','!=','dropped')
                ->where('course_id', $course_id)
                ->orderBy('id', 'asc') 
                ->get();
           

            foreach ($students as $i=> $cs) {
                $record = ExamAttendance::where([
                    'student_id' => $cs->student_id,
                    'course_id' => $course_id,
                    'exam_type_id' => $exam_type_id,
                ])->first();

                $students[$i]->att_status = $record->status ?? 'Not Taken';
                
            }
        }

        return [
            'students' => $students,
            'fields' => $fields,
        ];
        
    }
}
