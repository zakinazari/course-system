<?php

namespace App\Livewire\Financial\FinancialReports;

use Livewire\Component;
use App\Models\Settings\Menu;
use App\Models\CenterSettings\Branch; 
use App\Models\CenterSettings\Section; 
use App\Models\Financial\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;


// incomes---------------
use App\Models\Financial\StudentCourseFeePayment;
use App\Models\Financial\StudentBookFee;
use App\Models\Financial\MakeupFee;
use App\Models\Financial\StudentOtherFee;
use App\Models\Financial\ExamFine;

use Auth;
use Carbon\Carbon;
use DB;
use App\Models\User;
use App\Enums\TransactionCategory;

class DailyIncomeReport extends Component
{
    
    // -------start generals--------------------
    protected $paginationTheme = 'bootstrap';   
    public $active_menu_id;
    public $active_menu;
    public $pdfOrientation = 'landscape';
    public $selectedFields = [
        'no',
        'category',
        'amount',
        'date',
        'section',
        'user',
        'branch',
        'amount',
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
        $this->loadDailyIncome();
    }
    
    // ---------------------------------end generals-------------

    public $branches=[];
    public $sections=[];
    public $users=[];
    public $income_categories=[];

    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->branches =  Branch::all();

        $this->sections =  Section::all();

        $this->income_categories = TransactionCategory::incomeCategories();

