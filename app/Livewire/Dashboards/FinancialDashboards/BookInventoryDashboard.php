<?php

namespace App\Livewire\Dashboards\FinancialDashboards;
use Livewire\Component;
use App\Models\Settings\Menu;
use App\Models\User;
use App\Models\Academic\Student;
use App\Models\Academic\Course;
use App\Models\Academic\CourseStudent;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Program;
use App\Models\CenterSettings\Book;
use App\Models\CenterSettings\Section;

// incomes---------------
use App\Models\Financial\StudentCourseFeePayment;
use App\Models\Financial\StudentBookFee;
use App\Models\Financial\MakeupFee;
use App\Models\Financial\StudentOtherFee;
use App\Models\Financial\ExamFine;

// expenses-------------------
use App\Models\Financial\Expense;
use App\Models\Financial\Asset;
use App\Models\Hr\PermanentPayroll;
use App\Models\Hr\TemporaryPayroll;
use App\Models\Hr\EmployeeSalaryAdvance;
use App\Models\Warehouse\BookInventoryMovement;
use App\Models\Warehouse\BookInventory;
use App\Models\Warehouse\Warehouse;


use App\Models\Financial\Transaction;
use App\Enums\TransactionCategory;
use App\Enums\Action;
use App\Models\Financial\Account;

use Auth;
use DB;
use Carbon\Carbon;
class BookInventoryDashboard extends Component
{
    public $active_menu_id;
    public $active_menu;

    public $from_date;
    public $to_date;
    public $genders;
    public $gender;

    public $view_mode_general = 'dashboard';
    public $view_mode = 'dashboard';

    public $auth_branch_id = null;
    public $selected_branch_id = null;
    public $selected_branch_name = null;

    public function mount($active_menu_id = null)
    {
         // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->from_date = now()->toDateString();
        $this->to_date = now()->toDateString();
        $this->auth_branch_id = Auth::user()?->branch_id;
        $this->loadFinancialData();
    }

    public function render()
    {
        return view('livewire.dashboards.financial-dashboards.book-inventory-dashboard');
    }


    public function refreshData()
    {
        $this->loadFinancialData();

        $this->loadBranchFinancialData();

        switch ($this->view_mode_general) {

            case 'central_warehouse_inventory':
                $this->loadCentralWarehouseInventoryData();
                break;
        }

        switch ($this->view_mode) {

            case 'central_warehouse_inventory':
                $this->loadCentralWarehouseInventoryData();
                break;

            case 'branch_financial_section':
                $this->loadBranchFinancialSectionData();
                break;

            case 'section_financial_details':
                $this->loadSectionFinancialDetailsData();
                
                break;

            case 'financial_records':

                $this->loadFinancialRecords();

                break;
        }
    }

    public function updatedFromDate()
    {
        $this->refreshData();
    }

    public function updatedToDate()
    {
        $this->refreshData();
    }

    public $financial_stats = [];

    public function loadFinancialData()
    {
        $from = Carbon::parse($this->from_date)->startOfDay();
        $to = Carbon::parse($this->to_date)->endOfDay();

        // ---------------- Income ----------------

        // sale book-------
        $book_income = StudentBookFee::query()
            ->whereBetween('payment_date', [$from, $to])
            ->when($this->auth_branch_id, function ($q) {
                $q->where('branch_id', $this->auth_branch_id);
            })
            ->sum('price');


        $income = $book_income;

        // ---------------- Expense ----------------

        // book purchase ------------------------

        $book_purchase_expense = BookInventoryMovement::query()
        ->whereBetween('created_at', [$from, $to])
        ->where('type', 'purchase')
        ->when($this->auth_branch_id, function ($q) {
            $q->whereHas('inventory.warehouse', function ($qq) {
                $qq->where('branch_id', $this->auth_branch_id);
            });
        })
        ->sum(DB::raw('quantity_change * unit_price'));

        
        $total_expense = $book_purchase_expense;

        // ---------------- Asset ----------------

        $asset = 0;

        // ---------------- Profit ----------------

        $profit = $income - $total_expense;

        // ---------------- Current Cash ----------------

        $total = max(
            $income,
            $total_expense,
            $profit,
            1
        );

        $this->financial_stats = [

            [
                'label'   => __('label.income'),
                'count'   => $income,
                'percent' => round(($income / $total) * 100, 1),
                'color'   => 'success',
            ],

            [
                'label'   => __('label.expense'),
                'count'   => $total_expense,
                'percent' => round(($total_expense / $total) * 100, 1),
                'color'   => 'danger',
            ],


            [
                'label'   => __('label.profit'),
                'count'   => $profit,
                'percent' => round((abs($profit) / $total) * 100, 1),
                'color'   => $profit >= 0 ? 'info' : 'danger',
            ],
        ];

        $this->loadBranchFinancialData();
        $this->loadCentralWarehouseData();
    }

