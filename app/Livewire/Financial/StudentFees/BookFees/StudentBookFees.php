<?php

namespace App\Livewire\Financial\StudentFees\BookFees;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\CenterSettings\Branch;
use App\Models\Academic\Student;
use App\Models\Academic\Course;
use App\Models\Academic\CourseStudent;
use App\Models\Financial\StudentBookFee;
use App\Models\CenterSettings\PhysicalBook;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\BookInventory;
use App\Models\Warehouse\BookInventoryMovement;
use Auth;
use Carbon\Carbon;
use DB;
class StudentBookFees extends Component
{
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = '';
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
    public $student;
    public $student_id;
    public $fee_type_name;
    public $physical_books =[];
    public $student_courses =[];
    public function mount($active_menu_id = null, $student_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->student =Student::findOrFail($student_id);
        $this->student_id =$student_id;
        $this->branches =  Branch::all();
        
        $this->fee_type_name = 'Book Fee';

        $this->modalId = 'student-book-fees-addEditModal'.$this->fee_type_name;
        $this->billModalId = 'student-book-fees-billModal'.$this->fee_type_name;
        
        $active_courses = $this->student->courses()
            // ->where('status', 'active') 
            ->get();
        $book_ids = $active_courses->pluck('book_id')->unique();

        $this->physical_books = PhysicalBook::whereIn('book_id', $book_ids)->get();
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
            'student',
            'student_id',
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
        $book_fees = StudentBookFee::with('book')
            ->where('student_id',$this->student_id)
            ->orderBy('id','desc')
            ->paginate($this->perPage);
        return view('livewire.financial.student-fees.book-fees.student-book-fees',compact('book_fees'));
    }

