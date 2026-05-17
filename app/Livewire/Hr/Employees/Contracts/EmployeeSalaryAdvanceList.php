<?php

namespace App\Livewire\Hr\Employees\Contracts;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Section;
use App\Models\Hr\Position;
use App\Models\Hr\Employee;
use App\Models\Hr\EmployeeSalaryAdvance;
use App\Models\Hr\EmployeeSalaryAdvancePayment;
use Carbon\Carbon;
use Auth;
use DB;
use App\Enums\TransactionCategory;
use App\Services\TransactionService;
use App\Enums\Action;
class EmployeeSalaryAdvanceList extends Component
{
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'employee-salary-advance-list-addEditModal';
    public $table_name='employee_salary_advances';
    public $pdfOrientation = 'landscape';

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
    public $branches = [];
    public $sections = [];
    public function mount($active_menu_id = null,$employee_id)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->employee_id = $employee_id;
        $this->branches =  Branch::all();
        $this->sections =  Section::all();

    }

    public $employee_id;
    public $branch_id;
    public $section_id;

    public $advance_id;
    public $total_amount;
    public $remaining_amount;
    public $status;
    public $note;

    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'employees',
            'sections',
            'employee_id',
            'branches',
        ]);
    }
    public $search = [
            'employee_id' => null,
            'status' => null,
            'branch_id' => null,
        ];

    public function render()
    {
        $search = $this->search;
        $advances = EmployeeSalaryAdvance::with('branch','section')
        ->where('employee_id',$this->employee_id)

        ->when(!empty($this->search['status']), function($q){

            $q->where('status', $this->search['status']);
        })
        ->when(!empty($this->search['branch_id']), function ($query) {
            $query->where('branch_id',$this->search['branch_id']);
        })
        ->orderBy('id','desc')
        ->paginate($this->perPage);

        return view('livewire.hr.employees.contracts.employee-salary-advance-list',compact('advances'));
    }

    protected function rules()
    {
        $rules = [
            'employee_id'   => 'required|exists:employees,id',
            'section_id'   => 'required|exists:sections,id',
            'total_amount' => 'required|numeric|min:1',
        ];
        if (!Auth::user()->branch_id) {
            $rules['branch_id'] = 'required|exists:branches,id';
        }
        return $rules;
    }
    // Localized messages
    protected function messages()
    {
        return [
            'total_amount.required' => __('label.amount.required'),
            'branch_id.required'   => __('label.branch.required'),
            'section_id.required'   => __('label.section.required'),
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

            $advance = EmployeeSalaryAdvance::create([
                'employee_id' => $this->employee_id,
                'section_id' => $this->section_id,
                'branch_id'  => Auth::user()->branch_id ?: $this->branch_id,
                'total_amount' => $this->total_amount,
                'remaining_amount' => $this->total_amount,
                'note' => $this->note,
            ]);

            // -----------start transaction-----------------------------
            TransactionService::expense(
                $advance->branch_id,
                $advance->total_amount,
                TransactionCategory::SALARY_ADVANCE,
                'EmployeeSalaryAdvance',
                $advance->id,
                $advance->section_id,
                Action::CREATE,
            );
            // -----------start transaction-----------------------------

            // // ---start system log-----------
            SystemLog::create([
                's_id' => $this->employee_id,
                'user_id' => Auth::user()->id,
                'section' => __('label.salary_advance').'ID:'.$advance->id.')',
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

        $advance = EmployeeSalaryAdvance::findOrFail($id);

        $this->advance_id = $advance->id;
        $this->section_id = $advance->section_id;
        $this->branch_id = $advance->branch_id;
        $this->total_amount = $advance->total_amount;
        $this->note = $advance->note;

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

            $advance = EmployeeSalaryAdvance::findOrFail($this->advance_id);
            // -----------start transaction-----------------------------
            TransactionService::adjust(
                'expense', 
                $advance->branch_id,
                $advance->total_amount,
                $this->total_amount,
                TransactionCategory::SALARY_ADVANCE,
                'EmployeeSalaryAdvance',
                $advance->id,
                $advance->section_id,
                Action::UPDATE,
            );
            // -----------end transaction-----------------------------

            $advance->update([
                'branch_id' =>  Auth::user()->branch_id ?: $this->branch_id,
                'section_id' => $this->section_id,
                'total_amount' => $this->total_amount,
                'remaining_amount' => $this->total_amount,
                'note' => $this->note,
            ]);

           

            // // ---start system log-----------
            SystemLog::create([
                's_id' => $this->employee_id,
                'user_id' => Auth::user()->id,
                'section' => __('label.salary_advance').'ID:'.$advance->id.')',
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
            
            $advance = EmployeeSalaryAdvance::findOrFail($id);
            // ---start system log-----------
            SystemLog::create([
                's_id' => $advance->employee_id,
                'user_id' => Auth::user()->id,
                'section' => __('label.salary_advance').' ID:'.$advance->id.')',
                'type_id' => 4,
            ]);
            // ---end system log-------------
            $advance->delete();

            // -----------start transaction-----------------------------
            TransactionService::income(
                $advance->branch_id,
                $advance->total_amount,
                TransactionCategory::SALARY_ADVANCE,
                'EmployeeSalaryAdvance',
                $advance->id,
                $advance->section_id,   
                Action::DELETE,
            );
            // -----------start transaction-----------------------------

            DB::commit();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }

    public $advance_payments = [];
    public function showAdvancePayments($advance_id)
    {
        $this->advance_payments = EmployeeSalaryAdvancePayment::where('employee_salary_advance_id',$advance_id)
        ->get();

        $this->dispatch('open-modal', id: 'advance_payments_modal');

    }
}