    // -----------start Central Warehouse Data---------------------
    public $central_warehouses = [];

    public function loadCentralWarehouseData()
    {
        $this->central_warehouses = Warehouse::with('section')->where('type','central')->get();

    }
    
    public $selected_central_warehouse_id;
    public $selected_central_warehouse_name;

    public function openCentralWarehouseDetails($warehouse_id){

        $this->selected_central_warehouse_id = $warehouse_id;
        $this->selected_central_warehouse_name = Warehouse::findOrFail($warehouse_id)->name;
        $this->view_mode_general = 'central_warehouse_inventory';

        $this->loadCentralWarehouseInventoryData();
    }

    public $central_warehouse_inventory_data = [];

    public function loadCentralWarehouseInventoryData(){
        $warehouse_id = $this->selected_central_warehouse_id;
        $this->central_warehouse_inventory_data  = BookInventory::with('book')->where('warehouse_id',$warehouse_id)->get();
    }
    // -----------end Central Warehouse Data-----------------------

    public $branch_financial_stats = [];

    public function loadBranchFinancialData()
    {
        $branches = Branch::query()
            ->when($this->auth_branch_id, function ($q) {
                $q->where('id', $this->auth_branch_id);
            })
            ->get();

        $from = Carbon::parse($this->from_date)->startOfDay();
        $to = Carbon::parse($this->to_date)->endOfDay();

        $this->branch_financial_stats = [];

        foreach ($branches as $branch) {

            // ---------------- Income ----------------

           
            $book_income = StudentBookFee::query()
                ->whereBetween('payment_date', [$from, $to])
                ->where('branch_id', $branch->id)
                ->sum('price');

            $income = $book_income ;

            $total = max($income,1);

            $this->branch_financial_stats[] = [
                'branch_id'   => $branch->id,
                'branch_name' => $branch->name,

                'income'      => $income,

                'income_percent'       => round(($income / $total) * 100, 1),
               
                'is_general' => false,
            ];
        }

    }

    // -------branch shift detaials ----------------------

    
    public function openBranchFinancialDetails($branch_id)
    {
        $this->selected_branch_id = $branch_id;
        $this->selected_branch_name = Branch::findOrFail($branch_id)->name;
        $this->view_mode = 'branch_financial_section';

        $this->loadBranchFinancialSectionData();
    }

    public function loadBranchFinancialSectionData()
    {
        $sections = Section::query()->get();
        $branch_id = $this->selected_branch_id;
        $from = Carbon::parse($this->from_date)->startOfDay();
        $to = Carbon::parse($this->to_date)->endOfDay();

        $this->branch_financial_stats = [];

        foreach ($sections as $section) {

            // ---------------- INCOME ----------------

            $book_income = StudentBookFee::query()
                ->whereBetween('payment_date', [$from, $to])
                ->whereHas('book.book.program.section', function ($q) use ($section) {
                    $q->where('id', $section->id);
                })
                ->where('branch_id',$branch_id)
                ->sum('price');

            $income = $book_income;

    
            $total = max($income, 1);

            $this->branch_financial_stats[] = [
                'section_id'   => $section->id,
                'section_name' => $section->name,

                'income'       => $income,
                
                'income_percent'       => round(($income / $total) * 100, 1),
            ];
        }
    }


