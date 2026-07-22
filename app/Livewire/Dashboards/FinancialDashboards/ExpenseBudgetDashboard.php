<?php

namespace App\Livewire\Dashboards\FinancialDashboards;

use Livewire\Component;
use App\Models\Settings\Menu;
use App\Models\User;
use App\Models\Academic\Student;
use App\Models\Academic\CourseStudent;
use App\Models\Academic\Course;
use App\Models\Hr\Employee;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Program;
use App\Models\CenterSettings\Book;
use App\Models\CenterSettings\Section;
use App\Models\CenterSettings\Shift;
use App\Models\Financial\Expense;
use App\Models\Hr\PermanentContract;
use App\Models\Hr\TemporaryContract;
use App\Models\Hr\PermanentPayroll;
use App\Models\Hr\TemporaryPayroll;
use App\Models\Financial\ExpenseBudget;
use App\Models\CenterSettings\Year;
use App\Models\CenterSettings\Month;
use Auth;
use DB;
use Carbon\Carbon;
use Verta;
class ExpenseBudgetDashboard extends Component
{
    public $active_menu_id;
    public $active_menu;

    public $from_date;
    public $to_date;
    public $genders;
    public $gender;
    public $shifts;

    public $view_mode_general = 'dashboard';
    public $view_mode = 'dashboard';

    public $auth_branch_id = null;

    public $years = [];
    public $months = [];

    public $year;
    public $month;

    public function mount($active_menu_id = null)
    {
         // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->years =  Year::orderBy('year','desc')->get();
        $this->months =  Month::all();

        $now = Verta::now();
        $this->year = $now->year;
        $this->month = $now->month;

        $this->auth_branch_id = Auth::user()?->branch_id;

        $this->shifts = Shift::all();
       $this->loadBranchExpenseBudgetData();
    }

    public function render()
    {
        return view('livewire.dashboards.financial-dashboards.expense-budget-dashboard');
    }

     public function refreshData()
    {
        $this->loadBranchExpenseBudgetData();

        switch ($this->view_mode) {

            case 'branch_expense_section':
                $this->loadBranchExpenseSectionData();
                break;

            case 'section_expense_category':
                $this->loadSectionExpenseCategoryData();
                
                break;
        }
    }

    public function updatedYear()
    {
        $this->refreshData();
    }

    public function updatedMonth()
    {
        $this->refreshData();
    }
    

    public $branch_expense_budget_stats = [];

