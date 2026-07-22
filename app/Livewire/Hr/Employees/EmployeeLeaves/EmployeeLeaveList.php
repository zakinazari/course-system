<?php

namespace App\Livewire\Hr\Employees\EmployeeLeaves;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\Hr\Employee;
use App\Models\Hr\PermanentContract;
use App\Models\Hr\TemporaryContract;
use App\Models\Hr\LeaveType;
use App\Models\Hr\EmployeeLeave;

use Auth;
use Carbon\Carbon;
use DB;

class EmployeeLeaveList extends Component
{
     // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'employee-live-list-addEditModal';
    public $billModalId = '';
    public $table_name='employee_leaves';
    public $selectedFields = [];
    public $pdfOrientation = 'landscape';
    public $branches=[];

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
    public $employee;
    public $employee_id;
    public $leave_types =[];
    public function mount($active_menu_id = null, $employee_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->employee_id = $employee_id;
        $this->employee =Employee::findOrFail($employee_id);

        $this->leave_types = LeaveType::where('status',true)->get();
    }

    public  $leave_id;
    public $leave_type_id;
    public $contract_type = null;

    public $contract_id = null;

    public $contracts = []; 

    public $start_date;

    public $end_date;

    public $days;

    public $status = 'approved';

    public $reason;

    public $note;

    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'branches',
            'employee',
            'employee_id',
            'leave_types',
        ]);
    }

    public $search = [
            'leave_type' => null,
        ];


    public function render()
    {
        $search = $this->search;
        $employee_leaves = EmployeeLeave::with('leaveType','contract.position','contract.branch')
            ->where('employee_id',$this->employee_id)
            ->when($search['leave_type'],function($q) use($search){
                $q->whereHas('leaveType',function($query) use($search){
                    $query->where('id',$search['leave_type']);
                });
            })
            ->orderBy('id','desc')
            ->paginate($this->perPage);

        return view('livewire.hr.employees.employee-leaves.employee-leave-list',compact('employee_leaves'));
    }

    
    protected function rules()
    {
        $rules= [

            'employee_id' => 'required|exists:employees,id',

            'leave_type_id' => 'required|exists:leave_types,id',

            'contract_type' => 'required',
            'contract_id' => 'required',

            'start_date' => 'required|date',

            'end_date' => 'required|date|after_or_equal:start_date',

            'days' => 'required|numeric|min:0.5',

            'status' => 'required|in:pending,approved,rejected,cancelled',

            'reason' => 'nullable|string',

            'note' => 'nullable|string',
    ];

        return $rules;
    }
    // Localized messages
    protected function messages()
    {
        return [
            'employee_id.required' => __('label.employee_id.required'),
            'leave_type_id.required' => __('label.leave_type.required'),
            'contract_type.required' => __('label.contract_type.required'),
            'contract_id.required' => __('label.contract.required'),
            'start_date.required' => __('label.start_date.required'),
            'end_date.required' => __('label.end_date.required'),

        ];
    }

    public function updatedStartDate()
    {
        $this->calculateLeaveDays();
    }

    public function updatedEndDate()
    {
        $this->calculateLeaveDays();
    }


    private function calculateLeaveDays()
    {
        if ($this->start_date && $this->end_date) {

            $start = Carbon::parse($this->start_date);

            $end = Carbon::parse($this->end_date);

            $this->days = $start->diffInDays($end) + 1;
        }
    }

    public function updatedContractType($value)
    {
        $this->contract_id = null;

        $this->contracts = [];

        if (!$value || !$this->employee_id) {
            return;
        }


        if ($value == 'permanent') {

            $this->contracts = PermanentContract::with('position','branch')->where('employee_id', $this->employee_id)
                ->orderByDesc('id')
                ->get();

        }


        if ($value == 'temporary') {

            $this->contracts = TemporaryContract::with('position')->where('employee_id', $this->employee_id)
                ->orderByDesc('id')
                ->get();

        }
    }

    public function store()
    {
        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {

            return $this->dispatch(
                'alert',
                type: 'error',
                message: __('label.permission_message')
            );
        }


        $this->validate();


        DB::beginTransaction();

        try {


            // =========================
            // Get Contract
            // =========================

            if ($this->contract_type == 'permanent') {

                $contract = PermanentContract::where('id', $this->contract_id)
                    ->where('employee_id', $this->employee_id)
                    ->first();

            } elseif ($this->contract_type == 'temporary') {

                $contract = TemporaryContract::where('id', $this->contract_id)
                    ->where('employee_id', $this->employee_id)
                    ->first();

            } else {

                throw new \Exception(
                    __('label.contract_required')
                );
            }


            if (!$contract) {

                throw new \Exception(
                    __('label.contract_not_found')
                );
            }



            // =========================
            // Leave Type Limit Check
            // =========================

            $leaveType = LeaveType::find($this->leave_type_id);


            if (!$leaveType) {

                throw new \Exception(
                    __('label.leave_type_not_found')
                );
            }


            if (!is_null($leaveType->yearly_limit)) {


                $year = Carbon::parse($this->start_date)->year;


                $usedDays = EmployeeLeave::where('employee_id', $this->employee_id)

                    ->where('leave_type_id', $this->leave_type_id)

                    ->whereYear('start_date', $year)

                    ->whereIn('status', [
                        'pending',
                        'approved'
                    ])

                    ->sum('days');



                $availableDays = $leaveType->yearly_limit - $usedDays;



                if ($this->days > $availableDays) {


                    throw new \Exception(
                        __('label.leave_limit_exceeded', ['days' => $availableDays])
                    );

                }

            }


            // =========================
            // Check Date Overlap
            // =========================

            $overlap = EmployeeLeave::where('employee_id', $this->employee_id)
                ->where('contract_id',$contract->id)
                ->where('contract_type',$this->contract_type)
                ->whereIn('status', [
                    'pending',
                    'approved'
                ])

                ->where(function ($query) {

                    $query->whereBetween('start_date', [
                            $this->start_date,
                            $this->end_date
                        ])

                        ->orWhereBetween('end_date', [
                            $this->start_date,
                            $this->end_date
                        ])

                        ->orWhere(function ($q) {

                            $q->where('start_date', '<=', $this->start_date)
                            ->where('end_date', '>=', $this->end_date);

                        });

                })

                ->exists();


            if ($overlap) {

                throw new \Exception(
                    __('label.employee_already_has_leave')
                );

            }



            // =========================
            // Create Leave
            // =========================

            $leave = $contract->leaves()->create([

                'employee_id' => $this->employee_id,

                'leave_type_id' => $this->leave_type_id,

                'start_date' => $this->start_date,

                'end_date' => $this->end_date,

                'days' => $this->days,

                'status' => 'approved',

                'reason' => $this->reason,

                'note' => $this->note,

                'user_id' => Auth::id(),

            ]);




            // =========================
            // System Log
            // =========================

            SystemLog::create([

                's_id' => $this->employee_id,

                'user_id' => Auth::id(),

                'section' => __('label.employee_leave'),

                'type_id' => 2,

            ]);



            DB::commit();

            $this->closeModal();

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
                message: $e->getMessage()
            );

        }
    }



    public function edit($id)
    {
        $this->resetValidation();

        $leave = EmployeeLeave::findOrFail($id);

        $this->leave_id = $leave->id;

        $this->employee_id = $leave->employee_id;

        $this->leave_type_id = $leave->leave_type_id;

        $this->contract_type = $leave->contract_type;


        // load contracts based on type
        $this->updatedContractType($this->contract_type);


        // after contracts loaded
        $this->contract_id = $leave->contract_id;


        $this->start_date = $leave->start_date->format('Y-m-d');

        $this->end_date = $leave->end_date->format('Y-m-d');

        $this->days = $leave->days;

        $this->status = $leave->status;

        $this->reason = $leave->reason;

        $this->note = $leave->note;


        $this->editMode = true;

        $this->dispatch('open-modal', id: $this->modalId);
    }

   public function update()
    {
        if (!edit(Auth::user()->role_ids, $this->active_menu_id)) {

            return $this->dispatch(
                'alert',
                type: 'error',
                message: __('label.permission_message')
            );
        }


        $this->validate();


        DB::beginTransaction();

        try {


            $leave = EmployeeLeave::findOrFail($this->leave_id);



            // =========================
            // Get Contract
            // =========================

            if ($this->contract_type == 'permanent') {

                $contract = PermanentContract::where('id', $this->contract_id)
                    ->where('employee_id', $this->employee_id)
                    ->first();

            } else {

                $contract = TemporaryContract::where('id', $this->contract_id)
                    ->where('employee_id', $this->employee_id)
                    ->first();

            }


            if (!$contract) {

                throw new \Exception(
                    __('label.contract_not_found')
                );
            }



            // =========================
            // Check Overlap
            // =========================

            $exists = EmployeeLeave::where('employee_id', $this->employee_id)

                ->where('id', '!=', $this->leave_id)

                ->whereIn('status', [
                    'pending',
                    'approved'
                ])

                ->where(function ($query) {

                    $query->whereBetween('start_date', [
                        $this->start_date,
                        $this->end_date
                    ])

                    ->orWhereBetween('end_date', [
                        $this->start_date,
                        $this->end_date
                    ])

                    ->orWhere(function ($q) {

                        $q->where('start_date', '<=', $this->start_date)
                        ->where('end_date', '>=', $this->end_date);

                    });

                })

                ->exists();



            if ($exists) {

                throw new \Exception(
                    __('label.employee_already_has_leave')
                );

            }



            // =========================
            // Update
            // =========================

            $leave->update([

                'employee_id' => $this->employee_id,

                'leave_type_id' => $this->leave_type_id,

                'contract_id' => $contract->id,

                'contract_type' => $this->contract_type,

                'start_date' => $this->start_date,

                'end_date' => $this->end_date,

                'days' => $this->days,

                'status' => $this->status,

                'reason' => $this->reason,

                'note' => $this->note,

            ]);



            SystemLog::create([

                's_id' => $this->employee_id,

                'user_id' => Auth::id(),

                'section' => 
                    __('label.employee_leave') .
                    ' | ' .
                    __('label.leave_type') . ': ' . $leave->leaveType?->name .
                    ' | ' .
                    __('label.days') . ': ' . $this->days .
                    ' | ' .
                    __('label.date') . ': ' . $this->start_date . ' - ' . $this->end_date .
                    ' | ' .
                    __('label.status') . ': ' . $this->status,

                'type_id' => 3,

            ]);



            DB::commit();


            $this->closeModal();


            $this->dispatch(
                'alert',
                type:'success',
                message: __('label.successfully_done')
            );


        } catch (\Exception $e) {


            DB::rollBack();


            $this->dispatch(
                'alert',
                type:'error',
                message:$e->getMessage()
            );
        }
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
            $leave = EmployeeLeave::findOrFail($id);

            SystemLog::create([

                's_id' => $leave->employee_id,

                'user_id' => Auth::id(),

                'section' => 
                    __('label.employee_leave') .
                    ' | ' .
                    __('label.leave_type') . ': ' . $leave->leaveType?->name .
                    ' | ' .
                    __('label.days') . ': ' . $leave->days .
                    ' | ' .
                    __('label.period') . ': ' . $leave->start_date->format('Y-m-d') 
                    . ' - ' . 
                    $leave->end_date->format('Y-m-d') .
                    ' | ID: ' . $leave->id,

                'type_id' => 4,

            ]);


            $leave->delete();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }
}