    public function backToDashboard()
    {
        $this->view_mode_general = 'dashboard';
        $this->view_mode = 'dashboard';

        $this->selected_central_warehouse_id = null;
        $this->selected_central_warehouse_name= null;

        $this->selected_branch_id = null;
        $this->selected_branch_name = null;

        $this->loadBranchFinancialData();
    }


    // -------------sechtion details-----------------------------------

    public $selected_section_id = null;
    public $selected_section_name = null;

    public $section_financial_details = [];
    
    public function openSectionFinancialDetails($section_id)
    {
        $this->selected_section_id = $section_id;

        $this->selected_section_name = Section::findOrFail($section_id)->name;

        $this->view_mode = 'section_financial_details';

        $this->loadSectionFinancialDetailsData();
    }

    public function loadSectionFinancialDetailsData()
    {
        $from = Carbon::parse($this->from_date)->startOfDay();
        $to = Carbon::parse($this->to_date)->endOfDay();

        $section_id = $this->selected_section_id;
        $branch_id = $this->selected_branch_id;

        $this->section_financial_details = BookInventory::with('book','warehouse')
                ->withSum([
                    'movements as sold_quantity' => function ($query) use ($from, $to) {
                        $query->where('type', 'sale')
                            ->whereBetween('created_at', [$from, $to]);
                    }
                ], 'quantity_change')
                ->withSum([
                    'movements as transfer_in_quantity' => function ($query) use ($from, $to) {
                        $query->where('type', 'transfer_in')
                            ->whereBetween('created_at', [$from, $to]);
                    }
                ], 'quantity_change')
                ->withSum([
                    'movements as transfer_out_quantity' => function ($query) use ($from, $to) {
                        $query->where('type', 'transfer_out')
                            ->whereBetween('created_at', [$from, $to]);
                    }
                ], 'quantity_change')
                ->whereHas('warehouse', function ($query) use ($section_id, $branch_id) {
                    $query->where('section_id', $section_id);
                    $query->where('branch_id', $branch_id);
                })
                ->get();
    }

    public function backToBranchFinancialSection()
    {
        $this->view_mode = 'branch_financial_section';

        $this->loadBranchFinancialSectionData();
    }
    

    // -------------------نظربه هر کتگوی ------------------------------------
    public $financial_detail_type = null;
    public $financial_category_details = [];


    public function backToSectionFinancialDetails()
    {
        $this->view_mode = 'section_financial_details';
        $this->loadSectionFinancialDetailsData();
    }

    // ------------نظر به هر نوع مصارف یا عواید ---------------

    public $selected_category_key = null;
    public $selected_book_id = null;
    public $records_view = null;
    public $financial_records = [];
    
    public function openFinancialRecordDetails($section_id,$book_id)
    {
        $this->selected_section_id = $section_id;
        $this->selected_book_id = $book_id;
        $this->selected_section_name = Section::findOrFail($section_id)->name;
        $this->view_mode = 'financial_records';

        $this->loadFinancialRecords();
    }


    public function loadFinancialRecords()
    {
        $from = Carbon::parse($this->from_date)->startOfDay();
        $to = Carbon::parse($this->to_date)->endOfDay();

        $branch_id = $this->selected_branch_id;
        $section_id = $this->selected_section_id;
        $book_id = $this->selected_book_id;

        $this->financial_records = [];
        $this->records_view = 'book_sale'; //$this->selected_category_key;
        $this->selected_category_key = TransactionCategory::BOOK_SALE->value;

        switch ($this->selected_category_key) {

            // ================= INCOME =================

            case TransactionCategory::BOOK_SALE->value:

                $this->financial_records = StudentBookFee::query()
                    ->with(['student', 'book'])
                    ->whereBetween('payment_date', [$from, $to])
                    ->whereHas('book.book.program', function ($q) use ($section_id) {
                        
                        $q->where('section_id', $section_id);
                    })
                    ->where('physical_book_id',$book_id)
                    ->where('branch_id',$branch_id)
                    ->latest()
                    ->get();

                break;

        }
    }

}