    public function loadBranchExpenseBudgetData()
    {

        [$start, $end] = jalaliToGregorianMonthRange($this->year, $this->month);

        $branches = Branch::query()
            ->when($this->auth_branch_id, function($q) {

                $q->where('id',$this->auth_branch_id);

            })
            ->get();


        $this->branch_expense_budget_stats = [];


        foreach($branches as $branch){


            $budgets = ExpenseBudget::with('category')
                ->where('branch_id',$branch->id)
                ->whereDate('effective_from','<=',$end)
                ->where(function($q) use($start){

                    $q->whereNull('effective_to')
                    ->orWhereDate('effective_to','>=',$start);

                })
                ->get();


            if($budgets->isEmpty()) {
                continue;
            }

            
            $budget = 0;
            $paid = 0;


            $has_permanent = false;
            $has_temporary = false;


            foreach($budgets as $item){


                switch ($item->category->type) {


                    case 'expense':

                        $budget += $item->amount;

                        $paid += Expense::query()
                            ->where('branch_id',$branch->id)
                            ->where(
                                'expense_category_id',
                                $item->expense_category_id
                            )
                            ->whereBetween('expense_date',[$start,$end])
                            ->sum('total_amount');

                    break;



                    case 'permanent_payroll':

                        $has_permanent = true;

                    break;



                    case 'temporary_payroll':

                        $has_temporary = true;

                    break;


                }

            }


            /*
            |--------------------------------------------------------------------------
            | Payroll Budget
            |--------------------------------------------------------------------------
            */


            if($has_permanent){

                $budget += $this->calculatePayrollBudget(
                    $branch->id,
                    $start,
                    $end,
                    'permanent'
                );


                $paid += PermanentPayroll::query()
                    ->where('branch_id',$branch->id)
                    ->where('status','paid')
                    ->where('year',$this->year)
                    ->where('month_id',$this->month)
                    ->sum('net_salary');

            }


            if($has_temporary){

                $budget += $this->calculatePayrollBudget(
                    $branch->id,
                    $start,
                    $end,
                    'temporary'
                );


                $paid += TemporaryPayroll::query()
                    ->where('branch_id',$branch->id)
                    ->where('status','paid')
                    ->where('year',$this->year)
                    ->where('month_id',$this->month)
                    ->sum('net_salary');

            }


            $remaining = $budget - $paid;



            $total = max(
                $budget,
                $paid,
                abs($remaining),
                1
            );

            // درصد واقعی مصرف از بودجه
            $actual_paid_percent = $budget > 0
                ? round(($paid / $budget) * 100, 1)
                : 0;



            $this->branch_expense_budget_stats[] = [


                'branch_id'=>$branch->id,

                'branch_name'=>$branch->name,


                'budget'=>$budget,

                'paid'=>$paid,

                'remaining'=>$remaining,


                // درصد برای progress bar
                'budget_percent'=>round(
                    ($budget/$total)*100,
                    1
                ),


                'paid_percent'=>round(
                    ($paid/$total)*100,
                    1
                ),


                'remaining_percent'=>round(
                    (abs($remaining)/$total)*100,
                    1
                ),


                // درصد واقعی مصرف
                'actual_paid_percent'=>$actual_paid_percent,


                'is_over_budget'=>$paid > $budget,


            ];

        }

    }

    public $selected_branch_id;
    public $selected_branch_name;

    public $branch_expense_section_stats = [];

    public function openBranchExpenseBudgetDetails($branch_id)
    {
        $this->selected_branch_id = $branch_id;

        $this->selected_branch_name = Branch::findOrFail($branch_id)->name;

        $this->view_mode = 'branch_expense_section';

        $this->loadBranchExpenseSectionData();
    }

    public function loadBranchExpenseSectionData()
    {
        [$start, $end] = jalaliToGregorianMonthRange($this->year, $this->month);
        $branch_id = $this->selected_branch_id;

        $this->branch_expense_section_stats = [];

        $sections = Section::all();


        foreach ($sections as $section) {


            $budgets = ExpenseBudget::with('category')
                ->where('branch_id', $branch_id)
                ->where('section_id', $section->id)
                ->whereDate('effective_from', '<=', $end)
                ->where(function ($q) use($start){

                    $q->whereNull('effective_to')
                        ->orWhereDate('effective_to', '>=',$start);

                })
                ->get();
          


            if ($budgets->isEmpty()) {
                continue;
            }



            // ---------------- Budget ----------------

            $budget = 0;



            // ---------------- Paid ----------------

            $paid = 0;



            foreach($budgets as $item){


                switch ($item->category->type) {

                    /*
                    |--------------------------------------------------------------------------
                    | Normal Expense
                    |--------------------------------------------------------------------------
                    */

                    case 'expense':

                        $budget += $item->amount;

                        $paid += Expense::query()
                            ->where('branch_id', $branch_id)
                            ->where('section_id', $section->id)
                            ->where('expense_category_id', $item->expense_category_id)
                            ->whereBetween('expense_date', [$start, $end])
                            ->sum('total_amount');

                    break;


                    /*
                    |--------------------------------------------------------------------------
                    | Permanent Payroll
                    |--------------------------------------------------------------------------
                    */

                    case 'permanent_payroll':

                        $budget += $this->calculatePayrollBudget(
                            $branch_id,
                            $start,
                            $end,
                            'permanent',
                            $section->id,
                        );

                        $paid += PermanentPayroll::query()
                            ->where('branch_id', $branch_id)
                            ->whereHas('permanentContract', function ($q) use ($section) {

                                $q->where('section_id', $section->id);

                            })
                            ->where('status', 'paid')
                            ->where('year', $this->year)
                            ->where('month_id', $this->month)
                            ->sum('net_salary');

                    break;


                    /*
                    |--------------------------------------------------------------------------
                    | Temporary Payroll
                    |--------------------------------------------------------------------------
                    */

                    case 'temporary_payroll':

                        $budget += $this->calculatePayrollBudget(
                            $branch_id,
                            $start,
                            $end,
                            'temporary',
                            $section->id,
                        );

                        $paid += TemporaryPayroll::query()
                            ->where('branch_id', $branch_id)
                            ->whereHas('temporaryContract', function ($q) use ($section) {

                                $q->where('section_id', $section->id);

                            })
                            ->where('status', 'paid')
                            ->where('year', $this->year)
                            ->where('month_id', $this->month)
                            ->sum('net_salary');

                    break;

                }

            }


            // ---------------- Remaining ----------------

            $remaining = $budget - $paid;



            // ---------------- Percent ----------------

            $budget_percent = 100;


            if($budget > 0){


                $paid_percent = round(
                    ($paid / $budget) * 100,
                    1
                );


                $remaining_percent = round(
                    (max($remaining,0) / $budget) * 100,
                    1
                );


            }else{


                $paid_percent = 0;

                $remaining_percent = 0;

            }



            $this->branch_expense_section_stats[] = [


                'section_id'=>$section->id,


                'section_name'=>$section->name,


                'budget'=>$budget,


                'paid'=>$paid,


                'remaining'=>$remaining,


                'budget_percent'=>$budget_percent,


                'paid_percent'=>min($paid_percent,100),


                'remaining_percent'=>min($remaining_percent,100),


                // درصد واقعی مصرف
                'actual_paid_percent'=>$paid_percent,


                'is_over_budget'=>$paid > $budget,


            ];


        }

    }

