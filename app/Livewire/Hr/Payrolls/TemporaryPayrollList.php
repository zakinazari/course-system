<?php

namespace App\Livewire\Hr\Payrolls;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\CenterSettings\Branch;
use App\Models\Hr\Position;
use App\Models\Hr\Employee;
use App\Models\Hr\TemporaryContract;
use App\Models\Hr\EmployeeSalaryAdvance;
use App\Models\Hr\EmployeeSalaryAdvancePayment;
use App\Models\Hr\TemporaryPayroll;
use App\Models\Hr\TemporaryPayrollDetail;

use App\Models\Assessment\TeacherAttendance;
use App\Models\CenterSettings\Year;
use App\Models\CenterSettings\Month;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;
use Auth;
use DB;
use App\Enums\TransactionCategory;
use App\Enums\Action;
use App\Services\TransactionService;
use App\Models\Financial\Account;
class TemporaryPayrollList extends Component
{
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'temporary-payroll-list-addEditModal';
    public $table_name='temporary_contracts';
    public $pdfOrientation = 'landscape';

    public $selectedFields = [
        'no',
        'employee_id',
        'status',
        'gross_salary',
        'taxi_fare',
        'credit_card',
        'tax',
        'food_deduction',
        'advance_deduction',
        'net_salary',
        'payment_date',
    ];

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