    protected function rules()
    {
        $rules= [
            'student_id' => 'required',
            'physical_book_id' => 'required',
        ];
        return $rules;
    }
    // Localized messages
    protected function messages()
    {
        return [
            'student_id.required' => __('label.student_id.required'),
            'physical_book_id.required' => __('label.book.required'),
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

            $book = PhysicalBook::findOrFail($this->physical_book_id);

            // -----------------------------
            // 1. پیدا کردن inventory
            // -----------------------------
            $inventory = BookInventory::where('book_id', $book->id)
                ->whereHas('warehouse', function ($q) {
                    $q->where('branch_id', $this->student?->branch_id);
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
            $before = $inventory->quantity;
            $inventory->decrement('quantity');

            // -----------------------------
            // 3. ثبت movement
            // -----------------------------
            BookInventoryMovement::create([
                'book_inventory_id' => $inventory->id,
                'quantity_change' => -1,
                'balance_after' => $inventory->quantity,
                'unit_price' => $book->price,
                'type' => 'sale',
                'note' => 'Manual sale to student ID: ' . $this->student_id,
                'user_id' => auth()->id(),
            ]);

            // -----------------------------
            // 4. ایجاد fee (paid)
            // -----------------------------
            $fee = StudentBookFee::create([
                'price' => $book->price,
                'notes' => $this->note,
                'type' => 'manual',
                'payment_date' => now(),
                'physical_book_id' => $this->physical_book_id,
                'student_id' => $this->student_id,
                'branch_id' => $this->student?->branch_id,
                'status' => 'paid',
                'user_id' => auth()->id(),
            ]);

            // -----------------------------
            // 5. System log
            // -----------------------------
            SystemLog::create([
                'student_id' => $this->student_id,
                'user_id' => Auth::user()->id,
                'section' => __('label.physical_book') . ' (' . $book->name . ' ID:' . $fee->id . ')',
                'type_id' => 2,
            ]);

            DB::commit();

            $this->closeModal();

            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));

        } catch (\Exception $e) {

            DB::rollBack();

            $this->dispatch('alert', type: 'error', message: __('label.store_error') . ' : ' . $e->getMessage());
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

        try {

            $fee = StudentBookFee::where('id', $id)
                ->where('student_id', $this->student_id)
                ->first();

            if (!$fee) {
                return;
            }

            SystemLog::create([
                'student_id' => $this->student_id,
                'user_id' => Auth::user()->id,
                'section' => $fee->book?->name.' ('.$fee->price.' ID:'.$fee->id.')',
                'type_id' => 4,
            ]);

            $fee->delete();

            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));

        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : '.$e->getMessage());
        }
    }

    public function openConfirmExemption($id)
    {
        $this->fee_id = $id;
        $this->dispatch('open-modal', id: "confirmExemptionModal");
    }


    public function confirmExemption()
    {
        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }
        
        DB::beginTransaction();
       try {

            $book_fee = StudentBookFee::find($this->fee_id);

            if (!$book_fee) {
                return;
            }

            if ($book_fee->status == 'paid') {
                return;
            }

            $book_fee->update([
                'status' => 'accepted_exemption',
                'processed_by' =>  Auth::user()->id,
            ]);

            DB::commit();
            $this->dispatch('close-modal', id: "confirmExemptionModal");

            $this->dispatch('alert',
                type: 'success',
                message: __('label.successfully_done')
            );
            
        } catch (\Exception $e) {

            DB::rollBack();
            $this->dispatch('alert',
                type: 'error',
                message: __('label.store_error') . ': ' . $e->getMessage()
            );
        }
    }

    public function openRejectExemption($id)
    {
        $this->fee_id = $id;
        $this->dispatch('open-modal', id: "rejectExemptionModal");
    }

    public function rejectExemption()
    {
        $this->validate([
            'fee_id' => 'required',
            'note' => 'required|string|max:255'
        ]);

        DB::beginTransaction();
       try {

            $book_fee = StudentBookFee::findOrFail($this->fee_id);

            $book_fee->update([
                'status' => 'rejected_exemption',
                'processed_by' =>  Auth::user()->id,
                'note' => $this->note,
            ]);

            $this->note = null;
            
           // ---start system log-----------
            SystemLog::create([
                'st_id' => $this->student->id,
                'user_id' => Auth::user()->id,
                'section' => __('label.book_fee').'('.$book_fee->course?->name.' ID:'.$book_fee->id.')',
                'type_id' => 3,
            ]);
            // ---end system log-------------
            DB::commit();
            $this->dispatch('close-modal', id: 'rejectExemptionModal');
            $this->dispatch('alert',type: 'success',message: __('label.successfully_done'));

        } catch (\Exception $e) {

            DB::rollBack();
            $this->dispatch('alert',type: 'error',message: __('label.store_error') . ': ' . $e->getMessage()
            );
        }
    }

    public function openPayModal($id)
    {
        $this->fee_id = $id;
        $this->dispatch('open-modal', id: "payModal");
    }

    public function payStore()
    {
        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        DB::beginTransaction();

        try {

            $book_fee = StudentBookFee::with('book','student')->find($this->fee_id);

            if (!$book_fee) {
                throw new \Exception('Fee not found.');
            }

            if ($book_fee->status === 'paid') {
                DB::rollBack();
                return $this->dispatch('alert', type: 'info', message: 'Already paid.');
            }

            // -------------------------
            // 1. پیدا کردن inventory کتاب
            // -------------------------
            $inventory = BookInventory::where('book_id', $book_fee->physical_book_id)
                ->lockForUpdate()
                ->first();

            if (!$inventory) {
                throw new \Exception('Book not found in inventory.');
            }

            if ($inventory->quantity < 1) {
                throw new \Exception('Book is out of stock.');
            }

            // -------------------------
            // 2. کم کردن stock
            // -------------------------
            $before = $inventory->quantity;
            $inventory->decrement('quantity');

            // -------------------------
            // 3. ثبت movement
            // -------------------------
            BookInventoryMovement::create([
                'book_inventory_id' => $inventory->id,
                'quantity_change' => -1,
                'balance_after' => $inventory->quantity,
                'unit_price' => $book_fee->price ?? 0,
                'type' => 'sale',
                'note' => 'Payment by student fee ID: ' . $book_fee->student->student_code,
                'user_id' => auth()->id(),
            ]);

            // -------------------------
            // 4. update fee
            // -------------------------
            $book_fee->update([
                'status' => 'paid',
                'payment_date' => now(),
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            $this->dispatch('close-modal', id: "payModal");

            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));

        } catch (\Exception $e) {

            DB::rollBack();

            $this->dispatch('alert', type: 'error', message: __('label.store_error') . ': ' . $e->getMessage());
        }
    }
}