        $this->search['from'] = now()->format('Y-m-d');
        $this->search['to'] = now()->format('Y-m-d');
        $this->users = User::where('is_active', true)
        ->when(auth()->user()->branch_id, function ($query) {
            $query->where('branch_id', auth()->user()->branch_id);
        })
        ->get();

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
            'sections',
            'users',
            'income_categories',
        ]);
    }

    public $search = [
            'branch_id' => null,
            'category' => null,
            'section_id' => null,
            'user_id' => null,

            'from' => null,
            'to' => null,
        ];

   

    public function render()
    {
        return view('livewire.financial.financial-reports.daily-income-report');
    }

    public $incomes=[];
    public $total_income=0;
    public $financial_records = [];
    public $records_view = [];
    public function loadDailyIncome()
    {
        $search = $this->search;

        $from = Carbon::parse($this->search['from'])->startOfDay();
        $to = Carbon::parse($this->search['to'])->endOfDay();

        $branch_id = $this->search['branch_id'] ?: Auth::user()->branch_id;

        $section_id = $this->search['section_id'];

        $user_id = $this->search['user_id'];

        $this->financial_records = [];
        $this->records_view = $this->search['category'];
        $this->selected_category_key = $this->search['category'];

        if (empty($this->selected_category_key)) {

            $this->dispatch('alert', type: 'error', message: 'Category is required');
            return;
        }

    
        switch ($this->selected_category_key) {

            // ================= INCOME =================

            case TransactionCategory::COURSE_FEE->value:

                $this->financial_records = StudentCourseFeePayment::query()
                    ->with([
                            'studentCourseFee.student', 
                            'studentCourseFee.branch', 
                            'studentCourseFee.course',
                            'studentCourseFee.course.program.section',
                            'user:id,name,email',
                            
                        ])
                    
                    ->whereBetween('payment_date', [$from, $to])
                    ->whereHas('studentCourseFee.course.program', function ($q) use ($branch_id, $section_id) {
                    
                         $q->when($section_id,function($qq) use($section_id){
                            $qq->where('section_id', $section_id);
                        });
                    })
                    ->whereHas('studentCourseFee', function ($q) use ($branch_id, $section_id) {

                        $q->when($branch_id,function($qq) use($branch_id){
                            $qq->where('branch_id', $branch_id);
                        });
                    })
                    ->when($user_id,function($qq) use($user_id){
                        $qq->where('user_id', $user_id);
                    })
                    ->latest()
                    ->get();
                break;

            case TransactionCategory::BOOK_SALE->value:

                $this->financial_records = StudentBookFee::query()
                    ->with([

                        'student:id,name,last_name,father_name,student_code', 
                        'book',
                        'book.book.program.section',
                        'user:id,name,email',
                        'branch',

                        ])
                    ->whereBetween('payment_date', [$from, $to])
                    ->whereHas('book.book.program', function ($q) use ($section_id) {
                        
                        $q->when($section_id,function($qq) use($section_id){
                            $qq->where('section_id', $section_id);
                        });
                    })

                    ->when($branch_id,function($qq) use($branch_id){
                        $qq->where('branch_id', $branch_id);
                    })
                    ->when($user_id,function($qq) use($user_id){
                        $qq->where('user_id', $user_id);
                    })
                    ->latest()
                    ->get();

                break;

            case TransactionCategory::MAKEUP_FEE->value:

                $this->financial_records = MakeupFee::query()
                    ->with([
                        'student', 
                        'course',
                        'course.program.section',
                        'course.branch',
                        ])
                    ->whereBetween('payment_date', [$from, $to])
                    ->whereHas('course.program', function ($q) use ($section_id) {
    
                        $q->when($section_id,function($qq) use($section_id){
                            $qq->where('section_id', $section_id);
                        });
                    })
                    ->whereHas('course', function ($q) use ($branch_id) {
    
                        $q->when($branch_id,function($qq) use($branch_id){
                            $qq->where('branch_id', $branch_id);
                        });
                    })
                    ->when($user_id,function($qq) use($user_id){
                        $qq->where('user_id', $user_id);
                    })
                    ->latest()
                    ->get();

                break;

            case TransactionCategory::OTHER_FEE->value:

                $this->financial_records = StudentOtherFee::query()
                    ->with(['student', 'feeType','feeType.section','branch','user:id,name,email'])
                    ->whereBetween('payment_date', [$from, $to])
                    ->whereHas('feeType.section', function ($q) use ($section_id) {
                         $q->when($section_id,function($qq) use($section_id){
                            $qq->where('section_id', $section_id);
                        });
                    })
                    ->when($branch_id,function($qq) use($branch_id){
                        $qq->where('branch_id', $branch_id);
                    })
                    ->when($user_id,function($qq) use($user_id){
                        $qq->where('user_id', $user_id);
                    })
                    ->latest()
                    ->get();

                break;

            case TransactionCategory::EXAM_FINE->value:

                $this->financial_records = ExamFine::query()
                    ->with(['student', 'course','course.branch','course.program.section','user:id,name,email'])
                    ->whereBetween('payment_date', [$from, $to])
                    ->where('status', 'paid')
                    ->whereHas('course.program', function ($q) use ($section_id) {

                        $q->when($section_id,function($qq) use($section_id){
                            $qq->where('section_id', $section_id);
                        });
                    })
                    ->whereHas('course', function ($q) use ($branch_id) {

                        $q->when($branch_id,function($qq) use($branch_id){
                            $qq->where('branch_id', $branch_id);
                        });
                    })
                    ->when($user_id,function($qq) use($user_id){
                        $qq->where('user_id', $user_id);
                    })
                    ->latest()
                    ->get();

                break;

            // ================= EXPENSE =================

            case TransactionCategory::EXPENSE->value:

                $this->financial_records = Expense::with('category')
                    ->whereBetween('expense_date', [$from, $to])
                    ->where('branch_id', $branch_id)
                    ->where('section_id', $section_id)
                    ->latest()
                    ->get();

                break;

            case TransactionCategory::SALARY_ADVANCE->value:

                $this->financial_records = EmployeeSalaryAdvance::query()
                    ->with('employee')
                    ->whereBetween('created_at', [$from, $to])
                    ->where('branch_id', $branch_id)
                    ->where('section_id', $section_id)
                    ->latest()
                    ->get();

                break;

            case TransactionCategory::TEMPORARY_SALARY_PAYMENT->value:

                $this->financial_records = TemporaryPayroll::query()
                    ->with('employee')
                    ->whereBetween('payment_date', [$from, $to])
                    ->where('status', 'paid')
                    ->whereHas('temporaryContract', function ($q) use ($branch_id, $section_id) {
                        $q->where('section_id', $section_id);
                    })
                    ->where('branch_id',$branch_id)
                    ->latest()
                    ->get();

                break;

            case TransactionCategory::PERMANENT_SALARY_PAYMENT->value:

                $this->financial_records = PermanentPayroll::query()
                    ->with('employee')
                    ->whereBetween('payment_date', [$from, $to])
                    ->where('status', 'paid')
                    ->whereHas('permanentContract', function ($q) use ($branch_id, $section_id) {
                        
                        $q->where('section_id', $section_id);
                    })
                    ->where('branch_id', $branch_id)
                    ->latest()
                    ->get();

                break;

            case TransactionCategory::BOOK_PURCHASE->value:

                $this->financial_records = BookInventoryMovement::query()
                    ->with(['inventory.warehouse', 'inventory.book'])
                    ->whereBetween('created_at', [$from, $to])
                    ->where('type', 'purchase')
                    ->whereHas('inventory.warehouse', function ($q) use ($branch_id, $section_id) {
                        $q->where('branch_id', $branch_id)
                        ->where('section_id', $section_id);
                    })
                    ->latest()
                    ->get();

                break;

            case TransactionCategory::ASSET->value:

                $this->financial_records = Asset::with('category')
                    ->whereBetween('purchase_date', [$from, $to])
                    ->where('branch_id', $branch_id)
                    ->where('section_id', $section_id)
                    ->latest()
                    ->get();

                break;
        }
    }

    public function updatedSearchCategory(){
        $this->loadDailyIncome();
    }

    public function print()
    {
        
        $this->dispatch('show-print-preview');
    }

    public function exportPdf()
    {
        
        $pdf = Pdf::loadView(
            'livewire.financial.financial-reports.daily-income-report-pdf',
            [
                'incomes' => $this->incomes,
                'selectedFields' => $this->selectedFields,
                'total_income' => $this->total_income,
                'search' =>$this->search,
            ]
        )->setPaper('a4',$this->pdfOrientation)
        ->setOption('defaultFont', 'dejavusans');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            __('daily_income_report').'-' . Carbon::now()->format('Y-m-d -H-i-A') . '.pdf'
        );
    }

    protected function rules()
    {
        $rules =  [


        ];
        return $rules;
    }

}
