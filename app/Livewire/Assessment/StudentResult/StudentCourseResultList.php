<?php

namespace App\Livewire\Assessment\StudentResult;

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
class StudentCourseResultList extends Component
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
        ];

    public $students=[];
    public $results=[];
    public $exam_types=[];
    public $exam_percentages = [];
    public function render()
    {
        return view('livewire.assessment.student-result.student-course-result-list');
    }

    protected function loadCourseStudent()
    {
        $course_id = $this->course_id;
        $this->students = collect();
        $this->exam_types = collect();
        $this->exam_percentages = [];

        if (!$course_id) return;

        // گرفتن کورس با کتاب و exam types
        $course = Course::with('book.examTypes')->find($course_id);
        if (!$course || !$course->book) return;

        // exam types مرتب شده بر اساس id (descending)
        $this->exam_types = $course->book->examTypes->sortByDesc('id');

        // درصد هر exam type
        foreach ($this->exam_types as $type) {
            $this->exam_percentages[$type->id] = $type->pivot->percentage ?? 0;
        }

        // گرفتن دانش‌آموزان کورس
        $students = CourseStudent::with('student')
            ->whereHas('courseResult')
            ->where('course_id', $course_id)
            ->get();

        $studentIds = $students->pluck('student_id');

        // گرفتن نمرات کل دانش‌آموزان برای این کورس (total + status)
        $studentTotals = StudentCourseResult::where('course_id', $course_id)
            ->whereIn('student_id', $studentIds)
            ->get()
            ->keyBy('student_id');

        // گرفتن نمرات تک تک امتحانات
        $examScores = StudentExamScore::where('course_id', $course_id)
            ->whereIn('student_id', $studentIds)
            ->get()
            ->groupBy('student_id')
            ->map(function ($scores) {
                return $scores->keyBy('exam_type_id');
            });

        $filteredStudents = collect();

        foreach ($students as $cs) {

            $cs->result = (object) [];

            // attach نمرات هر exam type به student
            foreach ($this->exam_types as $type) {
                $score = $examScores[$cs->student_id][$type->id]->score ?? 0;
                $cs->result->{$type->id} = $score;
            }

            // attach مجموع و وضعیت از StudentCourseResult
            $res = $studentTotals[$cs->student_id] ?? null;
            $cs->result->total = $res?->total ?? 0;
            $cs->result->status = $res?->status ?? null;

                    // اعمال فیلتر وضعیت
            if ($this->result_status === 'excellent' && $cs->result->total < 90) continue;
            if ($this->result_status === 'accepted' && ($cs->result?->status === 'failed' || $cs->result?->total >= 90)) continue;
            if ($this->result_status === 'weak' && $cs->result?->status === 'passed') continue;
            
            $filteredStudents->push($cs);
        }

        $this->students = $filteredStudents->values();

        // ------فیلدهای انتخاب شده 
        if (empty($this->selectedFields)) {

            $studentFields = ['no', 'student_code', 'name', 'father_name'];
            $endFields = ['total', 'status'];

            $examFields = collect($this->exam_types)
                ->map(fn($type) => 'exam_' . $type->id)
                ->toArray();

            $this->selectedFields = array_merge($studentFields, $examFields, $endFields);
        }
    }

    public function updatedResulStatus(){
        $this->loadCourseStudent;
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
            $this->results = [];
        }
    }

    public function exportPdf()
    {
        $data = $this->getReport();
        $students = $data['students'];
        $fields = $data['fields'];
        $exam_types = $data['exam_types'];
        $exam_percentages = $data['exam_percentages'];
        $course = Course::find($this->course_id);
        if($this->result_status==='excellent'){
            $status = __('label.excellent_student');
        }elseif($this->result_status==='accepted'){
            $status = __('label.accepted_student');
        }elseif($this->result_status==='week'){
            $status = __('label.week_student');
        }else{
            $status ='';
        }

        $pdf = Pdf::loadView(
            'livewire.assessment.mark-entry.student-course-result-entry-pdf',
            [
                'students' => $students,
                'fields' => $fields,
                'course' => $course,
                'status' => $status,
                'exam_types' => $exam_types,
                'exam_percentages' => $exam_percentages,
            ]
        )->setPaper('a4',$this->pdfOrientation)
        ->setOption('defaultFont', 'dejavusans');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'student-course-marks-' . Carbon::now()->format('Y-m-d -H-i-A') . '.pdf'
        );
    }

    public function exportExcel()
    {
        $data = $this->getReport();
        $students = $data['students'];
        $fields = $data['fields'];
        $course = Course::find($this->search['course_id']);
        if($this->result_status==='excellent'){
            $status = __('label.excellent_student');
        }elseif($this->result_status==='accepted'){
            $status = __('label.accepted_student');
        }elseif($this->result_status==='week'){
            $status = __('label.week_student');
        }else{
            $status ='';
        }
        

        return Excel::download(
            new class($students, $fields,$course,$status) implements FromCollection, WithHeadings, WithEvents {

                protected $students;
                protected $fields;
                protected $course;
                protected $status;
                public function __construct($students, $fields,$course,$status)
                {
                    $this->students  = $students;
                    $this->fields = $fields;
                    $this->course = $course;
                    $this->status = $status;
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
                                case 'cognitive': $row[] = $sc->result?->cognitive; break;
                                case 'attendance': $row[] = $sc->result?->attendance; break;
                                case 'midterm': $row[] = $sc->result?->midterm; break;
                                case 'final': $row[] = $sc->result?->final; break;
                                case 'total': $row[] = $sc->result?->total; break;
                                case 'status': $row[] = ucfirst($sc->result?->status); break;
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
                        'cognitive'         => __('label.cognitive_score'),
                        'attendance'         => __('label.attendance_score'),
                        'midterm'         => __('label.midterm_score'),
                        'final'         => __('label.final_score'),
                        'total'         => __('label.total_score'),
                        'status'         => __('label.status'),
                    ];

                    $translatedFields = [];

                    foreach ($this->fields as $field) {
                        $translatedFields[] = $headers[$field] ?? $field;
                    }

                    return [
                        [__('label.center_name')],
                        [__('label.student_course_marks')],
                        [$this->course?->name],
                        [$this->status],
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
            'student-course-marks-' . now()->format('Y-m-d-H-i') . '.xlsx'
        );
    }

   protected function getReport()
    {
        $this->loadCourseStudent();

        $studentFields = ['no', 'student_code', 'name', 'father_name'];
        $endFields = ['total', 'status'];

        // mapping: exam_id → name
        $examMapping = collect($this->exam_types ?? [])
            ->mapWithKeys(fn($type) => [
                'exam_' . $type->id => $type->name
            ])
            ->toArray();

        $selected = $this->selectedFields ?? [];

        $fields = [];

        //  فیلدهای ثابت اول
        foreach ($studentFields as $f) {
            if (in_array($f, $selected)) {
                $fields[] = $f;
            }
        }

        //  exam ها (با نام واقعی)
        foreach ($selected as $f) {
            if (isset($examMapping[$f])) {
                $fields[] = $examMapping[$f];
            }
        }

        //  آخر: total و status
        foreach ($endFields as $f) {
            if (in_array($f, $selected)) {
                $fields[] = $f;
            }
        }

        //  map نمرات به نام exam
        foreach ($this->students as $cs) {
            foreach ($this->exam_types ?? [] as $type) {
                $cs->result->{$type->name} = $cs->result->{$type->id} ?? 0;
            }
        }

        return [
            'students' => $this->students,
            'fields' => $fields,
            'exam_types' => $this->exam_types,
            'exam_percentages' => $this->exam_percentages,
            'examMapping' => $examMapping,
        ];
    }

}
