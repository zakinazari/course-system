<?php

namespace App\Livewire\Financial\Expenses;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;

use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Section;
use App\Models\CenterSettings\Unit;
use App\Models\Financial\ExpenseCategory;
use App\Models\Financial\Expense;
use App\Models\Financial\ExpenseBudget;
use App\Models\Financial\Shop;
use App\Models\Hr\Employee;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Auth;
use App\Enums\TransactionCategory;
use App\Enums\Action;
use App\Services\TransactionService;
use App\Models\Financial\Account;
use Carbon\Carbon;
use DB;
class ExpenseBudgetList extends Component
{
     // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'expense-budgets-list-addEditModal';
    public $table_name='expense_budgets';

    public $pdfOrientation = 'landscape';

    public $selectedFields = [
        'no',
        'name',
        'quantity',
        'unit_price',
        'total_amount',
        'unit_id',
        'category_id',
        'shop_id',
        'employee_id',
        'expense_date',
        'note',
        'section_id',
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
    public $branches=[], $categories=[], $sections = [];
    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->branches =  Branch::all();
        $this->categories =  ExpenseCategory::all();
        $this->sections =  Section::all();
   

        $user = auth()->user();

        $this->effective_from = now()->format('Y-m-d');

        if (!auth()->user()->branch_id && !in_array('branch_id', $this->selectedFields)) {
            $this->selectedFields[] = 'branch_id';
        }
    }

    public 
    $budget_id,
    $category_id,
    $amount,
    $effective_from,
    $section_id,
    $branch_id,
    $note;
    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'branches',
            'sections',
            'categories',
            'effective_from',
        ]);
    }

    public $search = [
            'name' => null,
            'section_id' => null,
            'category_id' => null,
            'branch_id' => null,
        ];

    public function render()
    {
        $expense_budgets = $this->loadExpenseBudgets();
        return view('livewire.financial.expenses.expense-budget-list',compact('expense_budgets'));
    }

    public function loadExpenseBudgets(){
        $search = $this->search;
        $expense_budgets = ExpenseBudget::with('branch','section','category')
        ->when(!empty($this->search['section_id']), function ($query) {
            $query->where('section_id',$this->search['section_id']);
        })
        ->when(!empty($this->search['category_id']), function ($query) {
            $query->where('expense_category_id',$this->search['category_id']);
        })
        ->when(!empty($this->search['branch_id']), function ($query) {
            $query->where('branch_id',$this->search['branch_id']);
        })
        ->orderBy('created_at','desc')
        ->paginate($this->perPage);

        return $expense_budgets;
    }

    protected function rules()
    {
        $rules =  [
            'amount' => 'required|numeric|min:0',
            'category_id' => 'required',
            'section_id' => 'required',
            'effective_from' => 'required',
        ];

        if (!Auth::user()->branch_id) {
            $rules['branch_id'] = 'required';
        }
        return $rules;
    }
    // Localized messages
    protected function messages()
    {
        return [

            'amount.required' => __('label.amount.required'),
            'category_id.required'   => __('label.category.required'),
            'section_id.required'   => __('label.section.required'),
            'branch_id.required'   => __('label.branch.required'),
        ];
    }
    


    // Create role
    public function store()
    {
        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        $this->validate();

        DB::beginTransaction();

        try {

                $branch_id = Auth::user()->branch_id ?: $this->branch_id;

                // پایان دادن به بودجه فعال قبلی
                ExpenseBudget::where('expense_category_id', $this->category_id)
                    ->where('branch_id', $branch_id)
                    ->where('section_id', $this->section_id)
                    ->whereNull('effective_to')
                    ->update([
                        'effective_to' => Carbon::parse($this->effective_from)
                            ->subDay()
                            ->toDateString(),
                    ]);

                $category = ExpenseCategory::find($this->category_id);


                $amount = $this->amount;

                if(in_array($category->type, [
                    'permanent_payroll',
                    'temporary_payroll'
                ])) {

                    $amount = 0;

                }

                // ایجاد بودجه جدید
                $budget = ExpenseBudget::create([
                    'amount' => $amount,
                    'expense_category_id' => $this->category_id,
                    'section_id' => $this->section_id,
                    'effective_from' => $this->effective_from,
                    'effective_to' => null,
                    'note' => $this->note,
                    'branch_id' => $branch_id,
                    'user_id' => Auth::id(),
                ]);

            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.expense_budget').' ('.$budget?->category?->name.' ID:'.$budget->id.')',
                'type_id' => 2,
            ]);
            // ---end system log-------------
            DB::commit();
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

        $this->budget_id = $id;

        $budget = ExpenseBudget::findOrFail($id);

        $this->category_id = $budget->expense_category_id;
        $this->section_id = $budget->section_id;
        $this->branch_id = $budget->branch_id;

        $this->amount = $budget->amount;
        $this->effective_from = $budget->effective_from?->format('Y-m-d');
        $this->note = $budget->note;

        $this->editMode = true;

        $this->dispatch('open-modal', id: $this->modalId);
    }
    // Update role
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

            $budget = ExpenseBudget::findOrFail($this->budget_id);

            $branch_id = Auth::user()->branch_id ?: $this->branch_id;

            // -------------------------------
            // جلوگیری از رکورد تکراری
            // -------------------------------

            $exists = ExpenseBudget::where('id', '!=', $budget->id)
            ->where('expense_category_id', $this->category_id)
            ->where('branch_id', $branch_id)
            ->where('section_id', $this->section_id)
            ->whereNull('effective_to')
            ->exists();

            if($exists){

                throw new \Exception(
                    __('label.expense_budget_already_exists')
                );

            }


            $old = $budget->replicate();


            $budget->update([

                'expense_category_id' => $this->category_id,

                'section_id' => $this->section_id,

                'branch_id' => $branch_id,

                'amount' => $this->amount,

                'effective_from' => $this->effective_from,

                'note' => $this->note,

            ]);


            // -------------------------------
            // System Log
            // -------------------------------

            SystemLog::create([

                'user_id' => Auth::id(),

                'section' =>
                    __('label.expense_budget')
                    .' | Category: '.$budget->category?->name
                    .' | Branch: '.$budget->branch?->name
                    .' | Section: '.($budget->section?->name ?? '-')
                    .' | Amount: '.number_format($old->amount,2)
                    .' → '.number_format($budget->amount,2)
                    .' | Effective From: '.$old->effective_from
                    .' → '.$budget->effective_from
                    .' | ID: '.$budget->id,

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
                message: __('label.update_error').' : '.$e->getMessage()
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

            return $this->dispatch(
                'alert',
                type: 'error',
                message: __('label.permission_message')
            );
        }


        DB::beginTransaction();

        try {


            $budget = ExpenseBudget::with([
                'category',
                'branch',
                'section'
            ])->findOrFail($id);



            // =========================
            // System Log
            // =========================

            SystemLog::create([

                'user_id' => Auth::id(),

                'section' => 
                    __('label.expense_budget')
                    .' | Category: '.$budget->category?->name
                    .' | Branch: '.$budget->branch?->name
                    .' | Section: '.($budget->section?->name ?? '-')
                    .' | Amount: '.number_format($budget->amount,2)
                    .' | Effective From: '.$budget->effective_from
                    .' | Effective To: '.($budget->effective_to ?? 'Current')
                    .' | ID: '.$budget->id
                    .' | Note: '.($budget->note ?? '-'),

                'type_id' => 4,

            ]);


            $budget->delete();


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
                message: __('label.delete_error').' : '.$e->getMessage()
            );

        }
    }
}
