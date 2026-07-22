<?php

namespace App\Livewire\Hr\Employees\SalarySecuritySaving;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\Hr\Employee;
use App\Models\Hr\PermanentContract;
use App\Models\Hr\TemporaryContract;
use App\Models\Hr\EmployeeSecuritySaving;
use App\Models\Hr\LeaveType;
use App\Models\Hr\EmployeeLeave;

use Auth;
use App\Enums\TransactionCategory;
use App\Enums\Action;
use App\Services\TransactionService;
use App\Models\Financial\Account;
use Carbon\Carbon;
use DB;
class SalarySecuritySavingList extends Component
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
        $this->employee =Employee::with([
                'activeTemporaryContract.securitySavings',
                'activePermanentContract.securitySavings',
            ])
            ->find($this->employee_id);

        $this->transaction_date = now()->format('Y-m-d');


    }

    public  $saving_id;
    public $type;
    public $contract_type = null;
    public $amount;

    public $contract_id = null;

    public $contracts = []; 

    public $transaction_date;

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
            'transaction_date',
        ]);
    }

    public $search = [
            'type' => null,
        ];


    public function render()
    {
        $search = $this->search;
        $salary_saving = EmployeeSecuritySaving::with('contract.position','contract.branch')
            ->where('employee_id',$this->employee_id)
            ->when($search['type'],function($q) use($search){
               $q->where('type',$search['type']);
            })
            ->orderBy('id','desc')
            ->paginate($this->perPage);

        return view('livewire.hr.employees.salary-security-saving.salary-security-saving-list',compact('salary_saving'));
    }

    
    protected function rules()
    {
        $rules= [

            'employee_id' => 'required|exists:employees,id',

            'type' => 'required|in:refund,deduction',

            'contract_type' => 'required',

            'contract_id' => 'required',

            'transaction_date' => 'required|date',

            'amount' => 'required|numeric|min:0',

            'note' => 'nullable|string',
    ];

        return $rules;
    }
    // Localized messages
    protected function messages()
    {
        return [
            'employee_id.required' => __('label.employee_id.required'),
            'type.required' => __('label.type.required'),
            'contract_type.required' => __('label.contract_type.required'),
            'contract_id.required' => __('label.contract.required'),
            'transaction_date.required' => __('label.date.required'),

        ];
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
            // Check Balance
            // =========================

            if (in_array($this->type, ['refund','deduction'])) {

                if ($this->amount > $contract->security_saving_balance) {

                    throw new \Exception(
                        __('label.amount_cannot_be_greater_than_balance')
                    );

                }

            }

            // =========================
            // Create Leave
            // =========================

            $saving = $contract->securitySavings()->create([

                'employee_id' => $this->employee_id,

                'type' => $this->type,

                'transaction_date' => $this->transaction_date,

                'amount' => $this->amount,

                'note' => $this->note,

                'user_id' => Auth::id(),

            ]);

            // =========================
            // System Log
            // =========================

            SystemLog::create([

                's_id' => $this->employee_id,

                'user_id' => Auth::id(),

                'section' => __('label.salary_security_saving'),

                'type_id' => 2,

            ]);

            // =========================
            // Create Financial Transaction
            // =========================

            if ($this->type == 'refund') {


                $account_id = Account::where('branch_id', $contract->branch_id)
                    ->where('category', 'treasury')
                    ->where('type','branch')
                    ->value('id');


                if (!$account_id) {

                    throw new \Exception(
                        __('label.treasury_account_not_found')
                    );

                }

                TransactionService::expense(
                    $account_id,
                    $contract->branch_id,
                    $this->amount,
                    TransactionCategory::SECURITY_SAVING_REFUND,
                    'EmployeeSecuritySaving',
                    $saving->id,
                    $contract->section_id,
                    Action::CREATE
                );

            }


            DB::commit();

            $this->closeModal();

            $this->dispatch('alert', type: 'success',message: __('label.successfully_done'));


        } catch (\Exception $e) {


            DB::rollBack();


            $this->dispatch('alert',type: 'error',message: $e->getMessage());

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

            $saving = EmployeeSecuritySaving::with('contract')
            ->whereKey($id)
            ->where('type', '!=', 'deposit')
            ->firstOrFail();

            SystemLog::create([

                's_id' => $saving->employee_id,

                'user_id' => Auth::id(),

                'section' => 
                    __('label.salary_security_saving') .
                    ' | ' .
                    __('label.type') . ': ' . $saving->type .
                    ' | ' .
                    __('label.amount') . ': ' . $saving->amount .
                    ' | ' .
                    __('label.date') . ': ' . $saving->transaction_date 
                    . ' - ' . 
                    ' | ID: ' . $saving->id,

                'type_id' => 4,

            ]);

            if ($saving->type == 'refund') {


                $account_id = Account::where('branch_id', $saving->contract->branch_id)
                    ->where('category', 'treasury')
                    ->where('type','branch')
                    ->value('id');


                if (!$account_id) {

                    throw new \Exception(
                        __('label.treasury_account_not_found')
                    );

                }

                TransactionService::income(
                    $account_id,
                    $saving->contract->branch_id,
                    $saving->amount,
                    TransactionCategory::SECURITY_SAVING_REFUND,
                    'EmployeeSecuritySaving',
                    $saving->id,
                    $saving->contract->section_id,
                    Action::DELETE
                );

            }

            $saving->delete();

            DB::commit();

            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {

            DB::rollBack();

            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }
}
