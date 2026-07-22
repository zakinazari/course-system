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

// expenses-------------------
use App\Models\Financial\Expense;
use App\Models\Financial\Asset;
use App\Models\Hr\PermanentPayroll;
use App\Models\Hr\TemporaryPayroll;
use App\Models\Hr\EmployeeSalaryAdvance;
use App\Models\Warehouse\BookInventoryMovement;

use Auth;
use Carbon\Carbon;
use DB;
use App\Models\User;
use App\Enums\TransactionCategory;

class DailyExpenseReport extends Component
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
        $this->loadDailyExpense();
    }
    
    // ---------------------------------end generals-------------

    public $branches=[];
    public $sections=[];
    public $users=[];
    public $expense_categories=[];

    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->branches =  Branch::all();

        $this->sections =  Section::all();

        $this->expense_categories = TransactionCategory::expenseCategories();

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
            'expense_categories',
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
        return view('livewire.financial.financial-reports.daily-expense-report');
    }

    public $expenses=[];
    public $financial_records = [];
    public $records_view = [];

    public function loadDailyExpense()
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


            // ================= EXPENSE =================

            case TransactionCategory::EXPENSE->value:

                $this->financial_records = Expense::with('category','section','branch','user')
                    ->whereBetween('expense_date', [$from, $to])

                    ->when($section_id,function($qq) use($section_id){
                        $qq->where('section_id', $section_id);
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

            case TransactionCategory::SALARY_ADVANCE->value:

                $this->financial_records = EmployeeSalaryAdvance::query()
                    ->with('employee','section','branch')
                    ->whereBetween('created_at', [$from, $to])
                    ->when($section_id,function($qq) use($section_id){
                        $qq->where('section_id', $section_id);
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

            case TransactionCategory::TEMPORARY_SALARY_PAYMENT->value:

                $this->financial_records = TemporaryPayroll::query()
                    ->with('employee','branch','temporaryContract.section')
                    ->whereBetween('payment_date', [$from, $to])
                    ->where('status', 'paid')
                    ->whereHas('temporaryContract', function ($q) use ($branch_id, $section_id) {
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

            case TransactionCategory::PERMANENT_SALARY_PAYMENT->value:

                $this->financial_records = PermanentPayroll::query()
                    ->with('employee','branch','permanentContract')
                    ->whereBetween('payment_date', [$from, $to])
                    ->where('status', 'paid')
                    ->whereHas('permanentContract', function ($q) use ($branch_id, $section_id) {
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

            case TransactionCategory::ASSET->value:

                $this->financial_records = Asset::with('category','section','branch','user')
                    ->whereBetween('purchase_date', [$from, $to])
                    ->when($section_id,function($qq) use($section_id){
                        $qq->where('section_id', $section_id);
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
        }
    }

    public function print()
    {
        
        $this->dispatch('show-print-preview');
    }

    public function exportPdf()
    {
        
        $pdf = Pdf::loadView(
            'livewire.financial.financial-reports.daily-expense-report-pdf',
            [
                'expenses' => $this->expenses,
                'selectedFields' => $this->selectedFields,
                'total_expense' => $this->total_expense,
                'search' =>$this->search,
            ]
        )->setPaper('a4',$this->pdfOrientation)
        ->setOption('defaultFont', 'dejavusans');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            __('daily_expense_report').'-' . Carbon::now()->format('Y-m-d -H-i-A') . '.pdf'
        );
    }

    protected function rules()
    {
        $rules =  [


        ];
        return $rules;
    }
}
