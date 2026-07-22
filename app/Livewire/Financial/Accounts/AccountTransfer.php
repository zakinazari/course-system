<?php

namespace App\Livewire\Financial\Accounts;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CenterSettings\Section;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\CenterSettings\Branch;
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
class AccountTransfer extends Component
{
    
      // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'account-transfer-addEditModal';
    public $table_name='transactions';
    protected $listeners = ['modalClosed' => 'closeModal','globalDelete' => 'handleGlobalDelete'];
    
    public function closeModal(){
        $this->resetInputFields();
        $this->resetValidation();
        $this->dispatch('close-modal', id: $this->modalId);

    }
    public function openModal(){

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
    public $sections=[];
    public $branches=[];

   
    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------
        $this->sections = Section::all();
        $this->branches = Branch::all();
        $user = auth()->user();

        $this->search['from'] = now()->format('Y-m-d');
        $this->search['to'] = now()->format('Y-m-d');

        $this->from_date = now()->toDateString();

        $this->to_date = now()->toDateString();
    
    }

    public $amount, $from_account_id,$to_account_id,$transaction_id,$section_id,$type,$note;

    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',

            'sections',
            'branches',

            'daily_financial_summary',
            'previous_treasury_balances',
            'current_treasury_balances',
            'from_date',
            'to_date',
        ]);
    }
    public $search = [
            'section_id' => null,
            'branch_id' => null,
            'transfer_type' => null,
            'module_type' => null,
            'from' => null,
            'to' => null,
        ];

    public function render()
    {
        $this->dailyBookSummary();
        $this->dailyFinancialSummary();
        $this->previousTreasuryBalance();
        $this->currentTreasuryBalance();

        // ---------------- Central ----------------
        $this->centralResourceSummary();
        $this->centralFinanceSummary();
        $this->centralBookSummary();
        $this->centralCurrentBalance();
        // ----------------------------------------

        $user = auth()->user();
        $search = $this->search;

        /*
        |--------------------------------------------------------------------------
        | Current Account (Branch or Central)
        |--------------------------------------------------------------------------
        */
        $current_account = $user->branch_id
            ? Account::where('branch_id', $user->branch_id)
                ->where('type', 'branch')
                ->where('category', 'treasury')
                ->firstOrFail()
            : Account::withoutGlobalScopes()
                ->where('type', 'central')
                ->where('category', 'treasury')
                ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | External Transfers (NO DUPLICATION LOGIC)
        |--------------------------------------------------------------------------
        */
        $external_funds = Transaction::withoutGlobalScopes()
        ->with(
                'section',
                'account',
                'fromAccount',
                'fromAccount.branch',
                'toAccount',
                'toAccount.branch',
            )
            ->where('category', TransactionCategory::ACCOUNT_TRANSFER->value)
            ->where('source_type', 'AccountTransfer')

            
            ->where(function ($q) use ($current_account) {

                if ($current_account->type === 'central') {

                    $q->where(function ($q) {
                        $q->whereNull('branch_id')
                        ->orWhere('status', 'pending');
                    });

                } else {

                    $q->where(function ($q) use ($current_account) {

                        // تراکنش‌های خود شعبه
                        $q->where('branch_id', $current_account->branch_id)

                        // انتقال‌های در انتظار از مرکز
                        ->orWhere(function ($q) use ($current_account) {

                            $q->whereNull('branch_id')
                                ->where('status', 'pending')
                                ->where('to_account_id', $current_account->id);

                        });

                    });

                }

            })

            ->when(!empty($search['section_id']), function ($q) use ($search) {
                $q->where('section_id', $search['section_id']);
            })

            ->when(!empty($search['branch_id']), function ($q) use ($search) {
                $q->where('branch_id', $search['branch_id']);
            })

            ->when(!empty($search['from']) && !empty($search['to']), function ($q) use ($search) {
                $q->whereBetween('transaction_date', [
                    $search['from'],
                    $search['to']
                ]);
            })

            ->when($this->search['transfer_type'] == 'transfer_out', function ($q) use ($current_account) {

                $q->where('from_account_id', $current_account->id);

            })

            ->when($this->search['transfer_type'] == 'transfer_in', function ($q) use ($current_account) {

                $q->where('to_account_id', $current_account->id);

            })
            ->when($this->search['module_type'], function ($q) use ($search) {

                $q->where('module_type', $search['module_type']);

            })

            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        /*
        |--------------------------------------------------------------------------
        | Transfer Type (IN / OUT)
        |--------------------------------------------------------------------------
        */
        $external_funds->getCollection()->transform(function ($t) use ($current_account) {

            $t->transfer_type = $t->from_account_id == $current_account->id
                ? 'transfer_out'
                : 'transfer_in';

            return $t;
        });

        return view(
            'livewire.financial.accounts.account-transfer',
            compact('external_funds')
        );
    }


    public $show_amount;
    public $module_type;

    public function openBranchToCentralTransferModal($section_id, $amount, $module_type)
    {
        if ($amount <= 0) {

            return $this->dispatch(
                'alert',
                type: 'error',
                message: 'Balance must be greater than zero'
            );
        }


        $this->to_account_id = null;

        $this->section_id = $section_id;
        $this->amount = $amount;
        $this->show_amount = $amount;
        $this->module_type = $module_type;
         $this->transfer_direction = 'branch_to_central';
        $this->from_account_id = Account::where('branch_id', auth()->user()->branch_id)
            ->where('type', 'branch')
            ->where('category', 'treasury')
            ->value('id');

        $this->to_account_id = Account::withoutGlobalScopes()
            ->where('type', 'central')
            ->where('category', 'treasury')
            ->value('id');

        $this->openModal();
    }

    protected function rules()
    {
        return [
            'section_id' => 'required|exists:sections,id',
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'module_type' => 'required',
        ];
    }
    // Localized messages
    protected function messages()
    {
        return [
            'section_id.required' => __('label.section.required'),
            'from_account_id.required' => __('label.from_account.required'),
            'to_account_id.required' => __('label.to_account.required'),
            'amount.required'   => __('label.amount.required'),
            'type.required'   => __('label.fund_type.required'),
        ];
    }
    
    // Create role
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
            
            /*
            |--------------------------------------------------------------------------
            | accounts
            |--------------------------------------------------------------------------
            */
            $from_account = Account::findOrFail($this->from_account_id);

            $to_account = Account::findOrFail($this->to_account_id);
            

            if ($from_account->id == $to_account->id) {

                return $this->dispatch(
                    'alert',
                    type: 'error',
                    message: 'Source and destination accounts cannot be the same'
                );
            }


            $available_balance = $this->getSectionAccountBalance(
                $this->section_id,
                $to_account->id,
                $this->module_type
            );

            if ($this->amount > $available_balance) {

                return $this->dispatch(
                    'alert',
                    type: 'error',
                    message: __('label.insufficient_balance')
                );
            }

            /*
            |--------------------------------------------------------------------------
            | insufficient balance
            |--------------------------------------------------------------------------
            */

            if ($from_account->balance < $this->amount) {

                return $this->dispatch(
                    'alert',
                    type: 'error',
                    message: __('label.insufficient_balance')
                );
            }

            /*
            |--------------------------------------------------------------------------
            | transfer transaction
            |--------------------------------------------------------------------------
            */
            TransactionService::transfer(

                $from_account->id,

                $to_account->id,

                $this->amount,

                TransactionCategory::ACCOUNT_TRANSFER,

                'AccountTransfer',

                null,

                $this->section_id,

                Action::CREATE,

                $this->note,
                $this->module_type,
            );

            /*
            |--------------------------------------------------------------------------
            | system log
            |--------------------------------------------------------------------------
            */
            SystemLog::create([

                'user_id' => Auth::user()->id,

                'section' =>
                    __('label.account_transfer') .
                    ' (' .
                    $from_account->name .
                    ' -> ' .
                    $to_account->name .
                    ')',

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
            dd($e);
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

                'AccountTransfer',

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

                'AccountTransfer',

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

    public function openApproveModal($id)
    {
        $this->transaction_id = $id;
        $this->dispatch('open-modal', id: "approveModal");
    }

    public function approve()
    {
        $id = $this->transaction_id;
        if (!edit(Auth::user()->role_ids, $this->active_menu_id)) {

            return $this->dispatch(
                'alert',
                type: 'error',
                message: __('label.permission_message')
            );
        }

        DB::beginTransaction();

        try {

            TransactionService::approveTransfer($id);

            /*
            |--------------------------------------------------------------------------
            | system log
            |--------------------------------------------------------------------------
            */
            SystemLog::create([

                'user_id' => Auth::user()->id,

                'section' =>
                    __('label.account_transfer') .
                    ' (Approve ID:' . $id . ')',

                'type_id' => 3,
            ]);

            DB::commit();

            $this->dispatch('close-modal', id: "approveModal");

            $this->dispatch(
                'alert',
                type: 'success',
                message: 'Transfer approved successfully'
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

    public function openRejectModal($id)
    {
        $this->transaction_id = $id;
        $this->dispatch('open-modal', id: "rejectModal");
    }

    public function reject()
    {
        $id = $this->transaction_id;
       
        if (!edit(Auth::user()->role_ids, $this->active_menu_id)) {

            return $this->dispatch(
                'alert',
                type: 'error',
                message: __('label.permission_message')
            );
        }

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | pending transfer
            |--------------------------------------------------------------------------
            */
            $transaction = Transaction::withoutGlobalScopes()->findOrFail($id);
    
            /*
            |--------------------------------------------------------------------------
            | already processed
            |--------------------------------------------------------------------------
            */
            if ($transaction->status != 'pending') {

                return $this->dispatch(
                    'alert',
                    type: 'error',
                    message: 'Transfer already processed'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | source account
            |--------------------------------------------------------------------------
            */
            
            $account = Account::withoutGlobalScopes()->findOrFail(
                $transaction->account_id
            );

            /*
            |--------------------------------------------------------------------------
            | only central pending transfers can be rejected
            |--------------------------------------------------------------------------
            */
            if ($account->type !== 'central') {

                return $this->dispatch(
                    'alert',
                    type: 'error',
                    message: 'Only pending transfers from the central account can be rejected.'
                );
            }
           
            /*
            |--------------------------------------------------------------------------
            | reverse transaction record
            |--------------------------------------------------------------------------
            */
            Transaction::create([

                'account_id' => $account->id,

                'from_account_id' => $transaction->from_account_id,

                'to_account_id' => $transaction->to_account_id,

                'branch_id' => $account->branch_id,

                'type' => 'income',

                'amount' => $transaction->amount,

                'category' => $transaction->category,

                'source_type' => $transaction->source_type,

                'source_id' => $transaction->source_id,

                'section_id' => $transaction->section_id,

                'action' => Action::REJECT->value,

                'note' => $this->note ?: 'Rejected transfer returned to source account',

                'status' => 'rejected',

                'from_account_id' => $transaction->from_account_id,

                'to_account_id' => $transaction->to_account_id,

                'transfer_group_id' => $transaction->transfer_group_id,

                'module_type' => $transaction->module_type,

                'approved_by' => auth()->id(),

                'approved_at' => now(),

                'transaction_date' => now()->toDateString(),

                'created_by' => auth()->id(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | restore balance
            |--------------------------------------------------------------------------
            */
            $account->increment(
                'balance',
                $transaction->amount
            );

            /*
            |--------------------------------------------------------------------------
            | update original transfer
            |--------------------------------------------------------------------------
            */
            $transaction->update([

                'status' => 'rejected',

                'approved_by' => auth()->id(),

                'note' => $this->note ?: 'Rejected transfer returned to source account',

                'approved_at' => now(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | system log
            |--------------------------------------------------------------------------
            */
            SystemLog::create([

                'user_id' => Auth::user()->id,

                'section' =>
                    __('label.account_transfer') .
                    ' (Reject ID:' . $id . ')',

                'type_id' => 3,
            ]);

            DB::commit();

            $this->dispatch('close-modal', id: "rejectModal");

            $this->dispatch(
                'alert',
                type: 'success',
                message: 'Transfer rejected successfully'
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

    // ------------dashboard---------------------------

    public $current_treasury_balances = [];
    public $current_balance = 0;
    
    public $previous_treasury_balances = [];
    public $previous_balance = 0;

    public $daily_financial_summary = [];

    public $daily_financial_total = 0;

    public $from_date;
    public $to_date;

    public function dailyFinancialSummary()
    {
        $branch_id = $this->selected_income_branch_id
            ?? $this->selected_expense_branch_id
            ?? auth()->user()->branch_id;

        $account_id = Account::where('branch_id', $branch_id)
            ->where('type', 'branch')
            ->where('category', 'treasury')
            ->value('id');

        /*
        |--------------------------------------------------------------------------
        | NORMAL INCOME (exclude book sale + transfer)
        |--------------------------------------------------------------------------
        */
        $income_categories = array_values(array_filter(
            TransactionCategory::incomeCategories(),
            fn ($c) =>
                $c !== TransactionCategory::BOOK_SALE
        ));

        /*
        |--------------------------------------------------------------------------
        | NORMAL EXPENSE (exclude asset, book purchase + transfer)
        |--------------------------------------------------------------------------
        */
        $expense_categories = array_values(array_filter(
            TransactionCategory::expenseCategories(),
            fn ($c) =>
                $c !== TransactionCategory::ASSET &&
                $c !== TransactionCategory::BOOK_PURCHASE 
        ));

        /*
        |--------------------------------------------------------------------------
        | NORMAL INCOME
        |--------------------------------------------------------------------------
        */
        $income_by_section = Transaction::where('branch_id', $branch_id)
            ->where('account_id', $account_id)
            ->whereBetween('transaction_date', [$this->from_date, $this->to_date])
            ->whereIn('category', $income_categories)
            ->selectRaw('section_id, SUM(amount) as total')
            ->groupBy('section_id')
            ->pluck('total', 'section_id');

        /*
        |--------------------------------------------------------------------------
        | NORMAL EXPENSE (finance only)
        |--------------------------------------------------------------------------
        */
        $expense_by_section = Transaction::where('branch_id', $branch_id)
            ->where('account_id', $account_id)
            ->where('type', 'expense')

            ->whereBetween('transaction_date', [$this->from_date, $this->to_date])
            ->whereIn('category', $expense_categories)
            ->selectRaw('section_id, SUM(amount) as total')
            ->groupBy('section_id')
            ->pluck('total', 'section_id');

        /*
        |--------------------------------------------------------------------------
        | TRANSFER IN (return / rejected money)
        |--------------------------------------------------------------------------
        */
        $transfer_income = Transaction::where('branch_id', $branch_id)
            ->where('account_id', $account_id)
            ->where('category', TransactionCategory::ACCOUNT_TRANSFER)
            ->where('type', 'income')
            ->where('module_type', 'finance')
            ->whereBetween('transaction_date', [$this->from_date, $this->to_date])
            ->selectRaw('section_id, SUM(amount) as total')
            ->groupBy('section_id')
            ->pluck('total', 'section_id');

        /*
        |--------------------------------------------------------------------------
        | TRANSFER OUT
        |--------------------------------------------------------------------------
        */
        $transfer_expense = Transaction::where('branch_id', $branch_id)
            ->where('account_id', $account_id)
            ->where('category', TransactionCategory::ACCOUNT_TRANSFER)
            ->where('type', 'expense')
            ->where('module_type', 'finance')
            ->whereBetween('transaction_date', [$this->from_date, $this->to_date])
            ->selectRaw('section_id, SUM(amount) as total')
            ->groupBy('section_id')
            ->pluck('total', 'section_id');

        /*
        |--------------------------------------------------------------------------
        | FINAL MAP
        |--------------------------------------------------------------------------
        */
        $total_balance = 0;

        $this->daily_financial_summary = Section::all()->map(function ($section)
            use (
                $income_by_section,
                $expense_by_section,
                $transfer_income,
                $transfer_expense,
                &$total_balance
            ) {

            $income = ($income_by_section[$section->id] ?? 0)
                    + ($transfer_income[$section->id] ?? 0);

            $expense = ($expense_by_section[$section->id] ?? 0)
                    + ($transfer_expense[$section->id] ?? 0);

            $balance = $income - $expense;

            $total_balance += $balance;

            return [
                'id' => $section->id,
                'name' => $section->name,
                'income' => $income,
                'expense' => $expense,
                'balance' => $balance,
            ];
        });

        $this->daily_financial_total = $total_balance;
    }


    public $daily_book_summary = [];
    public $daily_book_total = 0;
    public function dailyBookSummary()
    {
        $branch_id = $this->selected_income_branch_id
            ?? $this->selected_expense_branch_id
            ?? auth()->user()->branch_id;

        $account_id = Account::where('branch_id', $branch_id)
            ->where('type', 'branch')
            ->where('category', 'treasury')
            ->value('id');

        /*
        |--------------------------------------------------------------------------
        | BOOK INCOME (Sales)
        |--------------------------------------------------------------------------
        */
        $book_sales = Transaction::where('branch_id', $branch_id)
            ->where('account_id', $account_id)
            ->where('category', TransactionCategory::BOOK_SALE)
            
            ->whereBetween('transaction_date', [$this->from_date, $this->to_date])
            ->selectRaw('section_id, SUM(amount) as total')
            ->groupBy('section_id')
            ->pluck('total', 'section_id');

        $transform_rejected = Transaction::where('branch_id', $branch_id)
            ->where('account_id', $account_id)
            ->where('category', TransactionCategory::ACCOUNT_TRANSFER)
            ->where('module_type', 'book')
            ->where('type', 'income')
            
            ->whereBetween('transaction_date', [$this->from_date, $this->to_date])
            ->selectRaw('section_id, SUM(amount) as total')
            ->groupBy('section_id')
            ->pluck('total', 'section_id');

        /*
        |--------------------------------------------------------------------------
        | BOOK EXPENSE (Transfers)
        |--------------------------------------------------------------------------
        */
        $book_expense = Transaction::where('branch_id', $branch_id)
            ->where('account_id', $account_id)
            ->where('category', TransactionCategory::ACCOUNT_TRANSFER)
            ->where('module_type','book')
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$this->from_date, $this->to_date])
            ->selectRaw('section_id, SUM(amount) as total')
            ->groupBy('section_id')
            ->pluck('total', 'section_id');

        /*
        |--------------------------------------------------------------------------
        | FINAL MAP
        |--------------------------------------------------------------------------
        */
        $total = 0;

        $this->daily_book_summary = Section::all()->map(function ($section)
            use ($book_sales, $book_expense,$transform_rejected, &$total) {

            $income = ($book_sales[$section->id] ?? 0 )+ ($transform_rejected[$section->id]?? 0);

            $expense = $book_expense[$section->id] ?? 0;

            $balance = $income - $expense;

            $total += $balance;

            return [
                'id' => $section->id,
                'name' => $section->name,
                'income' => $income,
                'expense' => $expense,
                'balance' => $balance,
            ];
        });

        $this->daily_book_total = $total;
    }


    public function previousTreasuryBalance()
    {
        $branch_id = $this->selected_income_branch_id
            ?? $this->selected_expense_branch_id
            ?? auth()->user()->branch_id;

        $account_id = Account::where('branch_id', $branch_id)
            ->where('type', 'branch')
            ->where('category', 'treasury')
            ->value('id');

        $today = now()->toDateString();

        /*
        |--------------------------------------------------------------------------
        | NORMAL INCOME (before today)
        |--------------------------------------------------------------------------
        */
        $income = Transaction::where('branch_id', $branch_id)
            ->where('account_id', $account_id)
            ->where('type', 'income')
            ->whereDate('transaction_date', '<', $today)
            ->where('category', '!=', TransactionCategory::ACCOUNT_TRANSFER)
            ->selectRaw('section_id, SUM(amount) as total')
            ->groupBy('section_id')
            ->pluck('total', 'section_id');

        /*
        |--------------------------------------------------------------------------
        | NORMAL EXPENSE (before today, finance only)
        |--------------------------------------------------------------------------
        */
        $expense = Transaction::where('branch_id', $branch_id)
            ->where('account_id', $account_id)
            ->where('type', 'expense')
            // ->where('module_type', 'finance')
            ->whereDate('transaction_date', '<', $today)
            ->where('category', '!=', TransactionCategory::ACCOUNT_TRANSFER)
            ->selectRaw('section_id, SUM(amount) as total')
            ->groupBy('section_id')
            ->pluck('total', 'section_id');

        /*
        |--------------------------------------------------------------------------
        | TRANSFER IN (before today)
        |--------------------------------------------------------------------------
        */
        $transfer_in = Transaction::where('branch_id', $branch_id)
            ->where('account_id', $account_id)
            ->where('category', TransactionCategory::ACCOUNT_TRANSFER)
            ->where('type', 'income')
            // ->where('module_type', 'finance')
            ->whereDate('transaction_date', '<', $today)
            ->selectRaw('section_id, SUM(amount) as total')
            ->groupBy('section_id')
            ->pluck('total', 'section_id');

        /*
        |--------------------------------------------------------------------------
        | TRANSFER OUT (before today)
        |--------------------------------------------------------------------------
        */
        $transfer_out = Transaction::where('branch_id', $branch_id)
            ->where('account_id', $account_id)
            ->where('category', TransactionCategory::ACCOUNT_TRANSFER)
            ->where('type', 'expense')
            // ->where('module_type', 'finance')
            ->whereDate('transaction_date', '<', $today)
            ->selectRaw('section_id, SUM(amount) as total')
            ->groupBy('section_id')
            ->pluck('total', 'section_id');

        /*
        |--------------------------------------------------------------------------
        | FINAL MAP
        |--------------------------------------------------------------------------
        */
        $total_balance = 0;

        $this->previous_treasury_balances = Section::all()->map(function ($section)
            use ($income, $expense, $transfer_in, $transfer_out, &$total_balance) {

            $incomeTotal = ($income[$section->id] ?? 0)
                        + ($transfer_in[$section->id] ?? 0);

            $expenseTotal = ($expense[$section->id] ?? 0)
                        + ($transfer_out[$section->id] ?? 0);

            $balance = $incomeTotal - $expenseTotal;

            $total_balance += $balance;

            return [
                'id' => $section->id,
                'name' => $section->name,
                'income' => $incomeTotal,
                'expense' => $expenseTotal,
                'balance' => $balance,
            ];
        });

        $this->previous_balance = $total_balance;
    }

    public function currentTreasuryBalance()
    {
        $branch_id = $this->selected_income_branch_id
            ?? $this->selected_expense_branch_id
            ?? auth()->user()->branch_id;

        $account_id = Account::where('branch_id', $branch_id)
            ->where('type', 'branch')
            ->where('category', 'treasury')
            ->value('id');

        /*
        |--------------------------------------------------------------------------
        | NORMAL INCOME
        |--------------------------------------------------------------------------
        */
        $income = Transaction::where('branch_id', $branch_id)
            ->where('account_id', $account_id)
            ->where('type', 'income')
            ->where('category', '!=', TransactionCategory::ACCOUNT_TRANSFER)
            ->selectRaw('section_id, SUM(amount) as total')
            ->groupBy('section_id')
            ->pluck('total', 'section_id');

        /*
        |--------------------------------------------------------------------------
        | NORMAL EXPENSE (finance only)
        |--------------------------------------------------------------------------
        */
        $expense = Transaction::where('branch_id', $branch_id)
            ->where('account_id', $account_id)
            ->where('type', 'expense')
            // ->where('module_type', 'finance')
            ->where('category', '!=', TransactionCategory::ACCOUNT_TRANSFER)
            ->selectRaw('section_id, SUM(amount) as total')
            ->groupBy('section_id')
            ->pluck('total', 'section_id');
        
        /*
        |--------------------------------------------------------------------------
        | TRANSFER IN
        |--------------------------------------------------------------------------
        */
        $transfer_in = Transaction::where('branch_id', $branch_id)
            ->where('account_id', $account_id)
            ->where('category', TransactionCategory::ACCOUNT_TRANSFER)
            ->where('type', 'income')
            // ->where('module_type', 'finance')
            ->selectRaw('section_id, SUM(amount) as total')
            ->groupBy('section_id')
            ->pluck('total', 'section_id');
            

        /*
        |--------------------------------------------------------------------------
        | TRANSFER OUT
        |--------------------------------------------------------------------------
        */
        $transfer_out = Transaction::where('branch_id', $branch_id)
            ->where('account_id', $account_id)
            ->where('category', TransactionCategory::ACCOUNT_TRANSFER)
            ->where('type', 'expense')
            // ->where('module_type', 'finance')
            ->selectRaw('section_id, SUM(amount) as total')
            ->groupBy('section_id')
            ->pluck('total', 'section_id');
       
        /*
        |--------------------------------------------------------------------------
        | FINAL MAP
        |--------------------------------------------------------------------------
        */
        $total_balance = 0;

        $this->current_treasury_balances = Section::all()->map(function ($section)
            use ($income, $expense, $transfer_in, $transfer_out, &$total_balance) {

            $incomeTotal = ($income[$section->id] ?? 0)
                        + ($transfer_in[$section->id] ?? 0);

            $expenseTotal = ($expense[$section->id] ?? 0)
                        + ($transfer_out[$section->id] ?? 0);

            $balance = $incomeTotal - $expenseTotal;

            $total_balance += $balance;
         
            return [
                'id' => $section->id,
                'name' => $section->name,
                'balance' => $balance,
            ];
        });

        $this->current_balance = $total_balance;
    }



       /*
        |--------------------------------------------------------------------------
        | قسمت کدهای حساب مرکزی از اینجا شروع میشود 
        |--------------------------------------------------------------------------
        */
    
    
    public $central_resource_summary = [];

    public $central_finance_summary = [];

    public $central_resource_total = 0;

    public $central_finance_total = 0;

    public function centralResourceSummary()
    {
        $account_id = Account::withoutGlobalScopes()
            ->where('type', 'central')
            ->where('category', 'treasury')
            ->value('id');

        $categories = [
            TransactionCategory::OPENING_BALANCE,
            TransactionCategory::CAPITAL_INJECTION,
            TransactionCategory::LOAN_RECEIVED,
        ];

        $resources = Transaction::where('account_id', $account_id)
            ->where('type', 'income')
            ->whereIn('category', $categories)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        $this->central_resource_summary = collect($categories)->map(function ($category) use ($resources) {

            return [

                'category' => $category->value,

                'name' => str($category->value)
                    ->replace('_', ' ')
                    ->title(),

                'amount' => $resources[$category->value] ?? 0,

            ];

        });

        $this->central_resource_total = $this->central_resource_summary->sum('amount');
    }

    public function centralFinanceSummary()
    {
        $account_id = Account::withoutGlobalScopes()
            ->where('type', 'central')
            ->where('category', 'treasury')
            ->value('id');

        /*
        |--------------------------------------------------------------------------
        | Finance Income
        |--------------------------------------------------------------------------
        */

        $income = Transaction::where('account_id', $account_id)
            ->where('category', TransactionCategory::ACCOUNT_TRANSFER)
            ->where('type', 'income')
            ->where('module_type', 'finance')
            ->selectRaw('section_id,SUM(amount) as total')
            ->groupBy('section_id')
            ->pluck('total', 'section_id');

        /*
        |--------------------------------------------------------------------------
        | Finance Expense
        |--------------------------------------------------------------------------
        */

        $expense = Transaction::where('account_id', $account_id)
            ->where('category', TransactionCategory::ACCOUNT_TRANSFER)
            ->where('type', 'expense')
            ->where('module_type', 'finance')
            ->selectRaw('section_id,SUM(amount) as total')
            ->groupBy('section_id')
            ->pluck('total', 'section_id');


        $total = 0;

        $this->central_finance_summary = Section::all()->map(function ($section) use ($income, $expense, &$total) {

            $incomeTotal = $income[$section->id] ?? 0;

            $expenseTotal = $expense[$section->id] ?? 0;

            $balance = $incomeTotal - $expenseTotal;
            

            $total += $balance;

            // start accounts------------
            $accounts = collect();

            $accounts = Account::where('type', 'branch')
            ->where('category', 'treasury')
            ->get()
            ->map(function ($account) use ($section) {

                return [
                    'name' => $account->name,
                    'account_id' => $account->id,
                    'balance' => $this->getSectionAccountBalance(
                        $section->id,
                        $account->id,
                        'finance'
                    ),
                ];
            })
            ->filter(fn($item) => $item['balance'] != 0)
            ->values();
         // ennd accounts------------
   
            return [

                'id' => $section->id,

                'name' => $section->name,

                'income' => $incomeTotal,

                'expense' => $expenseTotal,

                'accounts' => $accounts,

                'balance' => $balance,
            ];

        });

        $this->central_finance_total = $total;
    }

    // ------------از این قسمت کدهای بخش کتاب شروع میشود -------------------------

    public $central_book_summary = [];

    public $central_book_total = 0;

    public $central_current_balance = 0;

    public function centralBookSummary()
    {
        $account_id = Account::withoutGlobalScopes()
            ->where('type', 'central')
            ->where('category', 'treasury')
            ->value('id');

        /*
        |--------------------------------------------------------------------------
        | BOOK TRANSFER IN
        |--------------------------------------------------------------------------
        */

        $income = Transaction::where('account_id', $account_id)
            ->where('category', TransactionCategory::ACCOUNT_TRANSFER)
            ->where('type', 'income')
            ->where('module_type', 'book')
            ->selectRaw('section_id, SUM(amount) as total')
            ->groupBy('section_id')
            ->pluck('total', 'section_id');

        /*
        |--------------------------------------------------------------------------
        | BOOK TRANSFER OUT
        |--------------------------------------------------------------------------
        */

        $expense = Transaction::where('account_id', $account_id)
            ->where('category', TransactionCategory::ACCOUNT_TRANSFER)
            ->where('type', 'expense')
            ->where('module_type', 'book')
            ->selectRaw('section_id, SUM(amount) as total')
            ->groupBy('section_id')
            ->pluck('total', 'section_id');

        /*
        |--------------------------------------------------------------------------
        | Start BOOK Break Dowons
        |--------------------------------------------------------------------------
        */

        $total = 0;

        $this->central_book_summary = Section::all()->map(function ($section) use ($income, $expense, &$total) {

            $incomeTotal = $income[$section->id] ?? 0;

            $expenseTotal = $expense[$section->id] ?? 0;

            $balance = $incomeTotal - $expenseTotal;

            $total += $balance;

            // -----------accounts-----------
            $accounts = collect();

            $accounts = Account::where('type', 'branch')
                ->where('category', 'treasury')
                ->get()
                ->map(function ($account) use ($section) {

                    return [
                        'name' => $account->name,
                        'account_id' => $account->id,
                        'balance' => $this->getSectionAccountBalance(
                            $section->id,
                            $account->id,
                            'book'
                        ),
                    ];
                })
                ->filter(fn($item) => $item['balance'] != 0)
                ->values();
             // -----------accounts-----------


            return [

                'id' => $section->id,

                'name' => $section->name,

                'income' => $incomeTotal,

                'expense' => $expenseTotal,
                'accounts' => $accounts,
                'balance' => $balance,
            ];
        });

        $this->central_book_total = $total;
    }

    public function centralCurrentBalance()
    {
        $this->central_current_balance =
            $this->central_resource_total
            + $this->central_finance_total
            + $this->central_book_total;
    }

    public $central_previous_balance = 0;

    public function centralPreviousBalance()
    {
        $account_id = Account::withoutGlobalScopes()
            ->where('type', 'central')
            ->where('category', 'treasury')
            ->value('id');

        $today = now()->toDateString();

        $resource = Transaction::where('account_id', $account_id)
            ->whereDate('transaction_date', '<', $today)
            ->whereIn('category', [
                TransactionCategory::OPENING_BALANCE,
                TransactionCategory::CAPITAL_INJECTION,
                TransactionCategory::LOAN_RECEIVED,
            ])
            ->sum('amount');

        $finance = Transaction::where('account_id', $account_id)
            ->whereDate('transaction_date', '<', $today)
            ->where('category', TransactionCategory::ACCOUNT_TRANSFER)
            ->where('module_type', 'finance')
            ->selectRaw("
                SUM(CASE WHEN type='income' THEN amount ELSE 0 END)
                -
                SUM(CASE WHEN type='expense' THEN amount ELSE 0 END)
                as balance
            ")
            ->value('balance') ?? 0;

        $book = Transaction::where('account_id', $account_id)
            ->whereDate('transaction_date', '<', $today)
            ->where('category', TransactionCategory::ACCOUNT_TRANSFER)
            ->where('module_type', 'book')
            ->selectRaw("
                SUM(CASE WHEN type='income' THEN amount ELSE 0 END)
                -
                SUM(CASE WHEN type='expense' THEN amount ELSE 0 END)
                as balance
            ")
            ->value('balance') ?? 0;

        $this->central_previous_balance = $resource + $finance + $book;
    }


    
    public $branch_account;
    public $transfer_direction;
    public function openCentralToBranchTransferModal($section_id,$to_account_id,$amount, $module_type)
    {
        if ($amount <= 0) {

            return $this->dispatch(
                'alert',
                type: 'error',
                message: 'Balance must be greater than zero'
            );
        }

        $this->section_id = $section_id;
        $this->amount = round($amount);
        $this->show_amount = $amount;
        $this->module_type = $module_type;
        $this->transfer_direction = 'central_to_branch';
        $this->from_account_id = Account::withoutGlobalScopes()
            ->where('type', 'central')
            ->where('category', 'treasury')
            ->value('id');
        $this->to_account_id = $to_account_id;
  
        $this->branch_account = Account::find($to_account_id);

        
        $this->openModal();
    }

    private function getSectionAccountBalance($section_id, $account_id, $module_type)
    {
        $central_account_id = Account::withoutGlobalScopes()
            ->where('type', 'central')
            ->where('category', 'treasury')
            ->value('id');

        $income = Transaction::query()
            ->where('account_id', $central_account_id)
            ->where('category', TransactionCategory::ACCOUNT_TRANSFER)
            ->where('type', 'income')
            ->where('module_type', $module_type)
            ->where('section_id', $section_id)
            ->where('from_account_id', $account_id)
            ->sum('amount');

        $expense = Transaction::query()
            ->where('account_id', $central_account_id)
            ->where('category', TransactionCategory::ACCOUNT_TRANSFER)
            ->where('type', 'expense')
            ->where('module_type', $module_type)
            ->where('section_id', $section_id)
            ->where('to_account_id', $account_id)
            ->sum('amount');

        return $income - $expense;
    }

    public function print()
    {
        
        $this->dispatch('show-print-preview');
    }
    

}
