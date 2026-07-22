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


use App\Models\Financial\Transaction;
use App\Enums\TransactionCategory;
use App\Enums\Action;
use App\Models\Financial\Account;

use Auth;
use DB;
use Carbon\Carbon;
class FinancialDashboard extends Component
{
    
    public $active_menu_id;
    public $active_menu;

    public $from_date;
    public $to_date;
    public $genders;
    public $gender;

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
        return view('livewire.dashboards.financial-dashboards.financial-dashboard');
    }


    public function refreshData()
    {
        $this->loadFinancialData();

        $this->loadBranchFinancialData();

        switch ($this->view_mode) {

            case 'branch_financial_section':
                $this->loadBranchFinancialSectionData();
                break;

            case 'section_financial_details':
                $this->loadSectionFinancialDetailsData();
                
                break;

            case 'financial_category_details':

                $this->loadFinancialCategoryDetails();
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

        $course_income = StudentCourseFeePayment::query()
            ->whereBetween('payment_date', [$from, $to])
            ->whereHas('studentCourseFee', function ($q) {
                $q->when($this->auth_branch_id, function ($qq) {
                    $qq->where('branch_id', $this->auth_branch_id);
                });
            })
            ->sum('amount');

        // sale book-------
        // $book_income = StudentBookFee::query()
        //     ->whereBetween('payment_date', [$from, $to])
        //     ->when($this->auth_branch_id, function ($q) {
        //         $q->where('branch_id', $this->auth_branch_id);
        //     })
        //     ->sum('price');

        // makeup fee-------
        $makeup_fee_income = MakeupFee::query()
        ->whereBetween('payment_date', [$from, $to])
        ->when($this->auth_branch_id, function ($q) {
            $branch_id = $this->auth_branch_id;

            $q->whereHas('course', function ($qq) use ($branch_id) {
                $qq->where('branch_id', $branch_id);
            });
        })
        ->sum('amount');

        // ---------other fee-------------------

        $other_fee_income = StudentOtherFee::query()
            ->whereBetween('payment_date', [$from, $to])
            ->when($this->auth_branch_id, function ($q) {
                $q->where('branch_id', $this->auth_branch_id);
            })
            ->sum('amount');

        // ---------exam fine-------------------

        $exam_fine_income = ExamFine::query()
        ->whereBetween('payment_date', [$from, $to])
        ->when($this->auth_branch_id, function ($q) {
            $branch_id = $this->auth_branch_id;

            $q->whereHas('course', function ($qq) use ($branch_id) {
                $qq->where('branch_id', $branch_id);
            });
        })
        ->where('status','paid')
        ->sum('amount');

        $income = /* $book_income + */ $course_income + $makeup_fee_income + $other_fee_income + $exam_fine_income;


        // ---------------- Expense ----------------

        $expense = Expense::query()
            ->whereBetween('expense_date', [$from, $to])
            ->when($this->auth_branch_id, function ($q) {
                $q->where('branch_id', $this->auth_branch_id);
            })
            ->get()
            ->sum('total_amount');

        $advance = EmployeeSalaryAdvance::query()
            ->whereBetween('created_at', [$from, $to])
            ->when($this->auth_branch_id, function ($q) {
                $q->where('branch_id', $this->auth_branch_id);
            })
            ->sum('total_amount');

        $temporary_payroll = TemporaryPayroll::query()
            ->whereBetween('payment_date', [$from, $to])
            ->when($this->auth_branch_id, function ($q) {
                $q->where('branch_id', $this->auth_branch_id);
            })
            ->where('status', 'paid')
            ->sum('net_salary');

        $permanent_payroll = PermanentPayroll::query()
            ->whereBetween('payment_date', [$from, $to])
            ->when($this->auth_branch_id, function ($q) {
                $q->where('branch_id', $this->auth_branch_id);
            })
            ->where('status', 'paid')
            ->sum('net_salary');

        // book purchase ------------------------

        // $book_purchase_expense = BookInventoryMovement::query()
        // ->whereBetween('created_at', [$from, $to])
        // ->where('type', 'purchase')
        // ->when($this->auth_branch_id, function ($q) {
        //     $q->whereHas('inventory.warehouse', function ($qq) {
        //         $qq->where('branch_id', $this->auth_branch_id);
        //     });
        // })
        // ->sum(DB::raw('quantity_change * unit_price'));

        
        $total_expense = $expense + $advance + $temporary_payroll + $permanent_payroll ;

        // ---------------- Asset ----------------

        $asset = Asset::query()
            ->whereBetween('purchase_date', [$from, $to])
            ->when($this->auth_branch_id, function ($q) {
                $q->where('branch_id', $this->auth_branch_id);
            })
            ->get()
            ->sum(function ($item) {
                return $item->quantity * $item->purchase_price;
            });

        // ---------------- Profit ----------------

        $profit = $income - $total_expense;

        // ---------------- Current Cash ----------------

        $current_cash = $income - $total_expense - $asset;

        $total = max(
            $income,
            $total_expense,
            $asset,
            $profit,
            $current_cash,
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
                'label'   => __('label.asset'),
                'count'   => $asset,
                'percent' => round(($asset / $total) * 100, 1),
                'color'   => 'warning',
            ],

            [
                'label'   => __('label.profit'),
                'count'   => $profit,
                'percent' => round((abs($profit) / $total) * 100, 1),
                'color'   => $profit >= 0 ? 'primary' : 'danger',
            ],

            [
                'label'   => __('label.current_cash'),
                'count'   => $current_cash,
                'percent' => round((abs($current_cash) / $total) * 100, 1),
                'color'   => 'info',
            ],
        ];

        $this->loadBranchFinancialData();
    }

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

            $course_income = StudentCourseFeePayment::query()
                ->whereBetween('payment_date', [$from, $to])
                ->whereHas('studentCourseFee', function ($q) use ($branch) {
                    $q->where('branch_id', $branch->id);
                })
                ->sum('amount');

            // $book_income = StudentBookFee::query()
            //     ->whereBetween('payment_date', [$from, $to])
            //     ->where('branch_id', $branch->id)
            //     ->sum('price');

            $makeup_fee_income = MakeupFee::query()
                ->whereBetween('payment_date', [$from, $to])
                ->whereHas('course', function ($q) use ($branch) {
                    $q->where('branch_id', $branch->id);
                })
                ->sum('amount');

            $other_fee_income = StudentOtherFee::query()
                ->whereBetween('payment_date', [$from, $to])
                ->where('branch_id', $branch->id)
                ->sum('amount');

            $exam_fine_income = ExamFine::query()
                ->whereBetween('payment_date', [$from, $to])
                ->whereHas('course', function ($q) use ($branch) {
                    $q->where('branch_id', $branch->id);
                })
                ->where('status', 'paid')
                ->sum('amount');

            $income =
                $course_income +
                // $book_income +
                $makeup_fee_income +
                $other_fee_income +
                $exam_fine_income;

            // ---------------- Expense ----------------

            $expense = Expense::query()
                ->whereBetween('expense_date', [$from, $to])
                ->where('branch_id', $branch->id)
                ->sum('total_amount');

            $advance = EmployeeSalaryAdvance::query()
                ->whereBetween('created_at', [$from, $to])
                ->where('branch_id', $branch->id)
                ->sum('total_amount');

            $temporary_payroll = TemporaryPayroll::query()
                ->whereBetween('payment_date', [$from, $to])
                ->where('branch_id', $branch->id)
                ->where('status', 'paid')
                ->sum('net_salary');

            $permanent_payroll = PermanentPayroll::query()
                ->whereBetween('payment_date', [$from, $to])
                ->where('branch_id', $branch->id)
                ->where('status', 'paid')
                ->sum('net_salary');

            // $book_purchase_expense = BookInventoryMovement::query()
            //     ->whereBetween('created_at', [$from, $to])
            //     ->where('type', 'purchase')
            //     ->whereHas('inventory.warehouse', function ($q) use ($branch) {
            //         $q->where('branch_id', $branch->id);
            //     })
            //     ->sum(DB::raw('quantity_change * unit_price'));

            $total_expense =
                $expense +
                $advance +
                $temporary_payroll +
                $permanent_payroll +
                // $book_purchase_expense;

            // ---------------- Asset ----------------

            $asset = Asset::query()
                ->whereBetween('purchase_date', [$from, $to])
                ->where('branch_id', $branch->id)
                ->get()
                ->sum(function ($item) {
                    return $item->quantity * $item->purchase_price;
                });

            // ---------------- Profit ----------------

            $profit = $income - $total_expense;

            // ---------------- Cash ----------------

            $current_cash = $income - $total_expense - $asset;

            $total = max($income, $total_expense, $asset, abs($profit), abs($current_cash), 1);

            $this->branch_financial_stats[] = [
                'branch_id'   => $branch->id,
                'branch_name' => $branch->name,

                'income'      => $income,
                'expense'     => $total_expense,
                'asset'       => $asset,
                'profit'      => $profit,
                'current_cash'=> $current_cash,

                'income_percent'       => round(($income / $total) * 100, 1),
                'expense_percent'      => round(($total_expense / $total) * 100, 1),
                'asset_percent'        => round(($asset / $total) * 100, 1),
                'profit_percent'       => round((abs($profit) / $total) * 100, 1),
                'current_cash_percent' => round((abs($current_cash) / $total) * 100, 1),

                'is_general' => false,
            ];
        }

        // خرید کتاب --------------------------------
        // $general_book_purchase = BookInventoryMovement::query()
        // ->whereBetween('created_at', [$from, $to])
        // ->where('type', 'purchase')
        // ->sum(DB::raw('quantity_change * unit_price'));

        // $this->branch_financial_stats[] = [

        //     'branch_id'   => null,
        //     'branch_name' => __('label.general_book_purchase'),

        //     'income'       => 0,
        //     'expense'      => $general_book_purchase,
        //     'asset'        => 0,
        //     'profit'       => -$general_book_purchase,
        //     'current_cash' => -$general_book_purchase,

        //     'income_percent'       => 0,
        //     'expense_percent'      => 100,
        //     'asset_percent'        => 0,
        //     'profit_percent'       => 100,
        //     'current_cash_percent' => 100,

        //     'is_general' => true,
        // ];


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

            $course_income = StudentCourseFeePayment::query()
                ->whereBetween('payment_date', [$from, $to])
                ->whereHas('studentCourseFee', function ($q) use ($branch_id) {
                    $q->where('branch_id', $branch_id);
                })
                ->whereHas('studentCourseFee.course.program', function ($q) use ($section) {
                    $q->where('section_id', $section->id);
                })
                ->sum('amount');

            // $book_income = StudentBookFee::query()
            //     ->whereBetween('payment_date', [$from, $to])
            //     ->whereHas('book.book.program.section', function ($q) use ($section) {
            //         $q->where('id', $section->id);
            //     })
            //     ->where('branch_id',$branch_id)
            //     ->sum('price');

            $makeup_fee_income = MakeupFee::query()
                ->whereBetween('payment_date', [$from, $to])
                ->whereHas('course.program', function ($q) use ($section) {
                    $q->where('section_id', $section->id);
                })
                ->whereHas('course', function ($q) use ($branch_id) {
                    $q->where('branch_id', $branch_id);
                })
                ->sum('amount');

            $other_fee_income = StudentOtherFee::query()
                ->whereBetween('payment_date', [$from, $to])
                ->whereHas('feeType.section', function ($q) use ($section) {
                    $q->where('id', $section->id);
                })
                ->where('branch_id',$branch_id)
                ->sum('amount');

            $exam_fine_income = ExamFine::query()
                ->whereBetween('payment_date', [$from, $to])
                ->whereHas('course.program', function ($q) use ($section) {
                    $q->where('section_id', $section->id);
                })
                ->whereHas('course', function ($q) use ($branch_id) {
                    $q->where('branch_id', $branch_id);
                })
                ->where('status', 'paid')
                ->sum('amount');

            $income =
                $course_income +
                // $book_income +
                $makeup_fee_income +
                $other_fee_income +
                $exam_fine_income;

            // ---------------- EXPENSE ----------------

            $expense = Expense::query()
                ->whereBetween('expense_date', [$from, $to])
                ->where('section_id', $section->id)
                ->where('branch_id', $branch_id)
                ->sum('total_amount');

            $advance = EmployeeSalaryAdvance::query()
                ->whereBetween('created_at', [$from, $to])
                ->where('section_id', $section->id)
                ->where('branch_id', $branch_id)
                ->sum('total_amount');

            $temporary_payroll = TemporaryPayroll::query()
                ->whereBetween('payment_date', [$from, $to])
                ->whereHas('temporaryContract', function ($q) use ($section) {
                    $q->where('section_id', $section->id);
                })
                ->where('branch_id', $branch_id)
                ->where('status', 'paid')
                ->sum('net_salary');

            $permanent_payroll = PermanentPayroll::query()
                ->whereBetween('payment_date', [$from, $to])
                ->whereHas('permanentContract', function ($q) use ($section) {
                    $q->where('section_id', $section->id);
                })
                ->where('branch_id', $branch_id)
                ->where('status', 'paid')
                ->sum('net_salary');

            // $book_purchase_expense = BookInventoryMovement::query()
            //     ->whereBetween('created_at', [$from, $to])
            //     ->where('type', 'purchase')
            //     ->whereHas('inventory.warehouse.section', function ($q) use ($section) {
            //         $q->where('id', $section->id);
            //     })
            //     ->whereHas('inventory.warehouse', function ($q) use ($branch_id) {
            //         $q->where('branch_id', $branch_id);
            //     })
               
            //     ->sum(DB::raw('quantity_change * unit_price'));

            $total_expense =
                $expense +
                $advance +
                $temporary_payroll +
                // $book_purchase_expense;
                $permanent_payroll;

            // ---------------- ASSET ----------------

            $asset = Asset::query()
                ->whereBetween('purchase_date', [$from, $to])
                ->where('section_id', $section->id)
                ->where('branch_id', $branch_id)
                ->get()
                ->sum(function ($item) {
                    return $item->quantity * $item->purchase_price;
                });

            // ---------------- CALC ----------------

            $profit = $income - $total_expense;
            $current_cash = $income - $total_expense - $asset;

            $total = max($income, $total_expense, $asset, abs($profit), abs($current_cash), 1);

            $this->branch_financial_stats[] = [
                'section_id'   => $section->id,
                'section_name' => $section->name,

                'income'       => $income,
                'expense'      => $total_expense,
                'asset'        => $asset,
                'profit'       => $profit,
                'current_cash' => $current_cash,

                'income_percent'       => round(($income / $total) * 100, 1),
                'expense_percent'      => round(($total_expense / $total) * 100, 1),
                'asset_percent'        => round(($asset / $total) * 100, 1),
                'profit_percent'       => round((abs($profit) / $total) * 100, 1),
                'current_cash_percent' => round((abs($current_cash) / $total) * 100, 1),
            ];
        }
    }


    public function backToDashboard()
    {
        $this->view_mode = 'dashboard';

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

        $course_income = StudentCourseFeePayment::query()
            ->whereBetween('payment_date', [$from, $to])
            ->whereHas('studentCourseFee.course.program', function ($q) use ($section_id) {
                $q->where('section_id', $section_id);
            })
            ->whereHas('studentCourseFee', function ($q) use ($branch_id) {
                $q->where('branch_id', $branch_id);
            })
            ->sum('amount');

        // $book_income = StudentBookFee::query()
        //     ->whereBetween('payment_date', [$from, $to])
        //     ->whereHas('book.book.program.section', function ($q) use ($section_id) {
        //         $q->where('id', $section_id);
        //     })
        //     ->where('branch_id',$branch_id)
        //     ->sum('price');

        $makeup_fee_income = MakeupFee::query()
            ->whereBetween('payment_date', [$from, $to])
            ->whereHas('course.program', function ($q) use ($section_id) {
                $q->where('section_id', $section_id);
            })
            ->whereHas('course', function ($q) use ($branch_id) {
                $q->where('branch_id', $branch_id);
            })
            ->sum('amount');

        $other_fee_income = StudentOtherFee::query()
            ->whereBetween('payment_date', [$from, $to])
            ->whereHas('feeType.section', function ($q) use ($section_id) {
                $q->where('id', $section_id);
            })
            ->where('branch_id',$branch_id)
            ->sum('amount');

        $exam_fine_income = ExamFine::query()
            ->whereBetween('payment_date', [$from, $to])
            ->whereHas('course.program', function ($q) use ($section_id) {
                $q->where('section_id', $section_id);
            })
            ->whereHas('course', function ($q) use ($branch_id) {
                $q->where('branch_id', $branch_id);
            })
            ->where('status', 'paid')
            ->sum('amount');

        $expense = Expense::query()
            ->whereBetween('expense_date', [$from, $to])
            ->where('section_id', $section_id)
            ->where('branch_id', $branch_id)
            ->sum('total_amount');

        $advance = EmployeeSalaryAdvance::query()
            ->whereBetween('created_at', [$from, $to])
            ->where('section_id', $section_id)
            ->where('branch_id', $branch_id)
            ->sum('total_amount');

        $temporary_payroll = TemporaryPayroll::query()
            ->whereBetween('payment_date', [$from, $to])
            ->whereHas('temporaryContract', function ($q) use ($section_id) {
                $q->where('section_id', $section_id);
            })
            ->where('branch_id',$branch_id)
            ->where('status', 'paid')
            ->sum('net_salary');

        $permanent_payroll = PermanentPayroll::query()
            ->whereBetween('payment_date', [$from, $to])
            ->whereHas('permanentContract', function ($q) use ($section_id) {
                $q->where('section_id', $section_id);
            })
            ->where('branch_id',$branch_id)
            ->where('status', 'paid')
            
            ->sum('net_salary');

        // $book_purchase = BookInventoryMovement::query()
        //     ->whereBetween('created_at', [$from, $to])
        //     ->where('type', 'purchase')
        //     ->whereHas('inventory.warehouse', function ($q) use ($section_id,$branch_id) {
        //         $q->where('section_id', $section_id);
        //         $q->where('branch_id', $branch_id);
        //     })
        //     ->sum(DB::raw('quantity_change * unit_price'));

        $asset = Asset::query()
            ->whereBetween('purchase_date', [$from, $to])
            ->where('section_id', $section_id)
            ->where('branch_id', $branch_id)
            ->get()
            ->sum(fn($item) => $item->quantity * $item->purchase_price);

        $income =
            $course_income +
            // $book_income +
            $makeup_fee_income +
            $other_fee_income +
            $exam_fine_income;

        $total_expense =
            $expense +
            $advance +
            $temporary_payroll +
            $permanent_payroll;
            // $book_purchase;

        $this->section_financial_details = [

            'course_income' => $course_income,
            // 'book_income' => $book_income,
            'makeup_fee_income' => $makeup_fee_income,
            'other_fee_income' => $other_fee_income,
            'exam_fine_income' => $exam_fine_income,

            'expense' => $expense,
            'advance' => $advance,
            'temporary_payroll' => $temporary_payroll,
            'permanent_payroll' => $permanent_payroll,
            // 'book_purchase' => $book_purchase,

            'asset' => $asset,

            'income' => $income,
            'total_expense' => $total_expense,
            'profit' => $income - $total_expense,
            'current_cash' => $income - $total_expense - $asset,
        ];
    }


    public function backToBranchFinancialSection()
    {
        $this->view_mode = 'branch_financial_section';

        $this->loadBranchFinancialSectionData();
    }
    

    // -------------------نظربه هر کتگوی ------------------------------------
    public $financial_detail_type = null;
    public $financial_category_details = [];


    public function openFinancialCategoryDetails($type)
    {
        $this->financial_detail_type = $type;

        $this->view_mode = 'financial_category_details';

        $this->loadFinancialCategoryDetails();
    }

    public function loadFinancialCategoryDetails()
    {
        $from = Carbon::parse($this->from_date)->startOfDay();
        $to = Carbon::parse($this->to_date)->endOfDay();

        $section_id = $this->selected_section_id;
        $branch_id = $this->selected_branch_id;

        $this->financial_category_details = [];

        if ($this->financial_detail_type === 'income') {

            $course_income = StudentCourseFeePayment::query()
                ->whereBetween('payment_date', [$from, $to])
                ->whereHas('studentCourseFee.course.program', function ($q) use ($section_id) {
                    $q->where('section_id', $section_id);
                })
                ->whereHas('studentCourseFee', function ($q) use ($branch_id) {
                    $q->where('branch_id', $branch_id);
                })
                ->sum('amount');

            // $book_income = StudentBookFee::query()
            //     ->whereBetween('payment_date', [$from, $to])
            //     ->whereHas('book.book.program', function ($q) use ($section_id) {
            //         $q->where('section_id', $section_id);
            //     })
            //     ->where('branch_id',$branch_id)
            //     ->sum('price');

            $makeup_fee_income = MakeupFee::query()
                ->whereBetween('payment_date', [$from, $to])
                ->whereHas('course.program', function ($q) use ($section_id) {
                    $q->where('section_id', $section_id);
                })
                ->whereHas('course', function ($q) use ($branch_id) {
                    $q->where('branch_id', $branch_id);
                })
                ->sum('amount');

            $other_fee_income = StudentOtherFee::query()
                ->whereBetween('payment_date', [$from, $to])
                ->whereHas('feeType.section', function ($q) use ($section_id) {
                    $q->where('id', $section_id);
                })
                ->where('branch_id',$branch_id)
                ->sum('amount');

            $exam_fine_income = ExamFine::query()
                ->whereBetween('payment_date', [$from, $to])
                ->whereHas('course.program', function ($q) use ($section_id) {
                    $q->where('section_id', $section_id);
                })
                ->whereHas('course', function ($q) use ($branch_id) {
                    $q->where('branch_id', $branch_id);
                })
                ->where('status', 'paid')
                ->sum('amount');

            $this->financial_category_details = [

                [
                    'key' => TransactionCategory::COURSE_FEE->value,
                    'name' => __('label.course_fee'),
                    'amount' => $course_income,
                ],

                // [
                //     'key' => TransactionCategory::BOOK_SALE->value,
                //     'name' => __('label.book_sale'),
                //     'amount' => $book_income,
                // ],

                [
                    'key' => TransactionCategory::MAKEUP_FEE->value,
                    'name' => __('label.makeup_fee'),
                    'amount' => $makeup_fee_income,
                ],


                [
                    'key' => TransactionCategory::EXAM_FINE->value,
                    'name' => __('label.exam_fine'),
                    'amount' => $exam_fine_income,
                ],

                
                [
                    'key' => TransactionCategory::OTHER_FEE->value,
                    'name' => __('label.other_fee'),
                    'amount' => $other_fee_income,
                ],

            ];
        }

        elseif ($this->financial_detail_type === 'expense') {

            $expense = Expense::query()
                ->whereBetween('expense_date', [$from, $to])
                ->where('section_id', $section_id)
                ->where('branch_id', $branch_id)
                ->sum('total_amount');

            $advance = EmployeeSalaryAdvance::query()
                ->whereBetween('created_at', [$from, $to])
                ->where('section_id', $section_id)
                ->where('branch_id', $branch_id)
                ->sum('total_amount');

            $temporary_payroll = TemporaryPayroll::query()
                ->whereBetween('payment_date', [$from, $to])
                ->whereHas('temporaryContract', function ($q) use ($section_id) {
                    $q->where('section_id', $section_id);
                })
                ->where('branch_id',$branch_id)
                ->where('status', 'paid')
                ->sum('net_salary');

            $permanent_payroll = PermanentPayroll::query()
                ->whereBetween('payment_date', [$from, $to])
                ->whereHas('permanentContract', function ($q) use ($section_id) {
                    $q->where('section_id', $section_id);
                })
                ->where('branch_id', $branch_id)
                ->where('status', 'paid')
                ->sum('net_salary');

            // $book_purchase = BookInventoryMovement::query()
            //     ->whereBetween('created_at', [$from, $to])
            //     ->where('type', 'purchase')
            //     ->whereHas('inventory.warehouse', function ($q) use ($section_id,$branch_id) {
            //         $q->where('section_id', $section_id);
            //         $q->where('branch_id', $branch_id);
            //     })
            //     ->sum(DB::raw('quantity_change * unit_price'));

            $this->financial_category_details = [

                [
                    'key' => TransactionCategory::EXPENSE->value,
                    'name' => __('label.expense'),
                    'amount' => $expense,
                ],

                [
                    'key' => TransactionCategory::SALARY_ADVANCE->value,
                    'name' => __('label.salary_advance'),
                    'amount' => $advance,
                ],

                [
                    'key' => TransactionCategory::TEMPORARY_SALARY_PAYMENT->value,
                    'name' => __('label.temporary_payroll'),
                    'amount' => $temporary_payroll,
                ],

                [
                    'key' => TransactionCategory::PERMANENT_SALARY_PAYMENT->value,
                    'name' => __('label.permanent_payroll'),
                    'amount' => $permanent_payroll,
                ],

                // [
                //     'key' => TransactionCategory::BOOK_PURCHASE->value,
                //     'name' => __('label.book_purchase'),
                //     'amount' => $book_purchase,
                // ],

            ];
        }

        elseif ($this->financial_detail_type === 'asset') {

            $assets = Asset::query()
                ->whereBetween('purchase_date', [$from, $to])
                ->where('section_id', $section_id)
                ->where('branch_id', $branch_id)
                ->get();

            foreach ($assets as $asset) {

                $this->financial_category_details[] = [

                    'key' => TransactionCategory::ASSET->value,

                    'name' => $asset->name,

                    'amount' => $asset->quantity * $asset->purchase_price,

                ];
            }
        }
    }

    public function backToSectionFinancialDetails()
    {
        $this->view_mode = 'section_financial_details';
        $this->loadFinancialCategoryDetails();
    }

   

    // ------------نظر به هر نوع مصارف یا عواید ---------------

    public $selected_category_key = null;
    public $records_view = null;
    public $financial_records = [];
    
    public function openFinancialRecordDetails($key)
    {
        $this->selected_category_key = $key;

        $this->view_mode = 'financial_records';

        $this->loadFinancialRecords();
    }


    public function loadFinancialRecords()
    {
        $from = Carbon::parse($this->from_date)->startOfDay();
        $to = Carbon::parse($this->to_date)->endOfDay();

        $branch_id = $this->selected_branch_id;
        $section_id = $this->selected_section_id;

        $this->financial_records = [];
        $this->records_view = $this->selected_category_key;

        switch ($this->selected_category_key) {

            // ================= INCOME =================

            case TransactionCategory::COURSE_FEE->value:

                $this->financial_records = StudentCourseFeePayment::query()
                    ->with(['studentCourseFee.student', 'studentCourseFee.course'])
                    ->whereBetween('payment_date', [$from, $to])
                    ->whereHas('studentCourseFee.course.program', function ($q) use ($branch_id, $section_id) {
                        $q->where('section_id', $section_id);
                    })
                    ->whereHas('studentCourseFee', function ($q) use ($branch_id, $section_id) {
                        $q->where('branch_id', $branch_id);
                    })
                    ->latest()
                    ->get();

                break;

            // case TransactionCategory::BOOK_SALE->value:

            //     $this->financial_records = StudentBookFee::query()
            //         ->with(['student', 'book'])
            //         ->whereBetween('payment_date', [$from, $to])
            //         ->whereHas('book.book.program', function ($q) use ($section_id) {
                        
            //             $q->where('section_id', $section_id);
            //         })
            //         ->where('branch_id',$branch_id)
            //         ->latest()
            //         ->get();

            //     break;

            case TransactionCategory::MAKEUP_FEE->value:

                $this->financial_records = MakeupFee::query()
                    ->with(['student', 'course'])
                    ->whereBetween('payment_date', [$from, $to])
                    ->whereHas('course.program', function ($q) use ($section_id) {
    
                        $q->where('section_id', $section_id);
                    })
                    ->whereHas('course', function ($q) use ($branch_id) {
    
                        $q->where('branch_id', $branch_id);
                    })
                    ->latest()
                    ->get();

                break;

            case TransactionCategory::OTHER_FEE->value:

                $this->financial_records = StudentOtherFee::query()
                    ->with(['student', 'feeType'])
                    ->whereBetween('payment_date', [$from, $to])
                    ->whereHas('feeType.section', function ($q) use ($section_id) {
                        $q->where('id', $section_id);
                    })
                    ->where('branch_id',$branch_id)
                    ->latest()
                    ->get();

                break;

            case TransactionCategory::EXAM_FINE->value:

                $this->financial_records = ExamFine::query()
                    ->with(['student', 'course'])
                    ->whereBetween('payment_date', [$from, $to])
                    ->where('status', 'paid')
                    ->whereHas('course.program', function ($q) use ($section_id) {
                        $q->where('section_id', $section_id);
                    })
                    ->whereHas('course', function ($q) use ($branch_id) {
                        $q->where('branch_id', $branch_id);
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

            // case TransactionCategory::BOOK_PURCHASE->value:

            //     $this->financial_records = BookInventoryMovement::query()
            //         ->with(['inventory.warehouse', 'inventory.book'])
            //         ->whereBetween('created_at', [$from, $to])
            //         ->where('type', 'purchase')
            //         ->whereHas('inventory.warehouse', function ($q) use ($branch_id, $section_id) {
            //             $q->where('branch_id', $branch_id)
            //             ->where('section_id', $section_id);
            //         })
            //         ->latest()
            //         ->get();

            //     break;

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


    public function backToFinancialCategory()
    {
        $this->view_mode = 'financial_category_details';

        $this->loadFinancialCategoryDetails();
    }

}
