<?php

namespace App\Livewire\Hr\Employees\Contracts;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\Hr\Position;
use App\Models\Hr\Employee;
use App\Models\Hr\TemporaryContract;
use App\Models\Hr\BookSalaryRate;
use App\Models\CenterSettings\Book;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Section;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;
use Auth;
use DB;
use Illuminate\Validation\Rule;
class EmployeeTemporaryContractList extends Component
{
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'temporary-contract-list-addEditModal';
    public $table_name='temporary_contracts';
    public $pdfOrientation = 'landscape';

    public $selectedFields = [
        
        'no',
        'employee_id',
        'position_id',
        'section_id',
        'branch_id',
        'taxi_fare',
        'credit_card',
        'food_deduction',
        'start_date',
        'end_date',
        'status',
        'security_saving_amount',
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
    public $sections = [];
    public $books = [];
    public $selected_books = [];
    public function mount($active_menu_id = null,$employee_id)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------
        $this->employee_id = $employee_id;

        $this->positions =  Position::all();
        $this->books =  Book::all();
        $this->branches =  Branch::all();
        $this->sections =  Section::all();
    }

    public $position_id;
    public $employee_id;
    public $branch_id;
    public $section_id;

    public $contract_id;
    public $start_date;
    public $end_date;
    public $taxi_fare=0;
    public $credit_card=0;
    public $food_deduction=0;
    public $status='active';
    public $security_saving_amount;
    public $security_saving_monthly_amount;

    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'positions',
            'employees',
            'books',
            'branches',
            'sections',
            'employee_id',
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
        $contracts = TemporaryContract::with('position','branch','section')
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

