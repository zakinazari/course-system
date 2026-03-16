<?php

namespace App\Livewire\Financial\StudentFees\Reports;

use Livewire\Component;
use App\Models\Settings\Menu;
use App\Models\CenterSettings\Branch; 
use App\Models\Academic\Student;
use App\Models\Academic\Course;
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
class CourseFeesDiscountReport extends Component
{
     // -------start generals--------------------

    protected $paginationTheme = 'bootstrap';   
    public $active_menu_id;
    public $active_menu;
    public $pdfOrientation = 'landscape';
    public $selectedFields = [
        'no',
        'branch',
        'program',
        'course',
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
        $this->programs = Program::where('status','active')->get();
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
            'branches',
            'programs',
            'books',
            'courses',
        ]);
    }

    public $search = [
        'branch' => null,
        'program_id' => null,
        'book_id' => null,
        'course_id' => null,
        'from' => null,
        'to' => null,
    ];

    public $fees=[];

    public function render()
    {
        return view('livewire.financial.student-fees.reports.course-fees-discount-report');
    }

    public $total_payments=0;
    public function loadFeesPayment(){

        $this->fees= StudentCourseFeePayment::with('studentCourseFee','studentCourseFee.branch','studentCourseFee.course')
        ->when(!empty($this->search['branch_id']), function ($query) {
            $query->whereHas('studentCourseFee',function($q){
                $q->where('branch_id',$this->search['branch_id']);
            });
        })
        ->when(!empty($this->search['course_id']), function ($query) {
            $query->whereHas('studentCourseFee',function($q){
                $q->where('course_id',$this->search['course_id']);
            });
        })
        ->when(!empty($this->search['program_id']), function ($query) {
            $query->whereHas('studentCourseFee.course',function($q){
                $q->where('program_id',$this->search['program_id']);
            });
        })

        ->whereHas('installment',function($query){  
            $query->where('status','paid');
        })
        ->whereBetween('payment_date',[$this->search['from'],$this->search['to']])
        ->get();
        $this->total_payments = $this->fees->sum(function($feePayment) {
            return $feePayment->studentCourseFee->discount_amount ?? 0;
        });
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
}
