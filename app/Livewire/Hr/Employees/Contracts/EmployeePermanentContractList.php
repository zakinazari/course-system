<?php

namespace App\Livewire\Hr\Employees\Contracts;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\Hr\Position;
use App\Models\Hr\Employee;
use App\Models\CenterSettings\Branch;
use App\Models\Hr\PermanentContract;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Auth;
use DB;
class EmployeePermanentContractList extends Component
{
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'permanent-contract-list-addEditModal';
    public $table_name='permanent_contracts';
    public $pdfOrientation = 'landscape';

    public $selectedFields = [
        'no',
        'employee_id',
        'position_id',
        'basic_salary',
        'taxi_fare',
        'credit_card',
        'food_allowance',
        'start_date',
        'end_date',
        'status',
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
    public $positions = [];
    public $employees = [];
    public $branches = [];
    public function mount($active_menu_id = null,$employee_id)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->employee_id = $employee_id;
        $this->positions =  Position::all();
        $this->branches =  Branch::all();
    }

    public $position_id;
    public $employee_id;
    public $branch_id;

    public $contract_id;
    public $start_date;
    public $end_date;
    public $basic_salary;
    public $taxi_fare=0;
    public $credit_card=0;
    public $food_allowance=0;
    public $status='active';

    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'positions',
            'employees',
            'employee_id',
            'branches',
        ]);
    }
    public $search = [
            'employee_id' => null,
            'position_id' => null,
            'from_date' => null,
            'to_date' => null,
            'status' => null,
            'branch_id' => null,
        ];

    public function render()
    {
        $search = $this->search;
        $contracts = PermanentContract::with('position','branch')
        ->where('employee_id',$this->employee_id)
        ->when(!empty($this->search['position_id']), function ($query) {
            $query->whereHas('position', function($q) {
                $q->where('id', $this->search['position_id']);
            });
        })
        ->when(!empty($this->search['start_date']) && !empty($this->search['end_date']), function($q){

            $q->where('start_date','>=', $this->search['start_date']);
            $q->where('end_date','<=', $this->search['end_date']);
        })
        ->when(!empty($this->search['status']), function($q){

            $q->where('status', $this->search['status']);
        })
        ->when(!empty($this->search['branch_id']), function ($query) {
            $query->where('branch_id',$this->search['branch_id']);
        })
        ->orderBy('id','desc')
        ->paginate($this->perPage);

        return view('livewire.hr.employees.contracts.employee-permanent-contract-list',compact('contracts'));
    }

    protected function rules()
    {
        $branch_id = Auth::user()->branch_id ?: $this->branch_id;
        $rules = [
            'employee_id' => [
                'required',
                'exists:employees,id',

                Rule::unique('permanent_contracts')
                    ->ignore($this->contract_id)
                    ->where(function ($q) use ($branch_id) {
                        return $q->where('branch_id', $branch_id)
                            ->where('status', 'active');
                    }),
            ],

            'position_id' => 'required|exists:positions,id',
            'basic_salary' => 'required|numeric|min:1',

            'start_date' => 'required|date',

            'end_date' => 'required|date|after_or_equal:start_date',
        ];

        if (!Auth::user()->branch_id) {
            $rules['branch_id'] = 'required';
        }

        return $rules;
    }

    public function updatedBranchId()
    {
        $this->resetErrorBag('employee_id');
    }
    // Localized messages
    protected function messages()
    {
        return [
            'basic_salary.required' => __('label.basic_salary.required'),
            'start_date.required' => __('label.start_date.required'),
            'end_date.required' => __('label.end_date.required'),
            'employee_id.unique' => 'This employee already has an active contract in this branch.',
        ];
    }

    public function store()
    {
        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        $this->validate();

        DB::beginTransaction();

        try {

            $contract = PermanentContract::create([
                'position_id'  => $this->position_id,
                'employee_id'  => $this->employee_id,
                'branch_id'  => Auth::user()->branch_id ?: $this->branch_id,
                'basic_salary' => $this->basic_salary,
                'taxi_fare' => $this->taxi_fare,
                'credit_card' => $this->credit_card,
                'start_date'   => $this->start_date,
                'end_date'     => $this->end_date,
                'status'     => $this->status,
            ]);

            // // ---start system log-----------
            SystemLog::create([
                's_id' => $this->employee_id,
                'user_id' => Auth::user()->id,
                'section' => __('label.permanent_contract').'ID:'.$contract->id.')',
                'type_id' => 2,
            ]);
            // // ---end system log-------------
            DB::commit();

            $this->closeModal();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));

        } catch (\Exception $e) {
           
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->resetValidation(); 

        $contract = PermanentContract::findOrFail($id);

        $this->contract_id = $contract->id;
        $this->employee_id = $contract->employee_id;
        $this->position_id = $contract->position_id;
        $this->branch_id = $contract->branch_id;
        $this->basic_salary = $contract->basic_salary;
        $this->taxi_fare = $contract->taxi_fare;
        $this->credit_card = $contract->credit_card;
        $this->start_date = $contract->start_date? $contract->start_date->format('Y-m-d') : null; 
        $this->end_date = $contract->end_date ? $contract->end_date->format('Y-m-d')
        : null;
         $this->status = $contract->status;
        $this->editMode = true;

        $this->dispatch('open-modal', id: $this->modalId);
    }

    public function update()
    {
        if (!edit(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        $this->validate();

        DB::beginTransaction();

        try {

            $contract = PermanentContract::findOrFail($this->contract_id);

            $contract->update([
                'position_id'  => $this->position_id,
                'employee_id'  => $this->employee_id,
                'branch_id' =>  Auth::user()->branch_id ?: $this->branch_id,
                'basic_salary' => $this->basic_salary,
                'taxi_fare' => $this->taxi_fare,
                'credit_card' => $this->credit_card,
                'start_date'   => $this->start_date,
                'end_date'     => $this->end_date,
                 'status'     => $this->status,
            ]);

            // // ---start system log-----------
            SystemLog::create([
                's_id' => $this->employee_id,
                'user_id' => Auth::user()->id,
                'section' => __('label.permanent_contract').'ID:'.$contract->id.')',
                'type_id' => 3,
            ]);
            // // ---end system log-------------
            DB::commit();

            $this->closeModal();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));

        } catch (\Exception $e) {
           
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
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
         DB::beginTransaction();
        try {
            $contract = PermanentContract::findOrFail($id);
            // ---start system log-----------
            SystemLog::create([
                's_id' => $contract->employee_id,
                'user_id' => Auth::user()->id,
                'section' => __('label.permanent_contract').' ID:'.$contract->id.')',
                'type_id' => 4,
            ]);
            // ---end system log-------------
            $contract->delete();
            DB::commit();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }
}