        return view('livewire.hr.employees.contracts.employee-temporary-contract-list',compact('contracts'));
    }

    protected function rules()
    {
        $branch_id = Auth::user()->branch_id ?: $this->branch_id;

        $rules = [
            'employee_id' => [
                'required',
                'exists:employees,id',

                Rule::unique('temporary_contracts')
                ->ignore($this->contract_id)
                ->where(fn ($q) => $q
                    ->where('employee_id', $this->employee_id)
                    ->where('section_id', $this->section_id)
                    ->where('branch_id', $branch_id)
                    ->where('status', 'active')
                ),
            ],

            'position_id' => 'required|exists:positions,id',
            'section_id' => 'required|exists:sections,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',

            'selected_books' => 'required|array|min:1',
            'selected_books.*.id' => 'required|exists:books,id',
            'selected_books.*.amount' => 'required|numeric|min:1',

            // security saving-------------------
            'security_saving_amount' => 'nullable|numeric|min:0',

            'security_saving_monthly_amount' => [
                'nullable',
                'numeric',
                'min:0',
                'lte:security_saving_amount',
            ],
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
            'selected_books.*.amount.required' => __('label.amount.required'),
            'selected_books.required' => 'At least one book is required',
            'selected_books.min'      => 'At least one book must be selected',
            'branch_id.required'   => __('label.branch.required'),
            'employee_id.unique' => 'This employee already has an active contract in this branch and section.',
            'position_id.required'   => __('label.position.required'),
            'section_id.required'   => __('label.section.required'),
        ];
    }


    public function addBook($book_id)
    {
        $book = Book::find($book_id);

        if (!$book) return;

        // جلوگیری از duplicate
        foreach ($this->selected_books as $b) {
            if ($b['id'] == $book_id) return;
        }

        $this->selected_books[] = [
            'id' => $book->id,
            'name' => $book->name,
            'amount' => 0,
        ];

        $this->resetErrorBag('selected_books');
        $this->dispatch('reset-book-select');
    }

    public function removeBook($index)
    {
        unset($this->selected_books[$index]);
        $this->selected_books = array_values($this->selected_books);
        $this->dispatch('reset-book-select');
    }

    public function updatedSelectedBooks($value, $key)
    {
       
        $this->resetErrorBag('selected_books.' . $key);
    }

    public function store()
    {
        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        $this->validate();

         /*
        |--------------------------------------------------------------------------
        | Security Saving Validation
        |--------------------------------------------------------------------------
        */

        $basic_salary = collect($this->selected_books)->sum('amount');

        if ($this->security_saving_amount > $basic_salary) {

            $this->dispatch(
                'alert',
                type: 'error',
                message: __('Security saving amount cannot be greater than total basic salary.')
            );

            return;
        }

        DB::beginTransaction();

        try {
            
            
            $contract = TemporaryContract::create([
                'position_id'  => $this->position_id,
                'employee_id'  => $this->employee_id,
                'section_id'  => $this->section_id,
                'branch_id'  => Auth::user()->branch_id ?: $this->branch_id,
                'taxi_fare'  => $this->taxi_fare,
                'credit_card'  => $this->credit_card,
                'food_deduction'  => $this->food_deduction,
                'start_date'   => $this->start_date,
                'end_date'     => $this->end_date,
                'status'     => $this->status,
                'security_saving_amount'     => $this->security_saving_amount,
                'security_saving_monthly_amount'     => $this->security_saving_monthly_amount,
            ]);

            // ------اضافه نمودن کتاب---------------------
            foreach ($this->selected_books as $book) {

                BookSalaryRate::create([
                    'book_id' => $book['id'],
                    'temporary_contract_id' => $contract->id,
                    'amount' => $book['amount'],
                ]);
            }

            // // ---start system log-----------
            SystemLog::create([
                's_id' => $this->employee_id,
                'user_id' => Auth::user()->id,
                'section' => __('label.temporary_contract').'ID:'.$contract->id.')',
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

        $contract = TemporaryContract::findOrFail($id);

        $this->contract_id = $contract->id;
        $this->employee_id = $contract->employee_id;
        $this->position_id = $contract->position_id;
        $this->section_id = $contract->section_id;
        $this->branch_id = $contract->branch_id;
        $this->taxi_fare = $contract->taxi_fare;
        $this->credit_card = $contract->credit_card;
        $this->food_deduction = $contract->food_deduction;
        $this->status = $contract->status;
        $this->security_saving_amount = $contract->security_saving_amount;
        $this->security_saving_monthly_amount = $contract->security_saving_monthly_amount;
        $this->start_date = $contract->start_date? $contract->start_date->format('Y-m-d') : null; 
        $this->end_date = $contract->end_date ? $contract->end_date->format('Y-m-d')
        : null;

        $this->selected_books = $contract->bookSalaryRates->map(function ($rate) {
        return [
                'id' => $rate->book_id,
                'name' => $rate->book?->name,
                'amount' => $rate->amount,
            ];
        })->toArray();

        $this->editMode = true;

        $this->dispatch('open-modal', id: $this->modalId);
    }

    public function update()
    {
        if (!edit(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        $this->validate();
         /*
        |--------------------------------------------------------------------------
        | Security Saving Validation
        |--------------------------------------------------------------------------
        */

        $basic_salary = collect($this->selected_books)->sum('amount');
        if ($this->security_saving_amount > $basic_salary) {

            $this->dispatch(
                'alert',
                type: 'error',
                message: __('Security saving amount cannot be greater than total basic salary.')
            );

            return;
        }

        DB::beginTransaction();

        try {

            $contract = TemporaryContract::findOrFail($this->contract_id);
            $contract->update([
                'position_id'  => $this->position_id,
                'employee_id'  => $this->employee_id,
                'section_id'  => $this->section_id,
                'branch_id' =>  Auth::user()->branch_id ?: $this->branch_id,
                'employee_id'  => $this->employee_id,
                'taxi_fare'  => $this->taxi_fare,
                'credit_card'  => $this->credit_card,
                'food_deduction'  => $this->food_deduction,
                'start_date'   => $this->start_date,
                'end_date'     => $this->end_date,
                'status'     => $this->status,
                'security_saving_amount'     => $this->security_saving_amount,
                'security_saving_monthly_amount'     => $this->security_saving_monthly_amount,
            ]);

            // ------اضافه نمودن کتاب----------------------------
            BookSalaryRate::where('temporary_contract_id', $contract->id)->delete();
            foreach ($this->selected_books as $book) {

                BookSalaryRate::create([
                    'book_id' => $book['id'],
                    'temporary_contract_id' => $contract->id,
                    'amount' => $book['amount'],
                ]);
            }

            // // ---start system log-----------
            SystemLog::create([
                's_id' => $this->employee_id,
                'user_id' => Auth::user()->id,
                'section' => __('label.temporary_contract').'ID:'.$contract->id.')',
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
            $contract = TemporaryContract::findOrFail($id);
            // ---start system log-----------
            SystemLog::create([
                's_id' => $contract->employee_id,
                'user_id' => Auth::user()->id,
                'section' => __('label.temporary_contract').' ID:'.$contract->id.')',
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

    public $book_salary_rates = [];
    public function showBookSalaryRate($contract_id)
    {
        $this->book_salary_rates = BookSalaryRate::with('book')
        ->where('temporary_contract_id',$contract_id)
        ->get();

        $this->dispatch('open-modal', id: 'book_salary_rate_modal');
    }
}
