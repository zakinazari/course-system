<?php

namespace App\Livewire\Hr\Payrolls;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\CenterSettings\Branch;
use App\Models\Hr\Position;
use App\Models\Hr\Employee;
use App\Models\Hr\EmployeeAttendance;
use App\Models\Hr\TemporaryContract;
use App\Models\Hr\EmployeeSalaryAdvance;
use App\Models\Hr\EmployeeSalaryAdvancePayment;
use App\Models\Hr\PermanentPayroll;

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
class PermanentPayrollList extends Component
{
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'permanent-payroll-list-addEditModal';
    public $table_name='permanent_payrolls';
    public $pdfOrientation = 'landscape';

    public $selectedFields = [
        'no',
        'employee_id',
        'status',
        'gross_salary',
        'total_present_days',
        'taxi_fare',
        'credit_card',
        'tax',
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

        $this->employees =  Employee::whereHas('activePermanentContract')
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
        return view('livewire.hr.payrolls.permanent-payroll-list',compact('selected_employees'));
    }

    protected function rules()
    {
        $rules = [
            'year'   => 'required|exists:years,year',
            'month'  => 'required|exists:months,number',
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
            $this->employees = Employee::whereHas('activePermanentContract', function ($q) use($branch_id) {
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
        
        if ($this->branch_id) {
            $this->employees = Employee::whereHas('activePermanentContract', function ($q) {
                    $q->where('branch_id', $this->branch_id);
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
    public $salaries = [];

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

   
        $employees = Employee::whereHas('activePermanentContract', function ($q) use($branch_id) {

                $q->where('branch_id', $branch_id);
                if ($this->position_id) {
                    $q->where('position_id', $this->position_id);
                }
            })
        ->with([
            'branch',
            'activePermanentContract' => function ($q) use ($branch_id) {
                $q->where('branch_id', $branch_id);
            },
            'permanentPayrolls' => function ($q) use($branch_id){
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
           $employee->payroll = $employee->permanentPayrolls->first();
        }
    
        $this->selected_employees = $employees;
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

            // preload data (performance)
            $teacher_attendances = TeacherAttendance::where('status', 'present')
                ->whereIn('teacher_id', $employees->pluck('id'))
                ->whereBetween('attendance_date', [$start, $end])
                ->with('course.time')
                ->get()
                ->groupBy('teacher_id');

            $attendances = EmployeeAttendance::where('status', '!=', 'absent')
                ->whereIn('employee_id', $employees->pluck('id'))
                ->where('branch_id', $branch_id)
                ->whereBetween('attendance_date', [$start, $end])
                ->get()
                ->groupBy('employee_id');

            $employee_advances = EmployeeSalaryAdvance::whereIn('employee_id', $employees->pluck('id'))
                ->where('status', 'active')
                ->where('branch_id', $branch_id)
                ->orderBy('created_at')
                ->get()
                ->groupBy('employee_id');

            foreach ($employees as $employee) {

                $contract = $employee->activePermanentContract()
                    ->where('branch_id', $branch_id)
                    ->first();

                if (!$contract) continue;

                // =======================
                // GROSS SALARY
                // =======================
                $gross_salary = $this->calculateEmployeeSalary($employee, $attendances);

                $present_days = $attendances->get($employee->id, collect())->count();

                // =======================
                // OVERTIME
                // =======================
                $overtime_hours = 0;
                $overtime_amount = 0;

                $teacher_records = $teacher_attendances->get($employee->id, collect());

                if ($teacher_records->isNotEmpty()) {

                    $grouped = $teacher_records->groupBy('attendance_date');

                    foreach ($grouped as $records) {

                        $daily = 0;

                        foreach ($records as $att) {
                            if (!$att->course?->time) continue;

                            $startTime = $att->course->time->start_time;
                            $endTime = $att->course->time->end_time;

                            $daily += $startTime->diffInMinutes($endTime) / 60;
                        }

                        if ($daily > 8) {
                            $overtime_hours += ($daily - 8);
                        }
                    }

                    if ($overtime_hours > 0) {
                        $rate = $contract->basic_salary / 208;
                        $overtime_amount = $overtime_hours * $rate;
                        $gross_salary += $overtime_amount;
                    }
                }

                // =======================
                // TAX + ALLOWANCES
                // =======================
                $tax = tax($gross_salary);

                $allowances =
                    ($contract->taxi_fare ?? 0) +
                    ($contract->credit_card ?? 0);

                $total_salary = $gross_salary - $tax + $allowances;

                // =======================
                // ADVANCE (SAFE SNAPSHOT LOGIC)
                // =======================
                $advances = $employee_advances->get($employee->id, collect());

                $advance_deduction = 0;
                $remaining_salary = $total_salary;

                foreach ($advances as $advance) {

                    $paid = EmployeeSalaryAdvancePayment::where('employee_salary_advance_id', $advance->id)
                        ->where('employee_id', $employee->id)
                        ->sum('amount');

                    $remaining_advance = $advance->total_amount - $paid;

                    if ($remaining_advance <= 0) continue;

                    $deduct = min($remaining_advance, $remaining_salary);

                    if ($deduct <= 0) break;

                    $advance_deduction += $deduct;
                    $remaining_salary -= $deduct;
                }

                // =======================
                // NET SALARY
                // =======================
                $net_salary = $total_salary - $advance_deduction;

                // =======================
                // SNAPSHOT SAVE
                // =======================
                PermanentPayroll::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'branch_id' => $branch_id,
                        'year' => $this->year,
                        'month_id' => $this->month,
                    ],
                    [
                        'permanent_contract_id' => $contract->id,

                        'gross_salary' => $gross_salary,
                        'tax' => $tax,

                        'taxi_fare' => $contract->taxi_fare ?? 0,
                        'credit_card' => $contract->credit_card ?? 0,

                        'total_allowances' => $allowances,

                        'advance_deduction' => $advance_deduction,
                        'total_deductions' => $tax + $advance_deduction,

                        'net_salary' => $net_salary,

                        'status' => 'pending',
                        'user_id' => auth()->id(),
                    ]
                );
            }

            DB::commit();

            $this->dispatch('alert', type: 'success', message: 'Payroll saved successfully');

        } catch (\Exception $e) {

            DB::rollBack();

            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    public function calculateEmployeeSalary($employee, $attendances)
    {
        $branch_id = Auth::user()->branch_id ?: $this->branch_id;
        $contract = $employee->activePermanentContract()
                    ->where('branch_id', $branch_id)
                    ->first();
        $gross_salary = 0;
        $attendance_count = $attendances->get($employee->id, collect())->count();

        $daily_rate =  round($contract->basic_salary / 26, 2);//26 days===> 26 روز ماه، یعنی یکماه جمعه ها کشیده،
        $gross_salary = $daily_rate * $attendance_count;

        return $gross_salary;
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

            foreach ($this->selected_employees as $employee) {
                $contract = $employee->activePermanentContract()
                    ->where('branch_id', $branch_id)
                    ->first();

                $payroll = PermanentPayroll::where([
                    'employee_id' => $employee->id,
                    'branch_id' => $branch_id,
                    'year' => $this->year,
                    'month_id' => $this->month,
                ])->first();

                if (!$payroll || $payroll->status === 'paid') continue;

                // =========================
                // 1. PAY SALARY (NO RECALC)
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
                        TransactionCategory::PERMANENT_SALARY_PAYMENT,
                        'PermanentPayroll',
                        $payroll->id,
                        $contract->section_id,
                        Action::CREATE
                    );
                }
                // =========================
                // 2. APPLY ADVANCE DEDUCTIONS (FROM SNAPSHOT LOGIC)
                // =========================
                $remaining_to_deduct = $payroll->advance_deduction;

                if ($remaining_to_deduct > 0) {

                    $advances = EmployeeSalaryAdvance::where('employee_id', $employee->id)
                        ->where('branch_id', $branch_id)
                        ->where('status', 'active')
                        ->orderBy('created_at')
                        ->get();

                    foreach ($advances as $advance) {

                        if ($remaining_to_deduct <= 0) break;

                        $remaining = $advance->remaining_amount;

                        if ($remaining <= 0) continue;

                        $deduct = min($remaining, $remaining_to_deduct);

                        EmployeeSalaryAdvancePayment::create([
                            'employee_salary_advance_id' => $advance->id,
                            'employee_id' => $employee->id,
                            'amount' => $deduct,
                            'month_id' => $this->month,
                            'year' => $this->year,
                            'payment_date' => now(),
                        ]);

                        $advance->remaining_amount -= $deduct;

                        if ($advance->remaining_amount <= 0) {
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

                        $remaining_to_deduct -= $deduct;
                    }

                }

                // =========================
                // 3. LOCK PAYROLL
                // =========================
                $payroll->update([
                    'status' => 'paid',
                    'paid_by' => auth()->id(),
                    'payment_date' => now(),
                ]);
            }

            DB::commit();

            $this->dispatch('alert', type: 'success', message: 'Payroll paid successfully');

        } catch (\Exception $e) {

            DB::rollBack();

            $this->dispatch('alert', type: 'error', message: $e->getMessage());
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
            'livewire.hr.payrolls.permanent-payroll-list-pdf',
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
            __('label.permanent_teachers_payroll').'-' . Carbon::now()->format('Y-m-d -H-i-A') . '.pdf'
        );
    }
}
