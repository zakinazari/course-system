<?php

namespace App\Livewire\Hr\Employees\Attendance;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\CenterSettings\Branch;
use App\Models\Hr\Employee;
use App\Models\Hr\PermanentContract;
use App\Models\Hr\EmployeeAttendance;
use App\Models\Hr\EmployeeLeave;
use App\Models\Hr\LeaveType;
use App\Models\Hr\Position;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Auth;
use Carbon\Carbon;
use DB;
class EmployeeAttendanceList extends Component
{
     // -------start generals--------------------
    use WithPagination;
    use WithFileUploads;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'employee-attnedance-list-addEditModal';
    public $table_name='employee_attendances';
    
    public $selectedFields = [
        'no',
        'employee_id',
        'position_id',
        'attendance_status',
        'status',
    ];
    public $pdfOrientation ='landscape';
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
        $this->loadAttendances();
    }
    
    // ---------------------------------end generals-------------

    public $branches=[];
    public $employees=[]; 
    public $positions=[]; 
    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------
        $this->branches =  Branch::all();
         $branch_id = auth()->user()->branch_id ?: $this->branch_id;
        $this->employees =  PermanentContract::with('employee:id,name,last_name,branch_id,employee_code','branch')
            ->where('status', 'active')
            ->where('branch_id', $branch_id)
            ->get()
            ->unique('employee_id')
            ->values();

        $this->positions =  Position::all();
        $this->attendance_date = now()->toDateString();
    }

    public
        $branch_id,
        $employee_id,
        $position_id,
        $attendance_date,
        $status;
    
     public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'branches',
            'positions',
            'employees',
        ]);
    }
    public $search = [
            'name' => null,
            'branch_id' => null,
            'employee_id' => null,
            'status' => null,
        ];

    public $selected_employees = [];
    public $attendances = [];
    public $existing_attendances = [];
    public $employee_leaves = [];
    public function render()
    {
        return view('livewire.hr.employees.attendance.employee-attendance-list');
    }

    protected function rules()
    {
        $rules =  [
            'branch_id' => 'required',

        ];
        return $rules;
    }

    public function loadAttendances()
    {
        $this->selected_employees = collect();
        $this->attendances = [];
        $this->existing_attendances = [];
        $this->employee_leaves = [];
        $date = $this->attendance_date ?? now()->toDateString();
        if (!$this->attendance_date) {
            $this->selected_employees = collect();
            return;
        }

        $branch_id = auth()->user()->branch_id ?: $this->branch_id;

        $this->selected_employees = PermanentContract::with('employee:id,name,last_name,branch_id,employee_code')
            ->where('status','active')
            ->where('branch_id', $branch_id)
            ->when($this->position_id, fn($q) =>
                $q->where('position_id', $this->position_id)
            )
            ->when($this->employee_id, fn($q) =>
                $q->where('employee_id', $this->employee_id)
            )
            ->get()
            ->unique('employee_id')
            ->values();

        // load existing attendance
        $existing = EmployeeAttendance::where('attendance_date', $this->attendance_date)
            ->when($branch_id, fn($q) => $q->where('branch_id', $branch_id))
            ->get()
            ->keyBy('employee_id');

        $leaves = EmployeeLeave::with('leaveType')
        ->where('status', 'approved')
        ->whereDate('start_date', '<=', $date)
        ->whereDate('end_date', '>=', $date)
        ->get()
        ->keyBy('employee_id');
        
        foreach ($this->selected_employees as $i=> $emp) {

            $employee_id = $emp->employee?->id;

            if (isset($leaves[$employee_id])) {

                $this->employee_leaves[$employee_id] = $leaves[$employee_id];

            } else {

                $this->employee_leaves[$employee_id] = null;

            }


            if (isset($existing[$employee_id])) {

                $this->attendances[$employee_id] = $existing[$employee_id]->status;

            } elseif (isset($leaves[$employee_id])) {

                $this->attendances[$employee_id] = 'leave';

            } else {

                $this->attendances[$employee_id] = 'present';

            }
        }
    }

    public function updatedAttendanceDate()
    {
        $this->loadAttendances();
    }

    

    public function updatedBranchId()
    {
        $branch_id = auth()->user()->branch_id ?: $this->branch_id;
        $this->employees =  PermanentContract::with('employee:id,name,last_name,branch_id,employee_code','branch')
            ->where('status', 'active')
            ->where('branch_id', $branch_id)
            ->when(!empty($this->position_id), function($q){
                $q->where('position_id',$this->position_id);
            })
            ->get()
            ->unique('employee_id') 
            ->values();
        
        $this->loadAttendances();
    }

    public function updatedPositionId()
    {
        $branch_id = auth()->user()->branch_id ?: $this->branch_id;
        $this->employees =  PermanentContract::with('employee:id,name,last_name,branch_id,employee_code','branch')
            ->where('status', 'active')
            ->where('branch_id', $branch_id)
            ->when(!empty($this->position_id), function($q){
                $q->where('position_id',$this->position_id);
            })
            ->get()
            ->unique('employee_id') 
            ->values();
        
        $this->loadAttendances();
    }

    // public function updatedEmployeeId()
    // {
    //     $this->loadAttendances();
    // }

    public function saveAttendance()
    {
        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        DB::beginTransaction();

        try {

            $branch_id = auth()->user()->branch_id ?: $this->branch_id;


                foreach ($this->attendances as $employee_id => $status) {


                    // ==========================
                    // Check Employee Leave
                    // ==========================

                    $leave = EmployeeLeave::where('employee_id', $employee_id)
                        ->where('status', 'approved')
                        ->whereDate('start_date', '<=', $this->attendance_date)
                        ->whereDate('end_date', '>=', $this->attendance_date)
                        ->first();


                    $leave_type_id = null;


                    if ($leave) {

                        $status = 'leave';

                        $leave_type_id = $leave->leave_type_id;

                    }

                    EmployeeAttendance::updateOrCreate(

                        [
                            'employee_id' => $employee_id,

                            'branch_id' => $branch_id,

                            'attendance_date' => $this->attendance_date,
                        ],

                        [
                            'status' => $status,

                            'leave_type_id' => $leave_type_id,

                            'user_id' => auth()->id(),
                        ]

                    );

                }

            DB::commit();

            $this->selected_employees = collect();
            $this->attendances = [];
            $this->existing_attendances = [];

            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.store_error') . ': ' . $e->getMessage());
        }
    }

    public function exportPdf()
    {
        
        $this->loadAttendances();
        if (!auth()->user()->branch_id && !in_array('branch_id', $this->selectedFields)) {
            $this->selectedFields[] = 'branch_id';
        }
        $pdf = Pdf::loadView(
            'livewire.hr.employees.attendance.employee-attendance-list-pdf',
            [
                'selected_employees' => $this->selected_employees,
                'attendances' => $this->attendances,
                'existing_attendances' => $this->existing_attendances,
                'selectedFields' => $this->selectedFields,
                'attendance_date' =>$this->attendance_date,
            ]
        )->setPaper('a4',$this->pdfOrientation)
        ->setOption('defaultFont', 'dejavusans');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            __('label.employee_attendance').'-' . Carbon::now()->format('Y-m-d -H-i-A') . '.pdf'
        );
    }
}
