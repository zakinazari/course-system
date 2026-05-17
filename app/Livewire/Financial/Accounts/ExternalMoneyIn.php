<?php

namespace App\Livewire\Financial\Accounts;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CenterSettings\Section;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\Financial\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Auth;
use DB;
use App\Enums\TransactionCategory;
use App\Enums\Action;
use App\Services\TransactionService;
use App\Models\Financial\Account;
class ExternalMoneyIn extends Component
{
      // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'external-money-in-addEditModal';
    public $table_name='accounts';
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
    public $accounts = [],$sections=[];
    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------
        $this->sections = Section::all();
        $user = auth()->user();
        $this->accounts = Account::when($user->branch_id, function ($query) use ($user) {
            $query->where('branch_id', $user->branch_id);
            $query->where('type','branch');
        },function($query)  use ($user){
            if(!$user->isDeveloper()){
                $query->where('type','central');
            }
        })->get();
    }

    public $amount, $account_id,$transaction_id,$section_id,$type,$note;

     public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'accounts',
            'sections',
        ]);
    }
    public $search = [
            'account_id' => null,
        ];

    public function render()
    {
        $user = auth()->user();
        $external_funds = Transaction::with('branch', 'section', 'account')

        ->whereIn('category', [
            'opening_balance',
            'capital_injection',
            'loan_received',
            'correction'
        ])

        ->where('source_type', 'ExternalFund')

        ->when(!empty($this->search['account_id']), function ($query) {

            $query->where('account_id', $this->search['account_id']);
        })

        ->when($user->branch_id,

            function ($query) use ($user) {

                $query->where('branch_id', $user->branch_id)

                    ->whereHas('account', function ($q) {

                        $q->where('type', 'branch');
                    });
            },

            function ($query) use ($user) {

                if (!$user->isDeveloper()) {

                    $query->whereHas('account', function ($q) {

                        $q->where('type', 'central');
                    });
                }
            }
        )
        ->when(!empty($this->search['type']), function ($query) {

            $query->where('category', $this->search['type']);
        })
        ->orderBy('created_at','desc')
        ->paginate($this->perPage);
        return view('livewire.financial.accounts.external-money-in',compact('external_funds'));
    }

    protected function rules()
    {
        return [
            'section_id' => 'required|exists:sections,id',
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required',
        ];
    }
    // Localized messages
    protected function messages()
    {
        return [
            'section_id.required' => __('label.section.required'),
            'account.required' => __('label.account.required'),
            'amount.required'   => __('label.amount.required'),
            'type.required'   => __('label.fund_type.required'),
        ];
    }
    
    // Create role
    public function store()
    {
        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        $this->validate();

        try {

            $account = Account::findOrFail($this->account_id);
            /*
            |--------------------------------------------------------------------------
            | create transaction
            |--------------------------------------------------------------------------
            */

            if ($this->type == 'opening_balance') {

                $exists = Transaction::where('account_id', $this->account_id)

                    ->where('category', 'opening_balance')

                    ->exists();

                if ($exists) {

                    return $this->dispatch(
                        'alert',
                        type: 'error',
                        message: 'Opening balance already exists'
                    );
                }
            }

            Transaction::create([
                'account_id' => $account->id,
                'branch_id' => $account->branch_id,
                'type' => 'income',
                'amount' => $this->amount,
                'category' => $this->type,
                'source_type' => 'ExternalFund',
                'source_id' => null,
                'section_id' => $this->section_id,
                'action' => Action::CREATE,
                'transaction_date' => now(),
                'created_by' => auth()->id(),
                'note' => $this->note,
            ]);

            /*
            |--------------------------------------------------------------------------
            | update balance
            |--------------------------------------------------------------------------
            */
            $account->increment('balance', $this->amount);

            // ---start system log-----------
            $account = SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.account').' ('.$account->name.' ID:'.$account->id.')',
                'type_id' => 2,
            ]);
              DB::commit();
            // ---end system log-------------
            $this->closeModal();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));
        } catch (\Exception $e) {

             DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.store_error') . ': ' . $e->getMessage());
        }
    }


    public function edit($id)
    {
        $this->resetValidation(); 
        $this->transaction_id = $id;    
        $transaction = Transaction::find($id);
        $this->account_id = $transaction->account_id;
        $this->section_id = $transaction->section_id;
        $this->amount = $transaction->amount;
        $this->note = $transaction->note;
        $this->type = $transaction->category;
        $this->editMode = true;
        $this->dispatch('open-modal', id: $this->modalId);
    }
    // Update role
    public function update()
    {
        if (!edit(Auth::user()->role_ids, $this->active_menu_id)) {

            return $this->dispatch('alert',type: 'error',message: __('label.permission_message'));
        }

        $this->validate();

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | original transaction
            |--------------------------------------------------------------------------
            */
            $transaction = Transaction::findOrFail($this->transaction_id);

            /*
            |--------------------------------------------------------------------------
            | opening balance check
            |--------------------------------------------------------------------------
            */
            if ($this->type == 'opening_balance') {

                $exists = Transaction::where('account_id', $this->account_id)

                    ->where('category', 'opening_balance')

                    ->where('id', '!=', $transaction->id)

                    ->exists();

                if ($exists) {

                    return $this->dispatch(
                        'alert',
                        type: 'error',
                        message: 'Opening balance already exists'
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | adjust transaction
            |--------------------------------------------------------------------------
            */
            TransactionService::adjust(

                $transaction->account_id,

                'income',

                $transaction->branch_id,

                $transaction->amount,

                $this->amount,

                TransactionCategory::from($transaction->category),

                'ExternalFund',

                $transaction->id,

                $transaction->section_id,

                Action::UPDATE,
                $this->note,
            );

            /*
            |--------------------------------------------------------------------------
            | update note only
            |--------------------------------------------------------------------------
            */
            $transaction->update([

                'note' => $this->note,
            ]);

            /*
            |--------------------------------------------------------------------------
            | system log
            |--------------------------------------------------------------------------
            */
            SystemLog::create([

                'user_id' => Auth::user()->id,

                'section' => __('label.account') .
                    ' (' . $transaction->account?->name .
                    ' ID:' . $transaction->account_id . ')',

                'type_id' => 3,
            ]);

            DB::commit();

            $this->closeModal();

            $this->dispatch(
                'alert',
                type: 'success',
                message: __('label.successfully_updated')
            );

        } catch (\Exception $e) {

            DB::rollBack();

            $this->dispatch(
                'alert',
                type: 'error',
                message: __('label.update_error') . ' : ' . $e->getMessage()
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
        if (!delete(Auth::user()->role_ids, $this->active_menu_id)) {

            return $this->dispatch(
                'alert',
                type: 'error',
                message: __('label.permission_message')
            );
        }

        DB::beginTransaction();

        try {

            $external_fund = Transaction::findOrFail($id);

            /*
            |--------------------------------------------------------------------------
            | already reversed check
            |--------------------------------------------------------------------------
            */
            $already_reversed = Transaction::where(

                'source_type',
                'ExternalFund'

            )

            ->where('source_id', $external_fund->id)

            ->where('action', Action::DELETE)

            ->exists();

            if ($already_reversed) {

                return $this->dispatch(
                    'alert',
                    type: 'error',
                    message: 'Transaction already reversed'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | reverse transaction
            |--------------------------------------------------------------------------
            */
            TransactionService::expense(

                $external_fund->account_id,

                $external_fund->branch_id,

                $external_fund->amount,

                TransactionCategory::from($external_fund->category),

                'ExternalFund',

                $external_fund->id,

                $external_fund->section_id,

                Action::DELETE,

                'Reverse external fund #' . $external_fund->id
            );

            /*
            |--------------------------------------------------------------------------
            | system log
            |--------------------------------------------------------------------------
            */
            SystemLog::create([

                'user_id' => Auth::user()->id,

                'section' => __('label.account') .
                    ' (' . $external_fund->account?->name .
                    ' ID:' . $external_fund->account_id . ')',

                'type_id' => 4,
            ]);

            DB::commit();

            $this->dispatch(
                'alert',
                type: 'success',
                message: __('label.successfully_deleted')
            );

        } catch (\Exception $e) {

            DB::rollBack();

            $this->dispatch(
                'alert',
                type: 'error',
                message: __('label.delete_error') . ' : ' . $e->getMessage()
            );
        }
    }
}
