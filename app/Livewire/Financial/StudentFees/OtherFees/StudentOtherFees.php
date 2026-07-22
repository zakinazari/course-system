<?php

namespace App\Livewire\Financial\StudentFees\OtherFees;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\DiscountProvider;
use App\Models\Academic\Student;
use App\Models\Financial\FeeType;
use App\Models\Financial\StudentOtherFee;
use Auth;
use Carbon\Carbon;
use DB;
use App\Enums\TransactionCategory;
use App\Enums\Action;
use App\Services\TransactionService;
use App\Models\Financial\Account;
class StudentOtherFees extends Component
{
    
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId;
    public $billModalId;
    public $table_name='student_other_fees';
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
    public $student;
    public $student_id;
    public function mount($active_menu_id = null, $student_id = null,$fee_type)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------
        $this->student =Student::findOrFail($student_id);
        $this->student_id =$student_id;
        $this->branches =  Branch::all();
        $fee_type = FeeType::where('code',$fee_type)->first();
        $this->fee_type_id = $fee_type?->id;
        $this->fee_type_name = $fee_type?->name;
        $this->amount = $fee_type?->fee_amount;
        $this->modalId = 'student-other-fees-addEditModal'.$this->fee_type_name;
        $this->billModalId = 'student-other-fees-billModal'.$this->fee_type_name;
        $this->payment_date = now()->format('Y-m-d');
    }

        public $fee_type_id;                
        public $fee_id;                
        public $fee_type_name;                
        public $branch_id;         
        public $amount;         
        public $payment_date;  
        public $note;     

    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'branches',
            'student',
            'student_id',
            'fee_type_id',
            'fee_type_name',
            'amount',
            'payment_date',
        ]);
    }

    public $search = [
            'identity' => null,
            'branch_id' => null,
        ];

    public function render()
    {
        $other_fees = StudentOtherFee::query()
            ->where('student_id',$this->student_id)
            ->where('fee_type_id',$this->fee_type_id)
            ->paginate($this->perPage);
        return view('livewire.financial.student-fees.other-fees.student-other-fees',compact('other_fees'));
    }

    protected function rules()
    {
        $rules= [
            'student_id' => 'required',
            'amount' => 'required|numeric',
            // 'note' => 'required',
        ];
        return $rules;
    }
    // Localized messages
    protected function messages()
    {
        return [
            'amount.required' => __('label.amount.required'),
            'note.required'   => __('label.note.required'),
        ];
    }
    // Create role
    public function store()
    {
        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        $this->validate();
        $section_id = FeeType::findOrFail($this->fee_type_id)->section_id;
        DB::beginTransaction();
        try{
            
            $fee = StudentOtherFee::create([
                'amount' => $this->amount,
                'notes' => $this->note,
                'payment_date' => now(),
                'fee_type_id' => $this->fee_type_id,
                'student_id' => $this->student_id,
                'branch_id' => $this->student?->branch_id,
                'user_id' => auth()->id(),
            ]);
            
            // ----------------start transaction-----------------------
            $account_id = Account::where('branch_id', $this->student->branch_id)
                    ->where('category', 'treasury')
                    ->where('type','branch')
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
                $this->student->branch_id,
                $this->amount,
                TransactionCategory::OTHER_FEE,
                'StudentOtherFee',
                $fee->id,
                $section_id,
                Action::CREATE
            );
            // -----------------end transaction-----------------------

            // ---start system log-----------
            SystemLog::create([
                'student_id' => $this->student_id,
                'user_id' => Auth::user()->id,
                'section' => $this->fee_type_name.' ('.$fee->amount.' ID:'.$fee->id.')',
                'type_id' => 2,
            ]);
            // ---end system log-------------
             DB::commit();
            $this->closeModal();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));
        }catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.store_error').' : '. $e->getMessage());
        }
    }


    public function edit($id)
    {
        $this->resetValidation(); 
        $this->fee_id = $id;    
        $fee = StudentOtherFee::find($id);
        $this->amount = $fee?->amount;
        $this->note = $fee?->notes;
        $this->editMode = true;
        $this->dispatch('open-modal', id: $this->modalId);
    }
    // Update role
    public function update()
    {
        if (!edit(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        $this->validate();
        DB::beginTransaction();
        try {
            $fee = StudentOtherFee::find($this->fee_id);
            $fee->update([
                // 'amount' => $this->amount,
                'notes' => $this->note,
                'branch_id' => Auth::user()->branch_id ?: $this->branch_id,
            ]);
            // ---start system log-----------
            SystemLog::create([
                'student_id' => $this->student_id,
                'user_id' => Auth::user()->id,
                'section' => $this->fee_type_name.' ('.$fee->amount.' ID:'.$fee->id.')',
                'type_id' => 3,
            ]);
            // ---end system log-------------
            $this->closeModal();
             DB::commit();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_updated'));
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.update_error').' : '. $e->getMessage());
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
        if (!delete(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }
        DB::beginTransaction();
        try {

            $fee = StudentOtherFee::with('feeType')->find($id);
            if (!$fee) {
                return;
            }

            SystemLog::create([
                'student_id' => $this->student_id,
                'user_id' => Auth::user()->id,
                'section' => $this->fee_type_name.' ('.$fee->amount.' ID:'.$fee->id.')',
                'type_id' => 4,
            ]);

            // -----------start transaction-------------------
            $account_id = Account::where('branch_id', $fee->branch_id)
                    ->where('category', 'treasury')
                    ->where('type','branch')
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
                $fee->branch_id,
                $fee->amount,
                TransactionCategory::OTHER_FEE,
                'StudentOtherFee',
                $fee->id,
                $fee->feeType->section_id,
                Action::DELETE
            );
            // -----------end transaction-------------------

            $fee->delete();
            DB::commit();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : '.$e->getMessage());
        }
    }

    public $fee_bill;
    public function bill($fee_id)
    {
        $this->fee_bill = StudentOtherFee::with('feeType')->find($fee_id);
        $this->dispatch('open-modal', id: $this->billModalId);
    }
}
