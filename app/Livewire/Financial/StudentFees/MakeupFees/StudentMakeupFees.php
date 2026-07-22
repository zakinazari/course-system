<?php

namespace App\Livewire\Financial\StudentFees\MakeupFees;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\CenterSettings\Branch;
use App\Models\Academic\Student;
use App\Models\Academic\Course;
use App\Models\Academic\CourseStudent;
use App\Models\Financial\MakeupStudentBookFee;
use App\Models\Financial\MakeupFee;
use App\Models\Financial\MakeupSetting;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\BookInventory;
use App\Models\Warehouse\BookInventoryMovement;
use Auth;
use Carbon\Carbon;
use DB;
use App\Enums\TransactionCategory;
use App\Enums\Action;
use App\Services\TransactionService;
use App\Models\Financial\Account;
class StudentMakeupFees extends Component
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
    public $table_name='student_makeup_fees';
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
    public $makeup_settings =[];
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
      
        $this->fee_type_name = __('label.makeup_fee');

        $this->modalId = 'student-makeup-fees-addEditModal'.$this->fee_type_name;
        $this->billModalId = 'student-makeup-fees-billModal'.$this->fee_type_name;
        
        $this->makeup_settings = MakeupSetting::where('status',true)->get();
    }

        public $fee_type_id;                
        public $fee_id;                            
        public $branch_id;         
        public $amount;         
        public $payment_date;  
        public $makeup_setting_id;  
        public $course_id;  
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
            'makeup_settings',
        ]);
    }

    public $search = [
            'identity' => null,
        ];

    public function render()
    {
        $feeCourseIds = MakeupFee::where('student_id', $this->student->id)
        ->pluck('course_id');
        $this->student_courses = $this->student->courses()
        ->withoutGlobalScopes()
        ->with('book') 
        ->wherePivot('status', 'makeup')
        ->whereNotIn('courses.id', $feeCourseIds)  
        ->where('courses.status','!=','archived')  
        ->orderBy('pivot_enrolled_at','desc') 
        ->get();

        $makeup_fees = MakeupFee::with(['course' => function ($q) {
                $q->withoutGlobalScopes();
            }])
            ->where('student_id',$this->student_id)
            ->orderBy('id','desc')
            ->paginate($this->perPage);
            
        return view('livewire.financial.student-fees.makeup-fees.student-makeup-fees',compact('makeup_fees'));
    }

    protected function rules()
    {
        $rules= [
            'student_id' => 'required',
            'course_id' => 'required',
            'makeup_setting_id' => 'required',
        ];
        return $rules;
    }
    // Localized messages
    protected function messages()
    {
        return [
            'student_id.required' => __('label.student_id.required'),
            'course_id.required' => __('label.course.required'),
            'makeup_setting_id.required' => __('label.amount.required'),
        ];
    }

    public function store()
    {
        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        $this->validate();


        $makeup_setting = MakeupSetting::findOrFail($this->makeup_setting_id);

        $course = Course::with('program','book:id,name')->FindOrFail($this->course_id);

        if (!$course->end_date) {
            return $this->dispatch(
                'alert',
                type: 'error',
                message: __('label.makeup_end_date_missing')
            );
        }

        $allowed_until = Carbon::parse($course->end_date)
            ->addDays($makeup_setting->fee_valid_days);

        if (now()->startOfDay()->gt($allowed_until->startOfDay())) {

            return $this->dispatch(
                'alert',
                type: 'error',
                message: __('label.makeup_expired', [
                    'days' => $makeup_setting->fee_valid_days
                ])
            );
        }

        DB::beginTransaction();

        try {

            // -----------------------------
            // 4. ایجاد fee (paid)
            // -----------------------------
            $fee = MakeupFee::create([

                'course_id' => $this->course_id,
                'student_id' => $this->student_id,
                'amount' => $makeup_setting->fee_amount,
                'note' => $this->note,
                'payment_date' => now(),
                'user_id' => auth()->id(),
            ]);

            // ----------------start transaction-----------------------
            $account_id = Account::where('branch_id', $course->branch_id)
                    ->where('category', 'treasury')
                    ->where('type','branch')
                    ->value('id');

            if (!$account_id) {
                throw new \Exception(__('label.treasury_account_not_found'));
            }

            TransactionService::income(
                $account_id,
                $course->branch_id,
                $fee->amount,
                TransactionCategory::MAKEUP_FEE,
                'MakeupFee',
                $fee->id,
                $course->program->section_id,
                Action::CREATE
            );
            
            // -----------------end transaction-----------------------

            // -----------------------------
            // 5. System log
            // -----------------------------
            SystemLog::create([
                'student_id' => $this->student_id,
                'user_id' => Auth::user()->id,
                'section' => __('label.makeup_fee') . ' (' . $course?->book?->name . ' ID:' . $fee->id . ')',
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

        DB::beginTransaction();

        try {

            $fee = MakeupFee::findOrFail($id);
            $course = Course::with('program','book:id,name')->findOrFail($fee->course_id);
            
            // -----------------------------
            //  Transaction reverse (expense)
            // -----------------------------
            $account_id = Account::where('branch_id', $course->branch_id)
                    ->where('category', 'treasury')
                    ->where('type','branch')
                    ->value('id');

               if (!$account_id) {
                    throw new \Exception(__('label.treasury_account_not_found'));
                }

            TransactionService::expense(
                $account_id,
                $course->branch_id,
                $fee->amount,
                TransactionCategory::MAKEUP_FEE,
                'MakeupFee',
                $fee->id,
                $course->program->section_id,
                Action::DELETE
            );

            // -----------------------------
            //  System log
            // -----------------------------
            SystemLog::create([
                'student_id' => $fee->student_id,
                'user_id' => auth()->id(),
                'section' => __('label.makeup_fee')
                . ' (' . $course->book?->name
                . ' ID:' . $fee->id . ')',
                'type_id' => 4,
            ]);

            // -----------------------------
            //  Delete fee
            // -----------------------------
            $fee->delete();

            DB::commit();
            $this->dispatch('reset-select2');
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));

        } catch (\Exception $e) {

            DB::rollBack();

            $this->dispatch('alert', type: 'error', message: __('label.delete_error') . ' : ' . $e->getMessage());
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

            $book_fee = StudentBookFee::with('book','book.book.program','student')->find($this->fee_id);

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

            // --------start transaction-------------------------
            $account_id = Account::where('branch_id', $book_fee->branch_id)
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
                $book_fee->branch_id,
                $book_fee->price,
                TransactionCategory::BOOK_SALE,
                'StudentBookFee',
                $book_fee->id,
                $book_fee->book->book->program->section_id,
                Action::CREATE
            );

            // --------end transaction-------------------------

            DB::commit();

            $this->dispatch('close-modal', id: "payModal");

            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));

        } catch (\Exception $e) {

            DB::rollBack();

            $this->dispatch('alert', type: 'error', message: __('label.store_error') . ': ' . $e->getMessage());
        }
    }
}