    public $section_expense_category_stats = [];

    public $selected_section_id;
    public $selected_section_name;

    public function openSectionExpenseBudgetDetails($section_id)
    {
        $this->selected_section_id = $section_id;

        $this->selected_section_name = Section::findOrFail($section_id)->name;

        $this->view_mode = 'section_expense_category';

        $this->loadSectionExpenseCategoryData();
    }

    public function loadSectionExpenseCategoryData()
    {
        [$start, $end] = jalaliToGregorianMonthRange($this->year, $this->month);

        $branch_id = $this->selected_branch_id;
        $section_id = $this->selected_section_id;

        $this->section_expense_category_stats = [];

        $budgets = ExpenseBudget::with('category')
            ->where('branch_id', $branch_id)
            ->where('section_id', $section_id)
            ->whereDate('effective_from', '<=', $end)
            ->where(function ($q) use ($start) {

                $q->whereNull('effective_to')
                    ->orWhereDate('effective_to', '>=', $start);

            })
            ->get();

        foreach ($budgets as $budget) {

            /*
            |--------------------------------------------------------------------------
            | Default Budget
            |--------------------------------------------------------------------------
            */
            $budget_amount = $budget->amount;
            $paid = 0;

            switch ($budget->category->type) {

                /*
                |--------------------------------------------------------------------------
                | Expense
                |--------------------------------------------------------------------------
                */
                case 'expense':

                    $paid = Expense::query()
                        ->where('branch_id', $branch_id)
                        ->where('section_id', $section_id)
                        ->where('expense_category_id', $budget->expense_category_id)
                        ->whereBetween('expense_date', [$start, $end])
                        ->sum('total_amount');

                break;

                /*
                |--------------------------------------------------------------------------
                | Permanent Payroll
                |--------------------------------------------------------------------------
                */
                case 'permanent_payroll':

                    $budget_amount = $this->calculatePayrollBudget(
                        $branch_id,
                        $start,
                        $end,
                        'permanent',
                        $section_id,
                    );

                    $paid = PermanentPayroll::query()
                        ->where('branch_id', $branch_id)
                        ->whereHas('permanentContract', function ($q) use ($section_id) {

                            $q->where('section_id', $section_id);

                        })
                        ->where('status', 'paid')
                        ->where('year', $this->year)
                        ->where('month_id', $this->month)
                        ->sum('net_salary');

                break;

                /*
                |--------------------------------------------------------------------------
                | Temporary Payroll
                |--------------------------------------------------------------------------
                */
                case 'temporary_payroll':

                    $budget_amount = $this->calculatePayrollBudget(
                        $branch_id,
                        $start,
                        $end,
                        'temporary',
                        $section_id,
                    );

                    $paid = TemporaryPayroll::query()
                        ->where('branch_id', $branch_id)
                        ->whereHas('temporaryContract', function ($q) use ($section_id) {

                            $q->where('section_id', $section_id);

                        })
                        ->where('status', 'paid')
                        ->where('year', $this->year)
                        ->where('month_id', $this->month)
                        ->sum('net_salary');

                break;

            }

            /*
            |--------------------------------------------------------------------------
            | Remaining
            |--------------------------------------------------------------------------
            */
            $remaining = $budget_amount - $paid;

            /*
            |--------------------------------------------------------------------------
            | Percentages
            |--------------------------------------------------------------------------
            */
            $budget_percent = 100;

            if ($budget_amount > 0) {

                $paid_percent = round(
                    ($paid / $budget_amount) * 100,
                    1
                );

                $remaining_percent = round(
                    (max($remaining, 0) / $budget_amount) * 100,
                    1
                );

            } else {

                $paid_percent = 0;
                $remaining_percent = 0;

            }

            /*
            |--------------------------------------------------------------------------
            | Result
            |--------------------------------------------------------------------------
            */
            $this->section_expense_category_stats[] = [

                'category_id' => $budget->expense_category_id,

                'category_name' => $budget->category?->name,

                'category_type' => $budget->category?->type,

                'budget' => $budget_amount,

                'paid' => $paid,

                'remaining' => $remaining,

                'budget_percent' => $budget_percent,

                'paid_percent' => min($paid_percent, 100),

                'remaining_percent' => min($remaining_percent, 100),

                'actual_paid_percent' => $paid_percent,

                'is_over_budget' => $paid > $budget_amount,

            ];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Payroll Budget
    |--------------------------------------------------------------------------
    |
    | Calculates payroll budget from active contracts.
    | Permanent:
    |   basic_salary + taxi_fare + credit_card
    |
    | Temporary:
    |   SUM(book_salary_rates.amount)
    |   + taxi_fare
    |   + credit_card
    |
    */


    private function calculatePayrollBudget($branch_id, $start, $end, $type,  $section_id = null)
    {
        if ($type === 'permanent') {

            return PermanentContract::query()
                ->where('branch_id', $branch_id)
                ->when($section_id, function ($q) use ($section_id) {

                    $q->where('section_id', $section_id);

                })
                ->where('status', 'active')
                ->whereDate('start_date', '<=', $end)
                ->where(function ($q) use ($start) {

                    $q->whereNull('end_date')
                        ->orWhereDate('end_date', '>=', $start);

                })
                ->get()
                ->sum(function ($contract) {

                    return
                        $contract->basic_salary +
                        $contract->taxi_fare +
                        $contract->credit_card;

                });

        }

        return TemporaryContract::query()
            ->with('bookSalaryRates')
            ->where('branch_id', $branch_id)
            ->when($section_id, function ($q) use ($section_id) {

                $q->where('section_id', $section_id);

            })
            ->where('status', 'active')
            ->whereDate('start_date', '<=', $end)
            ->where(function ($q) use ($start) {

                $q->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $start);

            })
            ->get()
            ->sum(function ($contract) {

                $course_salary = Course::where('teacher_id', $contract->employee_id)
                    ->get()
                    ->sum(function ($course) use ($contract) {

                        return optional(
                            $contract->bookSalaryRates
                                ->firstWhere('book_id', $course->book_id)
                        )->amount ?? 0;

                    });

                return
                    $course_salary +
                    $contract->taxi_fare +
                    $contract->credit_card;

            });
    }
}
