<?php

namespace App\Livewire\Hr\Employees\AssignedBooks;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\CenterSettings\Branch;
use App\Models\Hr\Employee;
use App\Models\Academic\Course;
use App\Models\Academic\CourseStudent;
use App\Models\Financial\StudentBookFee;
use App\Models\CenterSettings\PhysicalBook;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\BookInventory;
use App\Models\Warehouse\BookInventoryMovement;
use App\Models\Warehouse\EmployeeBookMovement;
use Auth;
use Carbon\Carbon;
use DB;
use App\Enums\TransactionCategory;
use App\Enums\Action;
use App\Services\TransactionService;
use App\Models\Financial\Account;
class AssignedBookList extends Component
{
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'assigned-book-list-addEditModal';
    public $billModalId = '';
    public $table_name='student_book_fees';
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
    public $fee_type_name;
    public $physical_books =[];
    public $student_courses =[];
    public function mount($active_menu_id = null, $employee_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->employee_id = $employee_id;
        $this->employee =Employee::findOrFail($employee_id);
        $this->fee_type_name = 'Book Fee';
        $this->branches =  Branch::all();
        
        $this->physical_books = PhysicalBook::all();
    }

    public $fee_type_id;                
        public $fee_id;                            
        public $branch_id;         
        public $amount;         
        public $payment_date;  
        public $physical_book_id;  
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
            'fee_type_name',
            'billModalId',
            'student_courses',
            'physical_books',
        ]);
    }

    public $search = [
            'identity' => null,
            'branch_id' => null,
        ];


    public function render()
    {
        $book_movements = EmployeeBookMovement::query()
            ->where('employee_id',$this->employee_id)
            ->orderBy('id','desc')
            ->paginate($this->perPage);

        return view('livewire.hr.employees.assigned-books.assigned-book-list',compact('book_movements'));
    }

    protected function rules()
    {
        $rules= [
            'employee_id' => 'required',
            'physical_book_id' => 'required',
        ];

        if (!Auth::user()->branch_id) {
            $rules['branch_id'] = 'required';
        }
        if ($this->editMode) {
            $rules['movement_id'] = 'required';
        }

        return $rules;
    }
    // Localized messages
    protected function messages()
    {
        return [
            'employee_id.required' => __('label.employee_id.required'),
            'physical_book_id.required' => __('label.book.required'),
            'branch_id.required' => __('label.branch.required'),
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

            $branch_id = Auth::user()->branch_id ?: $this->branch_id;

            $book = PhysicalBook::with('book.program')
                ->findOrFail($this->physical_book_id);

            // -----------------------------
            // 1. گرفتن inventory
            // -----------------------------
            $inventory = BookInventory::where('book_id', $book->id)
                ->whereHas('warehouse', function ($q) use($branch_id){
                    $q->where('branch_id', $branch_id);
                })
                ->lockForUpdate()
                ->first();

            if (!$inventory) {
                throw new \Exception('Book not found in branch inventory.');
            }

            if ($inventory->quantity < 1) {
                throw new \Exception('Book is out of stock.');
            }

            // -----------------------------
            // 2. کم کردن stock
            // -----------------------------
            $inventory->decrement('quantity');

            // -----------------------------
            // 3. inventory movement (برای استاد)
            // -----------------------------
            BookInventoryMovement::create([
                'book_inventory_id' => $inventory->id,
                'quantity_change'   => -1,
                'balance_after'     => $inventory->quantity,
                'unit_price'        => $book->price,
                'type'              => 'employee_issue',
                'note'              => 'Issued to teacher ID: ' . $this->employee_id,
                'user_id'           => auth()->id(),
            ]);

            // -----------------------------
            // 4. teacher movement log
            // -----------------------------
            EmployeeBookMovement::create([
                'employee_id'       => $this->employee_id,
                'book_inventory_id' => $inventory->id,
                'book_id' => $book->id,
                'quantity'          => 1,
                'type'              => 'issued',
                'movement_date'     => now(),
                'note'              => $this->note,
                'user_id'           => auth()->id(),
            ]);

            // -----------------------------
            // 5. system log
            // -----------------------------
            SystemLog::create([
                'employee_id' => $this->employee_id,
                'user_id'     => Auth::id(),
                'section'     => __('label.teacher_book_issue') . ' (' . $book->name . ')',
                'type_id'     => 2,
            ]);

            DB::commit();

            $this->dispatch('reset-select2');
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


    public $movement_id;
    public $return_date;
    public $return_note;
    public function edit($id)
    {
        $this->resetValidation(); 

        $book_movement = EmployeeBookMovement::findOrFail($id);

        $this->movement_id = $book_movement->id;
        $this->return_date = now()->format('Y-m-d');
      
        $this->editMode = true;

        $this->dispatch('open-modal', id: $this->modalId);
    }

    public function returnBook()
    {
        if (!edit(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch(
                'alert',
                type: 'error',
                message: __('label.permission_message')
            );
        }

        DB::beginTransaction();

        try {

            $movement = EmployeeBookMovement::findOrFail($this->movement_id);

            // آخرین حرکت این کتاب برای این استاد
            $lastMovement = EmployeeBookMovement::where('employee_id', $movement->employee_id)
                ->where('book_id', $movement->book_id)
                ->latest('id')
                ->first();

            if ($lastMovement->type === 'returned') {

                DB::rollBack();

                return $this->dispatch(
                    'alert',
                    type: 'warning',
                    message: __('label.book_already_returned')
                );
            }

            $inventory = BookInventory::lockForUpdate()
                ->findOrFail($movement->book_inventory_id);

            // افزایش موجودی
            $inventory->increment('quantity');
            $inventory->refresh();

            // Inventory Movement
            BookInventoryMovement::create([
                'book_inventory_id' => $inventory->id,
                'quantity_change'   => 1,
                'balance_after'     => $inventory->quantity,
                'unit_price'        => 0,
                'type'              => 'employee_return',
                'note'              => 'Returned from teacher ID: '.$movement->employee_id,
                'user_id'           => Auth::id(),
            ]);

            // Teacher Movement

            $movement = EmployeeBookMovement::findOrFail($this->movement_id);

            $movement->update([
                'type'         => 'returned',
                'return_date'  => $this->return_date,
                'return_note'  => $this->return_note,
                'returned_by'  => Auth::id(),
            ]);

            SystemLog::create([
                'employee_id' => $movement->employee_id,
                'user_id'     => Auth::id(),
                'section'     => __('label.teacher_book_return'),
                'type_id'     => 2,
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
}