        $this->processMonthlyEmployeePayroll();
    }
    
    // ---------------------------------end generals-------------
    public $years = [];
    public $months = [];

    public $positions = [];
    public $employees = [];
    public $branches = [];
    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->employees =  Employee::whereHas('activeTemporaryContract')
        ->select('id', 'name','last_name','employee_code')
        ->get();
       
        $this->positions =  Position::all();
        $this->years =  Year::orderBy('year','desc')->get();
        $this->months =  Month::all();
        $this->branches =  Branch::all();
    }

    public $position_id;
    public $employee_id;
    public $branch_id;

    public $year;
    public $month;
  
    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'positions',
            'employees',
            'years',
            'months',
            'branches',
        ]);
    }
    public $search = [
            'employee_id' => null,
            'position_id' => null,
            'from_date' => null,
            'to_date' => null,
            'status' => null,
        ];

    public function render()
    {
        $selected_employees = $this->selected_employees;
        return view('livewire.hr.payrolls.temporary-payroll-list',compact('selected_employees'));
    }

    protected function rules()
    {
        $rules = [
            'year'   => 'required|exists:years,year',
            'month'  => 'required|exists:months,id',
        ];

        return $rules;
    }
    // Localized messages
    protected function messages()
    {
        return [
            'year.required' => __('label.year.required'),
            'month.required' => __('label.month.required'),
        ];
    }

    public function updatedPositionId()
    {
        $branch_id = Auth::user()->branch_id ?: $this->branch_id;
        if ($this->position_id) {
            $this->employees = Employee::whereHas('activeTemporaryContract', function ($q) use($branch_id){
                    $q->where('position_id', $this->position_id);
                    $q->where('branch_id', $branch_id);
                })
                ->select('id', 'name', 'last_name', 'employee_code','branch_id')
                ->get();
        }

        if (!$this->position_id) {
            $this->resetPayrollData();
            return;
        }
        
        $this->processMonthlyEmployeePayroll();
    }

    public function updatedBranchId()
    {
         $branch_id = Auth::user()->branch_id ?: $this->branch_id;
        if ($this->branch_id) {
            $this->employees = Employee::whereHas('activeTemporaryContract', function ($q) use($branch_id){
                    $q->where('branch_id', $branch_id);
                })
                ->select('id', 'name', 'last_name', 'employee_code','branch_id')
                ->get();
        }
        if (!$this->branch_id) {
            $this->resetPayrollData();
            return;
        }
        $this->processMonthlyEmployeePayroll();
    }

    public $selected_employees = [];

    public function resetPayrollData()
    {
        $this->reset(['selected_employees']);
    }

    public function updatedYear(){
        if (!$this->year) {

            $this->resetPayrollData();
            return;
        }

        $this->processMonthlyEmployeePayroll();
    }
    public function updatedMonth(){
        if (!$this->month) {
            $this->resetPayrollData();
            return;
        }
        
        $this->processMonthlyEmployeePayroll();
    }

    public function updatedEmployeeId(){
       if (!$this->employee_id) {
            $this->resetPayrollData();
            return;
        }
        
        $this->processMonthlyEmployeePayroll();
    }

    
    public function processMonthlyEmployeePayroll(){

        $branch_id = Auth::user()->branch_id ?: $this->branch_id;

        if (!$this->year) {
            $this->dispatch('alert', type: 'error', message: __('label.year.required'));
            $this->resetPayrollData();
            return;
        }

        if (!$this->month) {
            $this->dispatch('alert', type: 'error', message: __('label.month.required'));
            $this->resetPayrollData();
            return;
        }

        if (!$branch_id) {
            $this->dispatch('alert', type: 'error', message: __('label.branch.required'));
            $this->resetPayrollData();
            return;
        }

        [$start, $end] = jalaliToGregorianMonthRange($this->year, $this->month);

   
        $employees = Employee::whereHas('activeTemporaryContract', function ($q) use($branch_id){
            $q->where('branch_id',$branch_id);
            if ($this->position_id) {
                $q->where('position_id', $this->position_id);
            }
        })
        ->with([
            'branch',
            'activeTemporaryContract' => function ($q) use ($branch_id) {
                $q->where('branch_id', $branch_id);
            },
            'activeTemporaryContract' => function ($q) use ($branch_id) {
                $q->where('branch_id', $branch_id)
                ->with([
                    'bookSalaryRates.book'
                ]);
            },
            'temporaryPayrolls' => function ($q) use($branch_id){
                $q->where('branch_id',$branch_id);
                $q->where('year', $this->year)
                ->where('month_id', $this->month);
            }
        ])
        ->select('id', 'name', 'last_name', 'employee_code','branch_id')
        ->when(!empty($this->employee_id), function ($q) {
            $q->where('id', $this->employee_id);
        })
        // ->when(!empty($this->branch_id), function ($q) use($branch_id){
        //     $q->where('branch_id', $branch_id);
        // })
        ->get();

        foreach ($employees as $key => $employee) {
           $employee->payroll = $employee->temporaryPayrolls->first();
        }

        $this->selected_employees = $employees;
    }


    public function calculateEmployeeBookSalary($employee, $attendances)
    {
        $branch_id = Auth::user()->branch_id ?: $this->branch_id;

        $contract = $employee->activeTemporaryContract
        ->where('branch_id',$branch_id)->first();

        $gross_salary = 0;
        $details = [];
        $employee_attendances = $attendances->get($employee->id, collect());
        
        foreach ($contract->bookSalaryRates as $book_rate) {

            $attendance_count = $employee_attendances
                ->filter(fn($att) => $att->course->book_id == $book_rate->book_id)
                ->count();

            if ($book_rate->book->total_teaching_days == 0) continue;

            $amount = $book_rate->amount;
            $days = $book_rate->book->total_teaching_days;

            $daily_rate = round($amount / $days, 2);
            $total = $daily_rate * $attendance_count;

            $gross_salary += $total;

            //  فقط جمع شود
            $details[] = [
                'book_id' => $book_rate->book_id,
                'amount_snapshot' => $amount,
                'total_days_snapshot' => $days,
                'daily_rate_snapshot' => $daily_rate,
                'attendance_count' => $attendance_count,
                'total_salary' => $total,
            ];
        }

        return [
            'gross_salary' => $gross_salary,
            'details' => $details
        ];
    }


    public function savePayroll()
    {
        if (empty($this->selected_employees)) {
            $this->dispatch('alert', type: 'error', message: 'No employees selected');
            return;
        }

        DB::beginTransaction();

        try {

            [$start, $end] = jalaliToGregorianMonthRange($this->year, $this->month);

            $branch_id = Auth::user()->branch_id ?: $this->branch_id;

            $employees = $this->selected_employees;

            // =========================
            // attendances
            // =========================
            $attendances = TeacherAttendance::where('status', '!=', 'absent')
                ->whereIn('teacher_id', $employees->pluck('id'))
                ->whereHas('course', function ($q) use ($branch_id) {
                    $q->where('branch_id', $branch_id);
                })
                ->whereBetween('attendance_date', [$start, $end])
                ->with('course')
                ->get()
                ->groupBy('teacher_id');

            // =========================
            // advances
            // =========================
            $employee_advances = EmployeeSalaryAdvance::whereIn('employee_id', $employees->pluck('id'))
                ->where('branch_id', $branch_id)
                ->where('status', 'active')
                ->orderBy('created_at')
                ->get()
                ->groupBy('employee_id');

            foreach ($employees as $employee) {

                $contract = $employee->activeTemporaryContract
                    ->where('branch_id', $branch_id)
                    ->first();

                if (!$contract) {
                    continue;
                }

                // =========================
                // 1 GROSS SALARY
                // =========================
                $result = $this->calculateEmployeeBookSalary($employee, $attendances);

                $gross_salary = $result['gross_salary'];
                $details = $result['details'];

                // =========================
                // 2 DEDUCTIONS
                // =========================
                $tax = tax($gross_salary);

                $food_deduction = $contract->food_deduction ?? 0;

                // =========================
                // 3 ALLOWANCES
                // =========================
                $taxi_fare = $contract->taxi_fare ?? 0;

                $credit_card = $contract->credit_card ?? 0;

                $total_allowances = $taxi_fare + $credit_card;

                // =========================
                // salary after tax/allowance
                // advance باید از این مقدار کم شود
                // نه از gross salary
                // =========================
                $salary_after_tax = $gross_salary - $tax - $food_deduction + $total_allowances;

                // =========================
                // ADVANCE CALCULATION
                // فقط snapshot
                // =========================
                $advance_deduction = 0;

                $remaining_salary = $salary_after_tax;

                $advances = $employee_advances->get($employee->id, collect());

                foreach ($advances as $advance) {

                    if ($remaining_salary <= 0) {
                        break;
                    }

                    $real_remaining = $advance->remaining_amount;

                    if ($real_remaining <= 0) {
                        continue;
                    }

                    $deduct = min($real_remaining, $remaining_salary);

                    if ($deduct <= 0) {
                        break;
                    }

                    $advance_deduction += $deduct;

                    $remaining_salary -= $deduct;
                }

                // =========================
                // TOTAL DEDUCTIONS
                // =========================
                $total_deductions =
                    $tax +
                    $food_deduction +
                    $advance_deduction;

                // =========================
                // NET SALARY
                // =========================
                $net_salary = $salary_after_tax - $advance_deduction;

                if ($net_salary < 0) {
                    $net_salary = 0;
                }

                // =========================
                // SAVE PAYROLL
                // =========================
                $payroll = TemporaryPayroll::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'branch_id' => $branch_id,
                        'temporary_contract_id' => $contract->id,
                        'year' => $this->year,
                        'month_id' => $this->month,
                    ],
                    [
                        'gross_salary' => $gross_salary,

                        'tax' => $tax,

                        'food_deduction' => $food_deduction,

                        'taxi_fare' => $taxi_fare,
                        'credit_card' => $credit_card,

                        'total_allowances' => $total_allowances,

                        'advance_deduction' => $advance_deduction,

                        'total_deductions' => $total_deductions,

                        'net_salary' => $net_salary,

                        'status' => 'pending',

                        'user_id' => auth()->id(),
                    ]
                );

                // =========================
                // refresh details
                // =========================
                TemporaryPayrollDetail::where('temporary_payroll_id', $payroll->id)->delete();

                foreach ($details as $detail) {

                    TemporaryPayrollDetail::create([
                        'temporary_payroll_id' => $payroll->id,

                        'employee_id' => $employee->id,

                        'book_id' => $detail['book_id'],

                        'amount_snapshot' => $detail['amount_snapshot'],

                        'total_days_snapshot' => $detail['total_days_snapshot'],

                        'daily_rate_snapshot' => $detail['daily_rate_snapshot'],

                        'attendance_count' => $detail['attendance_count'],

                        'total_salary' => $detail['total_salary'],
                    ]);
                }
            }

            DB::commit();

            $this->selected_employees = null;

            $this->dispatch(
                'alert',
                type: 'success',
                message: __('label.successfully_done')
            );

        } catch (\Exception $e) {

            DB::rollBack();

            $this->dispatch(
                'alert',
                type: 'error',
                message: __('label.store_error') . ' : ' . $e->getMessage()
            );
        }
    }

   public function payPayroll()
    {
        if (empty($this->selected_employees)) {
            $this->dispatch('alert', type: 'error', message: 'No employees selected');
            return;
        }

        DB::beginTransaction();

        try {

            $branch_id = Auth::user()->branch_id ?: $this->branch_id;

            $employees = $this->selected_employees;

            foreach ($employees as $employee) {

                $payroll = TemporaryPayroll::where([
                    'employee_id' => $employee->id,
                    'branch_id' => $branch_id,
                    'year' => $this->year,
                    'month_id' => $this->month,
                ])->first();

                if (!$payroll || $payroll->status == 'paid') {
                    continue;
                }

                // =========================
                // CONTRACT
                // =========================
                $contract = TemporaryContract::find($payroll->temporary_contract_id);

                // =========================
                // ADVANCE PAYMENT
                // فقط همان مقدار snapshot شده در payroll
                // =========================
                $remaining_advance_deduction = $payroll->advance_deduction;

                if ($remaining_advance_deduction > 0) {

                    $advances = EmployeeSalaryAdvance::where('employee_id', $employee->id)
                        ->where('branch_id', $branch_id)
                        ->where('status', 'active')
                        ->orderBy('created_at')
                        ->get();

                    foreach ($advances as $advance) {

                        if ($remaining_advance_deduction <= 0) {
                            break;
                        }

                        $real_remaining = $advance->remaining_amount;

                        if ($real_remaining <= 0) {
                            continue;
                        }

                        // فقط همان مقدار payroll
                        $deduct = min($real_remaining, $remaining_advance_deduction);

                        if ($deduct <= 0) {
                            continue;
                        }

                        // ثبت payment
                        EmployeeSalaryAdvancePayment::create([
                            'employee_salary_advance_id' => $advance->id,
                            'employee_id' => $employee->id,
                            'month_id' => $this->month,
                            'year' => $this->year,
                            'amount' => $deduct,
                            'payment_date' => now(),
                        ]);

                        // update advance
                        $advance->remaining_amount -= $deduct;

                        if ($advance->remaining_amount <= 0) {

                            $advance->remaining_amount = 0;
                            $advance->status = 'completed';
                        }

                        $advance->save();

                        // ---------- start SALARY_ADVANCE_SETTLEMENT------------------
                        $account_id = Account::where('branch_id', $branch_id)
                            ->where('category', 'treasury')
                            ->value('id');

                        if (!$account_id) {

                            return $this->dispatch(
                                'alert',
                                type: 'error',
                                message: __('label.treasury_account_not_found')
                            );
                        }
                        TransactionService::income(
                            $account_id,
                            $branch_id,
                            $deduct,
                            TransactionCategory::SALARY_ADVANCE_SETTLEMENT,
                            'EmployeeSalaryAdvancePayment',
                            $payment->id,
                            $advance->section_id,
                            Action::CREATE
                        );
                        // --------------end SALARY_ADVANCE_SETTLEMENT------------------
                        
                        // کم شدن از snapshot payroll
                        $remaining_advance_deduction -= $deduct;
                    }
                }

                // =========================
                // TRANSACTION
                // فقط پول واقعی پرداخت‌شده
                // =========================
                if ($payroll->net_salary > 0) {
                    $account_id = Account::where('branch_id', $branch_id)
                        ->where('category', 'treasury')
                        ->value('id');

                    if (!$account_id) {

                        return $this->dispatch(
                            'alert',
                            type: 'error',
                            message: __('label.treasury_account_not_found')
                        );
                    }
                    TransactionService::expense(
                        $account_id,
                        $branch_id,
                        $payroll->net_salary,
                        TransactionCategory::TEMPORARY_SALARY_PAYMENT,
                        'TemporaryPayroll',
                        $payroll->id,
                        $contract?->section_id,
                        Action::CREATE
                    );
                }

                // =========================
                // UPDATE PAYROLL
                // =========================
                $payroll->update([
                    'status' => 'paid',
                    'paid_by' => auth()->id(),
                    'payment_date' => now(),
                ]);
            }

            DB::commit();

            $this->selected_employees = null;

            $this->dispatch(
                'alert',
                type: 'success',
                message: 'Payroll paid successfully'
            );

        } catch (\Exception $e) {

            DB::rollBack();

            $this->dispatch(
                'alert',
                type: 'error',
                message: $e->getMessage()
            );
        }
    }

    public function exportPdf()
    {
        
        $this->processMonthlyEmployeePayroll();
        if (!auth()->user()->branch_id && !in_array('branch_id', $this->selectedFields)) {
            $this->selectedFields[] = 'branch_id';
        }
        $month = Month::find($this->month);
        $year = $this->year;
        $pdf = Pdf::loadView(
            'livewire.hr.payrolls.temporary-payroll-list-pdf',
            [
                'selected_employees' => $this->selected_employees,
                'selectedFields' => $this->selectedFields,
                'year' =>$year,
                'month' =>$month,
            ]
        )->setPaper('a4',$this->pdfOrientation)
        ->setOption('defaultFont', 'dejavusans');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            __('label.temporary_teachers_payroll').'-' . Carbon::now()->format('Y-m-d -H-i-A') . '.pdf'
        );
    }
}
