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
class ExpenseList extends Component
{
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'expense-list-addEditModal';
    public $table_name='expenses';

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
    public $branches=[], $categories=[], $sections = [],$units =[],$shops = [], $employees = [];
    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->branches =  Branch::all();
        $this->categories =  ExpenseCategory::where('type','expense')->get();
        $this->sections =  Section::all();
        $this->units =  Unit::all();

        $user = auth()->user();
        $this->employees = Employee::when($user->branch_id, function ($query) use ($user) {
            $query->where('branch_id', $user->branch_id);
        })->get();
    
        $this->expense_date = now()->format('Y-m-d');

        if (!auth()->user()->branch_id && !in_array('branch_id', $this->selectedFields)) {
            $this->selectedFields[] = 'branch_id';
        }
    }

    public $name, 
    $expense_id,
    $shop_search,
    $shop_name,
    $shop_id,
    $employee_id,
    $quantity,
    $unit_price,
    $total_amount,
    $expense_date,
    $branch_id,
    $category_id,
    $section_id,
    $unit_id,
    $note,
    $total_expense_amount;
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
            'units',
            'shops',
            'employees',
            'expense_date',
        ]);
    }

    public $search = [
            'name' => null,
            'section_id' => null,
            'category_id' => null,
            'branch_id' => null,
            'from' => null,
            'to' => null,
        ];

    public function render()
    {
        $expenses = $this->loadExpenses();
        return view('livewire.financial.expenses.expense-list',compact('expenses'));
    }
    

    public function loadExpenses(){
        $search = $this->search;
        $expenses = Expense::with('branch','section','category','unit','shop','purchasedByEmployee:id,name,last_name')
        ->when(!empty($this->search['name']), function ($query) {
            $query->where('name', 'like', '%' . $this->search['name'] . '%');
        })
        ->when(!empty($this->search['section_id']), function ($query) {
            $query->where('section_id',$this->search['section_id']);
        })
        ->when(!empty($this->search['category_id']), function ($query) {
            $query->where('expense_category_id',$this->search['category_id']);
        })
        ->when(!empty($this->search['branch_id']), function ($query) {
            $query->where('branch_id',$this->search['branch_id']);
        })
        ->when(!empty($search['from']) && !empty($search['to']), function($q) use ($search){
            $from = Carbon::parse($search['from'])->startOfDay();
            $to   = Carbon::parse($search['to'])->endOfDay();

            $q->whereBetween('created_at', [$from, $to]);
        })
        ->orderBy('created_at','desc')
        ->paginate($this->perPage);

        return $expenses;
    }

    public function exportPdf()
    {
        $expenses = $this->loadExpenses();
        if (!auth()->user()->branch_id && !in_array('branch_id', $this->selectedFields)) {
            $this->selectedFields[] = 'branch_id';
        }
        $this->total_expense_amount = $expenses->sum('total_amount');
        $pdf = Pdf::loadView(
            'livewire.financial.expenses.expense-list-pdf',
            [
                'expenses' => $expenses,
                'selectedFields' => $this->selectedFields,
                'search' =>$this->search,
                'total_expense_amount' =>$this->total_expense_amount,
            ]
        )->setPaper('a4',$this->pdfOrientation)
        ->setOption('defaultFont', 'dejavusans');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            __('label.expense_list').'-' . Carbon::now()->format('Y-m-d -H-i-A') . '.pdf'
        );
    }

    protected function rules()
    {
        $rules =  [
            'name' => 'required',
            'quantity' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'category_id' => 'required',
            'section_id' => 'required',
            'unit_id' => 'required',
            'expense_date' => 'required',
            'shop_id' => 'nullable|exists:shops,id',
            'shop_search' => 'required_without:shop_id|string|min:2',
            
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
            'name.required' => __('label.name.required'),
            'quantity.required' => __('label.quantity.required'),
            'unit_price.required' => __('label.unit_price.required'),
            'category_id.required'   => __('label.category.required'),
            'section_id.required'   => __('label.section.required'),
            'unit_id.required'   => __('label.unit.required'),
        ];
    }
    
    public function updatedQuantity()
    {
        $this->calculateTotal();
    }

    public function updatedUnitPrice()
    {
        $this->calculateTotal();
    }

    private function calculateTotal()
    {
        $quantity = (float) $this->quantity;
        $unit_price = (float) $this->unit_price;

        $this->total_amount = $quantity * $unit_price;
    }

    public function updatedShopSearch()
    {
        if (!$this->shop_search) {
            $this->shops = [];
            return;
        }

        $this->shops = Shop::where('name', 'like', '%' . $this->shop_search . '%')
            ->orderBy('name')
            ->limit(10)
            ->get();
    }

    public function selectShop($id)
    {
        $shop = Shop::find($id);

        if (!$shop) return;

        $this->shop_id = $shop->id;
        $this->shop_search = $shop->name;
        $this->shops = [];
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

            $shop_id = $this->shop_id;

            if (!$shop_id) {

                $name = trim($this->shop_search);

                $shop = Shop::firstOrCreate([
                    'name' => $name,
                ]);

                $shop_id = $shop->id;
            }

            $expense = Expense::create([
                'name' => $this->name,
                'quantity' => $this->quantity,
                'unit_price' => $this->unit_price,
                'unit_id' => $this->unit_id,
                'expense_category_id' => $this->category_id,
                'section_id' => $this->section_id,
                'shop_id' => $shop_id,
                'employee_id' => $this->employee_id,
                'expense_date' => $this->expense_date,
                'note' => $this->note,
                'branch_id'=> Auth::user()->branch_id ?: $this->branch_id,
                'user_id'=> Auth::user()->id,
            ]);

            // -----------start transaction-----------------------------
            $account_id = Account::where('branch_id', $expense->branch_id)
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
                $expense->branch_id,
                $expense->total_amount,
                TransactionCategory::EXPENSE,
                'Expense',
                $expense->id,
                $expense->section_id,
                Action::CREATE
            );
            // -----------start transaction-----------------------------

            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.expense').' ('.$expense->name.' ID:'.$expense->id.')',
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
        $this->expense_id = $id;    
        $expense = Expense::find($id);
        $this->name = $expense->name;
        $this->quantity = $expense->quantity;
        $this->unit_id = $expense->unit_id;
        $this->category_id = $expense->expense_category_id;
        $this->unit_price = $expense->unit_price;
        $this->branch_id = $expense->branch_id;
        $this->section_id = $expense->section_id;
        $this->employee_id = $expense->employee_id;
        $this->note = $expense->note;
        $this->expense_date = $expense->expense_date->format('Y-m-d');

        $this->shop_id = $expense->shop_id;

        $this->shop_search = $expense->shop?->name;

        $this->shops = [];

        $this->editMode = true;
        $this->dispatch('open-modal', id: $this->modalId);
    }
    // Update role
    public function update()
    {
        if(!edit(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        $this->validate();

        DB::beginTransaction();

        try {

            $expense = Expense::findOrFail($this->expense_id);

            // ================= SHOP LOGIC (FIXED) =================
            $shop_id = $this->shop_id;

            if (!$shop_id && $this->shop_search) {

                $shop = Shop::firstOrCreate([
                    'name' => trim($this->shop_search),
                ]);

                $shop_id = $shop->id;
            }


            $expense = Expense::findOrFail($this->expense_id);
            // -----------start transaction-----------------------------

            $account_id = Account::where('branch_id', $expense->branch_id)
                    ->where('category', 'treasury')
                    ->where('type', 'branch')
                    ->value('id');

                if (!$account_id) {

                    return $this->dispatch(
                        'alert',
                        type: 'error',
                        message: __('label.treasury_account_not_found')
                    );
                }

            TransactionService::adjust(
                $account_id,
                'expense',
                $expense->branch_id,
                $expense->total_amount,
                $this->quantity*$this->unit_price,
                TransactionCategory::CORRECTION,
                'Expense',
                $expense->id,
                $expense->section_id,
                Action::UPDATE
            );
            // -----------start transaction-----------------------------

            $expense->update([
                'name' => $this->name,
                'quantity' => $this->quantity,
                'unit_price' => $this->unit_price,
                'unit_id' => $this->unit_id,
                'expense_category_id' => $this->category_id,
                'section_id' => $this->section_id,
                'expense_date' => $this->expense_date,
                'shop_id' => $shop_id,
                'employee_id' => $this->employee_id,
                'note' => $this->note,
                'branch_id'=> Auth::user()->branch_id ?: $this->branch_id,
            ]);

            

            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.expense').' ('.$expense->name.' ID:'.$expense->id.')',
                'type_id' => 3,
            ]);
            // ---end system log-------------

            DB::commit();
            $this->closeModal();
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
        if(!delete(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }
        DB::beginTransaction();
        try {

            $expense = Expense::findOrFail($id);
            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.expense').' ('.$expense->name.' ID:'.$expense->id.')',
                'type_id' => 4,
            ]);
            // ---end system log-------------

            // -----------start transaction-----------------------------
            $account_id = Account::where('branch_id', $expense->branch_id)
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
                $expense->branch_id,
                $expense->total_amount,
                TransactionCategory::EXPENSE,
                'Expense',
                $expense->id,
                $expense->section_id,
                Action::DELETE
            );
            // -----------start transaction-----------------------------

            $expense->delete();
            
            DB::commit();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
            DB::rollBack();
        $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }
}
