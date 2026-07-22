<?php

namespace App\Livewire\Hr\Employees\PersonalPayrolls;

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
use App\Models\Hr\TemporaryPayroll;

use App\Models\CenterSettings\Year;
use App\Models\CenterSettings\Month;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Auth;
use DB;
class EmployeeTemporaryPayrollList extends Component
{
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId;
    public $table_name='temporary_payrolls';
    public $pdfOrientation = 'landscape';

    public $selectedFields = [
        'no',
        'employee_id',
        'status',
        'gross_salary',
        'absent_days',
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

    }
    
    // ---------------------------------end generals-------------
    public $years = [];
    public $months = [];

    public $positions = [];
    public $employee;
    public $branches = [];
    public function mount($active_menu_id = null,$employee_id= null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->employee_id = $employee_id;
        $this->employee =  Employee::find($this->employee_id);
       
        $this->positions =  Position::all();
        $this->years =  Year::orderBy('year','desc')->get();
        $this->months =  Month::all();
        $this->branches =  Branch::all();

         $this->modalId= 'employee-temporary-payroll-list-addEditModal';
    }

    public $position_id;
    public $employee_id;
    public $branch_id;

    public $year;
    public $month;
    public $payroll_id;
    public $status;
    public $note;
    public $attendance_date;
    
  
    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'employee',
            'employee_id',
            'years',
            'months',
            'branches',
        ]);
    }
    public $search = [

            'employee_id' => null,
            'year' => null,
            'month' => null,
            'to_date' => null,
            'status' => null,
        ];

    public function render()
    {
        $search = $this->search;
        $payrolls = TemporaryPayroll::with('month')
        ->where('employee_id', $this->employee_id)
        ->when($search['year'],function($q) use($search){

            $q->where('year',$search['year']);
        })
        ->when($search['month'],function($q) use($search){

            $q->where('month_id',$search['month']);
        })
        ->orderByDesc('month_id')
        ->paginate($this->perPage);

        return view('livewire.hr.employees.personal-payrolls.employee-temporary-payroll-list',compact('payrolls'));
    }

    protected function rules()
    {
        return [

            'payroll_id' => 'required',
            'status' => 'required',
        ];
    }
    // Localized messages
    protected function messages()
    {
        return [
            'payroll_id.required' => __('label.payroll_id.required'),
            'status.required' => __('label.status.required'),
        ];
    }
  
    public $show_payroll_details= [];
    public function showPairollDetails($id)
    {
        $this->show_payroll_details = TemporaryPayroll::with('employee:id,name,last_name,employee_code','month','details.book')->find($id);

        $this->dispatch('open-modal', id: $this->modalId);
    }
 
    
    public function handleGlobalDelete($payload)
    {

        if (!isset($payload['table']) || $payload['table'] !== $this->table_name) {
            return;
        }

        $this->delete($payload['id']);
    }

    public function delete($id)
    {
        if(!delete(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        try {
            $payroll = TemporaryPayroll::findOrFail($id);

            
            if ($payroll->status === 'paid') {
                return $this->dispatch(
                    'alert',
                    type: 'warning',
                    message: __('label.cannot_delete_paid_record')
                );
            }

             // ---start system log-----------
            SystemLog::create([
                's_id' => $payroll->employee_id,
                'user_id' => Auth::id(),
                'section' => __(
                    'label.temporary_payroll'
                ) .
                ' | Employee: ' . $payroll->employee?->name .
                ' | Employee ID: ' . $payroll->employee->employee_code .
                ' | Payroll ID: ' . $payroll->id,
                'type_id' => 4,
            ]);
            // ---end system log-------------
            $payroll->delete();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }

    public function print()
    {
        
        $this->dispatch('show-print-preview');
    }

}
