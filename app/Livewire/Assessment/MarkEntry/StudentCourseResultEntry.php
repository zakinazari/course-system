<?php

namespace App\Livewire\Assessment\MarkEntry;

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
class StudentCourseResultEntry extends Component
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
        ];

    public $students=[];
    public $results=[];

    public function render()
    {
    
        return view('livewire.assessment.mark-entry.student-course-result-entry',[
            'students' => $this->students ?? collect()
        ]);
    }

   protected function loadCourseStudent()
    {
        $course_id = $this->search['course_id'] ?? null;
        $this->results = [];
        $this->students = collect();

        if (!$course_id) return;

        $students = CourseStudent::with('student')
            ->where('course_id', $course_id)
            ->get();

        $results = StudentCourseResult::where('course_id', $course_id)
            ->whereIn('student_id', $students->pluck('student_id'))
            ->get()
            ->keyBy('student_id');

        $filteredStudents = collect();
        foreach ($students as $cs) {
            $res = $results[$cs->student_id] ?? null;
            $total = $res?->total ?? 0;

            // status filter
            $status = $this->search['status'] ?? null;
            if ($status === 'excellent' && $total < 90) continue;
            if ($status === 'accepted' && ($total < 75 || $total >= 90)) continue;
            if ($status === 'week' && $total >= 75) continue;

            $this->results[$cs->student_id] = [
                'attendance' => $res?->attendance,
                'cognitive'  => $res?->cognitive,
                'midterm'    => $res?->midterm,
                'final'      => $res?->final,
                'total'      => $res?->total,
            ];

            $cs->result = (object) [
                'attendance' => $res?->attendance,
                'cognitive'  => $res?->cognitive,
                'midterm'    => $res?->midterm,
                'final'      => $res?->final,
                'total'      => $res?->total,
            ];

            $filteredStudents->push($cs);
        }

        $this->students = $filteredStudents;
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
            $query->whereHas('teachers',function($q){
                $q->where('teacher_id',$this->search['teacher_id']);
            });
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

        $this->teachers = Employee::where('status','new')->get();
    }


    public function updatedSearchCourseId()
    {
        $this->results = [];
        $this->students=collect();
    }

    public function saveMarks()
    {
        
        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        $course_id = $this->search['course_id'] ?? null;
        if (!$course_id) return;

        $course = Course::find($course_id);
        if (!$course) return;
          $data = ['results' => $this->results];


        $validator = Validator::make($data, [
            'results' => 'required|array',
            'results.*.cognitive'  => 'nullable|numeric|min:0|max:20',
            'results.*.attendance' => 'nullable|numeric|min:0|max:20',
            'results.*.midterm'    => 'nullable|numeric|min:0|max:30',
            'results.*.final'      => 'nullable|numeric|min:0|max:30',
            'results.*.total'      => 'nullable|numeric|min:0|max:100',
        ], [], [
            'results.*.cognitive'  => 'Cognitive',
            'results.*.attendance' => 'Attendance',
            'results.*.midterm'    => 'Midterm',
            'results.*.final'      => 'Final',
            'results.*.total'      => 'Total',
        ]);

        if ($validator->fails()) {
            
            $this->dispatch('alert', type: 'error', message: implode('<br>', $validator->errors()->all()));
            return;
        }

        try {

            // foreach ($this->results as $student_id => $data) {

            //     $attendance = isset($data['attendance']) ? floatval($data['attendance']) : 0;
            //     $cognitive = isset($data['cognitive']) ? floatval($data['cognitive']) : 0;
            //     $midterm   = isset($data['midterm']) ? floatval($data['midterm']) : 0;
            //     $final     = isset($data['final']) ? floatval($data['final']) : 0;
            //     $total = min(100, $attendance + $cognitive + $midterm + $final);

            //     StudentCourseResult::updateOrCreate(
            //         [
            //             'student_id' => $student_id,
            //             'course_id'  => $course_id,
            //         ],
            //         [
            //             'attendance' => $attendance,
            //             'cognitive'  => $cognitive,
            //             'midterm'    => $midterm,
            //             'final'      => $final,
            //             'total'      => $total,
            //             'user_id'      => Auth::Id(),
            //         ]
            //     );
            // }
             // Dispatch Job برای ذخیره در پس‌زمینه
            $user_id = Auth::user()->id;
            SaveStudentResultsJob::dispatch($course_id, $this->results,$user_id);
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));

        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.store_error') . ': ' . $e->getMessage());
        }
    }
    

    public function exportPdf()
    {
        $data = $this->getReport();
        $students = $data['students'];
        $fields = $data['fields'];
        $course = Course::find($this->search['course_id']);
        if($this->search['status']==='excellent'){
            $status = __('label.excellent_student');
        }elseif($this->search['status']==='accepted'){
            $status = __('label.accepted_student');
        }elseif($this->search['status']==='week'){
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
        if($this->search['status']==='excellent'){
            $status = __('label.excellent_student');
        }elseif($this->search['status']==='accepted'){
            $status = __('label.accepted_student');
        }elseif($this->search['status']==='week'){
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
        $defaultFields = [
            'no',
            'student_code',
            'name', 
            'father_name',
            'cognitive',
            'attendance',
            'midterm',
            'final',
            'total',
        ];

        $fields = !empty($this->selectedFields)
            ? $this->selectedFields
            : $defaultFields;

        // if (auth()->user()->isDeveloper() || auth()->user()->isAdmin()) {
        //     if (!in_array('branch_id', $fields)) {
        //         $fields[] = 'branch_id';
        //     }
        // }

        $this->loadCourseStudent();

        return [
            'students' => $this->students ?? collect(),
            'fields' => $fields,
        ];
    }
}
