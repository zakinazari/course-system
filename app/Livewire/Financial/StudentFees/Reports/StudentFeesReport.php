<?php

namespace App\Livewire\Financial\StudentFees\Reports;

use Livewire\Component;
use App\Models\Settings\Menu;
use App\Models\CenterSettings\Branch; 
use App\Models\Academic\Student;
use App\Models\Academic\Course;
use App\Models\CenterSettings\Section;
use App\Models\CenterSettings\Program;
use App\Models\CenterSettings\Book;
use App\Models\Financial\StudentCourseFee;
use App\Models\Financial\StudentCourseFeePayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Auth;
use Carbon\Carbon;
use DB;
class StudentFeesReport extends Component
{
    // -------start generals--------------------

    protected $paginationTheme = 'bootstrap';   
    public $active_menu_id;
    public $active_menu;
    public $pdfOrientation = 'landscape';
    public $selectedFields = [
        'no',
        'branch',
        'section',
        'program',
        'book',
        'course',
        'student_code',
        'name',
        'father_name',
        'total_amount',
        'remaining_amount',
        'amount',
        'payment_date',
    ];

     // Hook for real time error message
    public function updated($propertyName)
    {
        if (array_key_exists($propertyName, $this->rules())) {
            $this->validateOnly($propertyName);
        }
    }

    public function applySearch()
    {
        $this->fees = [];
        $this->dispatch('$refresh');
        $this->loadFeesPayment();
    }
    
    // ---------------------------------end generals-------------

    public $branches=[];
    public $sections=[];
    public $programs=[];
    public $books=[];
    public $courses=[];

    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->branches =  Branch::all();
        $this->sections = Section::all();
        $this->programs = Program::where('status','active')->get();
        $this->books = Book::where('status','active')->get();
        $this->search['from'] = now()->format('Y-m-d');
        $this->search['to'] = now()->format('Y-m-d');

        if(auth()->user()->branch_id){
                $index = array_search('branch', $this->selectedFields);
                if($index !== false){
                unset($this->selectedFields[$index]);
                $this->selectedFields = array_values($this->selectedFields);
            }
        }
    }

    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'sections',
            'branches',
            'programs',
            'books',
            'courses',
        ]);
    }

    public $search = [
            'student_code' => null,
            'branch' => null,
            'section_id' => null,
            'program_id' => null,
            'book_id' => null,
            'course_id' => null,
            'from' => null,
            'to' => null,
        ];

    public $fees=[];

    public function render()
    {
        return view('livewire.financial.student-fees.reports.student-fees-report');
    }

    public $total_payments=0;
    public function loadFeesPayment(){

        $search = $this->search;
        $this->fees = StudentCourseFeePayment::with(
            'studentCourseFee',
            'studentCourseFee.branch',
            'studentCourseFee.course',
            'studentCourseFee.course.program.section',
            'studentCourseFee.course.program',
            'studentCourseFee.course.book:id,name',
            'studentCourseFee.student',
        )
        ->when(!empty($search['student_code']), function ($query) use ($search) {
            $query->whereHas('studentCourseFee.student', fn($q) => $q->where('student_code', $search['student_code']));
        })
        
        ->when(!empty($search['branch_id']), function ($query) use ($search) {
            $query->whereHas('studentCourseFee', fn($q) => $q->where('branch_id', $search['branch_id']));
        })
        
        ->when(!empty($search['section_id']), function ($query) use ($search) {
            $query->whereHas('studentCourseFee.course.program', fn($q) => $q->where('section_id', $search['section_id']));
        })
        ->when(!empty($search['program_id']), function ($query) use ($search) {
            $query->whereHas('studentCourseFee.course', fn($q) => $q->where('program_id', $search['program_id']));
        })
        ->when(!empty($search['book_id']), function ($query) use ($search) {
            $query->whereHas('studentCourseFee.course', fn($q) => $q->where('book_id', $search['book_id']));
        })
        ->when(!empty($search['payment_type']), function ($query) use ($search) {
            $query->whereHas('studentCourseFee', fn($q) => $q->where('payment_type', $search['payment_type']));
        })
        ->whereHas('installment', fn($q) => $q->where('status','paid'))
        ->when(!empty($search['from']) && !empty($search['to']), function($q) use ($search){
            $q->whereBetween('payment_date', [$search['from'],$search['to']]);
        })
        ->orderBy('payment_date','desc')
        ->get();
        $this->total_payments=$this->fees->sum('amount');
    }

    public function print()
    {
        
        $this->dispatch('show-print-preview');
    }

    public function exportPdf()
    {
        
        $pdf = Pdf::loadView(
            'livewire.financial.student-fees.reports.course-fees-report-pdf',
            [
                'fees' => $this->fees,
                'selectedFields' => $this->selectedFields,
                'total_payments' => $this->total_payments,
                'search' =>$this->search,
            ]
        )->setPaper('a4',$this->pdfOrientation)
        ->setOption('defaultFont', 'dejavusans');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'student-course-fees-' . Carbon::now()->format('Y-m-d -H-i-A') . '.pdf'
        );
    }

    protected function rules()
    {
        $rules =  [
            'search.student_id' => 'required',

        ];
        return $rules;
    }

    public function updatedSearch()
    {
        $this->courses = Course::query()
        ->where('status','ongoing')
        ->where('program_id',$this->search['program_id'])
       ->get();
    }

    public function loadProgramBook($program_id)
    {
        $this->books = Book::where('status', 'active')
            ->where('program_id', $program_id)->get();
    }

    public function updatedSearchBookId()
    {
        $this->courses = Course::query()
        ->where('status','ongoing')
        ->where('book_id',$this->search['book_id'])
       ->get();
    }

    public function updatedSearchSectionId()
    {

        $this->programs = Program::where('section_id',$this->search['section_id'])
       ->get();
    }
}
