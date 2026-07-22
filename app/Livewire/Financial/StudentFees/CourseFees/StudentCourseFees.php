<?php

namespace App\Livewire\Financial\StudentFees\CourseFees;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\DiscountProvider;
use App\Models\Academic\Student;
use App\Models\Academic\CourseStudent;
use App\Models\Academic\Course;
use App\Models\Assessment\StudentCourseResult;
use App\Models\Financial\StudentCourseFee;
use App\Models\CenterSettings\PhysicalBook;
use App\Models\CenterSettings\BookSpecialDiscount;
use App\Models\Financial\StudentCourseFeeInstallment;
use App\Models\Financial\StudentCourseFeePayment;
use App\Models\Financial\GeneralDiscount;
use App\Models\Financial\StudentBookFee;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\BookInventory;
use App\Models\Warehouse\BookInventoryMovement;
use App\Models\Academic\CourseWaitingList;
use App\Models\User;
use App\Notifications\BookExemptionRequestNotification;
use App\Enums\TransactionCategory;
use App\Enums\Action;
use App\Services\TransactionService;
use App\Models\Financial\Account;
use Auth;
use Carbon\Carbon;
use DB;
use App\Models\Settings\NotificationCategory;
class StudentCourseFees extends Component
{
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'student-course-fees-addEditModal';
    public $installmentModalId = 'show-installment-modal';
    public $table_name='student_course_fees';
    public $selectedFields = [];
    public $pdfOrientation = 'landscape';
    public $branches=[];
    public $special_discount_types = ['makeup','failed','dropped'];

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
    public $student_courses = [];
    public $discount_providers = [];
    public $physical_books = [];
    public $selected_physical_books = [];
    public $physical_books_total = 0;
    public function mount($active_menu_id = null, $student_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->student =Student::findOrFail($student_id);
        $this->discount_providers =DiscountProvider::where('status',true)->get();
    
        $this->branches =  Branch::all();
    }

        public $fee_id;         
        public $branch_id;         
        public $course_id;         
        public $discount_provider_id;         
        public $fee_amount;        
        public $discount_type=null;    
        public $discount_value;  
        public $discount_amount;  
        public $discount_reason;  
        public $g_discount_value;  
        public $g_discount_amount;  
        public $general_discount_amount;  
        public $special_discount_amount;  
        public $special_discount_status;  
        public $total_amount;     
        public $payment_type; 
        public $installments = [];
        public $installment_amount;

        public $exemption_reason;



    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'branches',
            'student',
            'student_courses',
            'discount_providers',
            'special_discount_types',
        ]);
    }

    public $search = [
            'identity' => null,
            'branch_id' => null,
            'status' => null,
        ];
    
    public function render()
    {
        $feeCourseIds = StudentCourseFee::withoutGlobalScopes()->where('student_id', $this->student->id)
        ->pluck('course_id');
        $this->student_courses = $this->student->courses()
        ->with('book') 
        // ->wherePivot('status', 'active')
        ->whereNotIn('courses.id', $feeCourseIds)  
        ->where('courses.status','!=','archived')  
        ->orderBy('pivot_enrolled_at','desc') 
        ->get();

        $course_fees = StudentCourseFee::withoutGlobalScopes()
        ->with(['course' => function ($q) {
            $q->withoutGlobalScopes();
        }])
        ->where('student_id', $this->student->id)
        ->orderBy('id', 'desc')
        ->paginate($this->perPage);

        return view('livewire.financial.student-fees.course-fees.student-course-fees',compact('course_fees'));
    }

    public function updatedCourseId($courseId)
    {
        $course = Course::find($courseId);
        if ($course) {

            $this->fee_amount = $course->book?->fee ?? 0;
            $this->discount_type = $course->discount_type ?? null;
            $this->discount_value = $course->discount_value ?? 0;
            $this->physical_books = PhysicalBook::where('book_id',$course->book_id)->get();
            $this->calculateTotalAmount();

            $this->selected_physical_books = $this->physical_books->map(function ($book) {
                return [
                    'id' => $book->id,
                    'name' => $book->name ?? 'Book #'.$book->id,
                    'price' => $book->price ?? 0,
                    'status' => 'paid',
                ];
            })->toArray();

            $this->calculatePhysicalBooksTotal();

        } else {

            $this->fee_amount = 0;
            $this->discount_type = null;
            $this->discount_value = 0;
            $this->total_amount = 0;
            $this->physical_books = [];
            $this->selected_physical_books = [];

            $this->special_discount_amount = null;
            $this->special_discount_status = null;
            $this->durationDays = null;
            $this->specialDiscount = null;
            $this->discountLimit = null;
        }
    }

    public $durationDays;
    public $specialDiscount;
    public $discountLimit;
    public $discount_error;
    public function calculateTotalAmount()
    {
        
        $this->special_discount_amount = null;
        $this->special_discount_status = null;
        $this->durationDays = null;
        $this->specialDiscount = null;
        $this->discountLimit = null;
      
        $fee = (float) $this->fee_amount;
        $discount = (float) $this->discount_value;

        $this->total_amount = $fee;
        $this->discount_error = null;

        if (!$this->course_id) {
            return;
        }

        $course = Course::with('book')->find($this->course_id);

        if (!$course) {
            return;
        }

        $fee = (float) $this->fee_amount;
        $discount = (float) $this->discount_value;

        $this->total_amount = $fee;
        $this->discount_error = null;

        // ------ General Discount ------------------
     
        if ($course) {
            $general_discount = GeneralDiscount::where('status','active')
                ->where('branch_id',$course->branch_id)
                ->where('book_id',$course->book_id)
                ->latest()
                ->first();
            
            if ($general_discount) {
                
                $this->g_discount_amount = min($general_discount->discount_amount, $course->book?->fee);

                $this->total_amount -= $this->g_discount_amount;

                $this->total_amount = max(0, $this->total_amount);
            }
        }
        // -----------------------------------------

        // ------ Special Discount ----------------------
        $courseStudent = CourseStudent::with([
        'course' => function ($q) {
                $q->withoutGlobalScopes();
            }
        ])
        ->where('student_id', $this->student->id)

        ->whereHas('course', function ($q) use ($course) {
            $q->withoutGlobalScopes()
            ->where('program_id', $course->program_id)
            ->where('book_id', $course->book_id)
            ->where('id', '<>', $course->id);
        })

        ->whereIn('status', $this->special_discount_types)
        ->orderBy('enrolled_at', 'desc')
        ->first();

        if($this->editMode){

            $fee = StudentCourseFee::findOrFail($this->fee_id);

            if($fee->special_discount_amount > 0){

                $this->special_discount_amount = $fee->special_discount_amount;
            
                $this->total_amount -= $this->special_discount_amount;
                $this->total_amount = max(0, $this->total_amount);
            }
        }

        if ($courseStudent  && !$this->editMode) {

            $this->special_discount_status = $courseStudent->status;

            $this->durationDays = Carbon::parse($courseStudent->course->end_date)
                ->startOfDay()
                ->diffInDays(Carbon::now()->startOfDay(), false);

            $rule = BookSpecialDiscount::where('book_id', $course->book_id)
                ->where('type', $courseStudent->status)
                ->orderBy('duration_days', 'asc')
                ->first();

            $this->discountLimit = $rule?->duration_days;

            $specialDiscount = BookSpecialDiscount::where('book_id', $course->book_id)
                ->where('type', $courseStudent->status)
                ->where('duration_days', '>=', $this->durationDays)
                ->orderBy('duration_days', 'asc')
                ->first();
            
            if ($specialDiscount) {
                
                $this->special_discount_amount = min(
                    $specialDiscount->amount,
                    $this->total_amount
                );
                

                $this->total_amount -= $this->special_discount_amount;
                $this->total_amount = max(0, $this->total_amount);

                $this->specialDiscount = $specialDiscount;
            }

           
        }

        // ------ Personal Discount Validation ----------------

        if ($this->discount_type === 'percentage' && $discount > 100) {
            $discount = 100;
            $this->discount_error = "Discount percentage cannot be more than 100%";
        }

        if ($this->discount_type === 'fixed' && $discount > $this->total_amount) {
            $discount = $this->total_amount;
            $this->discount_error = "Discount amount adjusted to remaining total";
        }

        // ------ Apply Personal Discount ----------
        if ($this->discount_type == 'percentage') {
            $this->discount_amount = $this->total_amount * $discount / 100;
        } else { // fixed
            $this->discount_amount = $discount;
        }

        $this->total_amount -= $this->discount_amount;
          
        $this->total_amount = max(0, $this->total_amount);
    }
    
    public function updatedDiscountType($value)
    {   
        if($this->discount_type===''){
            $this->discount_value = null;
        }
        $this->calculateTotalAmount();
    }

    public function updatedDiscountValue($value)
    {
        
        $this->calculateTotalAmount();
        $this->loadProviderDiscountInfo();
    }

    // ----------------store------------------------------

    protected function rules()
    {

        $rules = [
            'course_id' => 'required',
            'fee_amount' => 'required|numeric',
            'total_amount' => 'required|numeric',
        ];

        if(!$this->editMode){
            $rules['payment_type'] =  'required';
        }

        if($this->discount_type !=''){
            $rules['discount_value'] = 'required|numeric|gt:0';
            $rules['discount_reason'] = 'required';
        }
        if($this->discount_type !=''){
            $rules['discount_value'] = 'required|numeric|gt:0';
            $rules['discount_reason'] = 'required';
        }

        if ($this->payment_type === 'installment' && !$this->editMode) {

           $rules['installment_amount'] = 'required|numeric|min:0|lte:total_amount';
        }

        return $rules;
    }
    // Localized messages
    protected function messages()
    {
        $rules =  [
            'course_id.required' => __('label.course_id.required'),
            'payment_type.required' => __('label.payment_type.required'),
            'fee_amount.required' => __('label.fee_amount.required'),
            'fee_amount.numeric' => __('label.fee_amount.numeric'),

            'total_amount.required' => __('label.total_amount.required'),
            'discount_reason.required' => __('label.discount_reason.required'),
            'total_amount.numeric' => __('label.branch.required'),
            'installment_amount.required' => __('label.installments.*.amount.required'),
        ];

        return $rules;
    }

    public function store()
    {

        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        if (!$this->loadProviderDiscountInfo() && $this->discount_type) {
            return;
        }

        $this->validate();

        $course = Course::with('program')->findOrFail($this->course_id);
        
        DB::beginTransaction();

        try {

            // ===============================
            // Remove ONLY courses with NO fee record
            // ===============================

            $programId = $course->program_id;

            $enrollments = CourseStudent::where('student_id', $this->student->id)
                ->where('course_id', '!=', $this->course_id)
                ->whereHas('course', function ($q) use ($programId) {
                    $q->where('program_id', $programId);
                })
                ->get();


            foreach ($enrollments as $enrollment) {

                $hasResult = StudentCourseResult::where('student_id', $enrollment->student_id)
                    ->where('course_id', $enrollment->course_id)
                    ->exists();

                if ($hasResult) {
                    continue;
                }

                $hasFee = StudentCourseFee::where('student_id', $enrollment->student_id)
                    ->where('course_id', $enrollment->course_id)
                    ->exists();

                if (!$hasFee) {
                    $enrollment->delete();
                }
            }

            // جلوگیری از تکرار
            $exists = StudentCourseFee::where('student_id', $this->student->id)
                ->where('course_id', $this->course_id)
                ->exists();

            if ($exists) {
                $this->dispatch('alert',
                    type: 'error',
                    message: 'Fee for this course already exists for this student.'
                );
                return;
            }

            //  تعیین مقدار پرداخت
            $paid = $this->payment_type === 'installment'
                ? $this->installment_amount
                : $this->total_amount;

            $remaining = $this->total_amount - $paid;

            $status = match (true) {
                $remaining <= 0 => 'paid',
                $paid > 0 => 'partial',
                default => 'unpaid',
            };

            //  ایجاد fee
            $studentCourseFee = StudentCourseFee::create([
                'student_id' => $this->student->id,
                'course_id' => $this->course_id,
                'payment_type' => $this->payment_type,
                'fee_amount' => $this->fee_amount,
                'discount_type' => $this->discount_type ?: null,
                'discount_provider_id' => $this->discount_provider_id ?: null,
                'g_discount_amount' => $this->g_discount_amount ?? 0,
                'special_discount_amount' => $this->special_discount_amount ?? 0,
                'special_discount_status' => $this->special_discount_status ?? 0,
                'discount_value' => $this->discount_value ?? 0,
                'discount_amount' => $this->discount_amount ?? 0,
                'discount_reason' => $this->discount_reason,
                'total_amount' => $this->total_amount,

                //  مهم
                'paid_amount' => $paid,
                'remaining_amount' => $remaining,
                'status' => $status,

                'branch_id' => $this->student->branch_id,
                'user_id' => auth()->id(),
            ]);

            //  ثبت installment + payment
            $installment = StudentCourseFeeInstallment::create([
                'student_course_fee_id' => $studentCourseFee->id,
                'due_date' => now()->toDateString(),
                'amount' => $paid,
                'status' => 'paid',
            ]);

            StudentCourseFeePayment::firstOrCreate(
                ['installment_id' => $installment->id],
                [
                    'student_course_fee_id' => $installment->student_course_fee_id,
                    'amount' => $installment->amount,
                    'payment_date' => now(),
                    'notes' => $this->payment_type === 'installment' ? 'Installment payment' : 'Full payment',
                    'user_id' => auth()->id(),
                ]
            );

            // -----------start transaction-----------------------------
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
                    $paid,
                    TransactionCategory::COURSE_FEE,
                    'StudentCourseFee',
                    $studentCourseFee->id,
                    $course->program->section_id,
                    Action::CREATE
                );
            // -----------start transaction-----------------------------


            //  کتاب‌ها
            // گرفتن warehouseهای مربوط به branch
            $warehouseIds = Warehouse::where('branch_id', $this->student->branch_id)
                ->pluck('id');

            foreach ($this->selected_physical_books as $book) {

                $status = $book['status'] ?? 'paid';

                $existing = StudentBookFee::where([
                    'student_id' => $this->student->id,
                    'physical_book_id' => $book['id'],
                ])->first();

                $student_book_fee = StudentBookFee::updateOrCreate(
                    [
                        'student_id' => $this->student->id,
                        'physical_book_id' => $book['id'],
                        'type' => 'automatic',
                    ],
                    [
                        'branch_id' => $this->student->branch_id,
                        'price' => $book['price'],
                        'reason' => $status === 'requested_exemption' ? $this->exemption_reason : null,
                        'status' => $status,
                        'user_id' => Auth::id(),
                    ]
                );

                if ($student_book_fee->wasRecentlyCreated) {
                    
                    $student_book_fee->update([
                            'payment_date' => $status === 'requested_exemption'
                                ? null
                                : now()->toDateString(),
                        ]);
                    }

                    // notification
                    if ($status === 'requested_exemption') {

                        $category = NotificationCategory::where('slug','book_exemption')->first();

                        if (! $category) {
                            return;
                        }

                       $users = User::where(function ($query) use ($category) {

                            // کاربران مرکزی
                            $query->where(function ($q) use ($category) {

                                $q->whereNull('branch_id')
                                    ->whereHas('role.notificationCategories', function ($role) use ($category) {

                                        $role->where('notification_categories.id', $category->id);

                                    });

                            });

                            // کاربران شعبه
                            $query->orWhere(function ($q) use ($category) {

                                $q->where('branch_id', $this->student->branch_id)
                                    ->whereHas('role.notificationCategories', function ($role) use ($category) {

                                        $role->where('notification_categories.id', $category->id);

                                    });

                            });

                        })->get();

                    \Notification::send(
                        $users,
                        new BookExemptionRequestNotification(
                            $this->student,
                            $book,
                            $this->active_menu_id
                        )
                    );
                }

                if ($status === 'paid' && (!$existing || $existing->status !== 'paid')) {

                    $inventory = BookInventory::where('book_id', $book['id'])
                        ->whereIn('warehouse_id', $warehouseIds)
                        ->where('quantity', '>', 0)
                        ->lockForUpdate()
                        ->orderBy('quantity', 'desc')
                        ->first();

                    if (!$inventory) {
                        throw new \Exception('Book "' . ($book['name'] ?? '') . '" is not available in this branch inventory.');
                    }

                    $before = $inventory->quantity;
                    $new_quantity = $before - 1;

                    $inventory->update([
                        'quantity' => $new_quantity
                    ]);

                    BookInventoryMovement::create([
                        'book_inventory_id' => $inventory->id,
                        'quantity_change' => -1,
                        'balance_after' => $new_quantity,
                        'unit_price' => $book['price'] ?? 0,
                        'type' => 'sale',
                        'note' => 'Sold to student ID: ' . $this->student->student_code,
                        'user_id' => auth()->id(),
                    ]);

                    $physical_book = PhysicalBook::with('book.program')->find($book['id']);
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
                        $book['price'],
                        TransactionCategory::BOOK_SALE,
                        'StudentBookFee',
                        $student_book_fee->id,
                        $physical_book->book->program->section_id,
                        Action::CREATE
                    );
                    // -----------------end transaction-----------------------
                }
            }


            //  فعال شدن شاگرد در کورس (فقط اگر پرداخت داشته باشد)
            // if ($paid > 0) {

                $course_student = CourseStudent::where('student_id', $this->student->id)
                    ->where('course_id', $this->course_id)
                    ->firstOrFail();

                $course_student->update([
                    'status' => 'active'
                ]);
            // }

            // ===============================
            // Remove from Waiting List
            // ===============================

            CourseWaitingList::where([
                'student_id' => $this->student->id,
                'program_id' => $course->program_id,
                'book_id' => $course->book_id,
                // 'branch_id' => $course->branch_id,
                // 'shift_id' => $course->shift_id,
            ])->delete();

            //  لاگ سیستم
            SystemLog::create([
                'st_id' => $this->student->id,
                'user_id' => Auth::user()->id,
                'section' => 'Student Course Fee (' . $studentCourseFee->course?->name . ' ID:' . $studentCourseFee->id . ')',
                'type_id' => 2,
            ]);

            DB::commit();

            $this->closeModal();

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

    public $remaining_discount = 0;
    public $used_discount = 0;
    public $discount_progress = 0;
    public $discount_provider_error = null;

    public function loadProviderDiscountInfo()
    {
        if(!$this->discount_provider_id){
            $this->remaining_discount = 0;
            $this->used_discount = 0;
            $this->discount_progress = 0;
            return;
        }

        $provider = DiscountProvider::find($this->discount_provider_id);

        if(!$provider){
            return;
        }

        // مقدار استفاده شده این ماه
        $this->used_discount = StudentCourseFee::where('discount_provider_id',$provider->id)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('discount_amount');
        
        // مقدار باقی مانده
        $this->remaining_discount = $provider->monthly_discount_total - $this->used_discount;

        if($this->remaining_discount < 0){
            $this->remaining_discount = 0;
        }

        // progress
        if($provider->monthly_discount_total > 0){
            $this->discount_progress =
                ($this->used_discount / $provider->monthly_discount_total) * 100;
        }

        // بررسی تخفیف جدید
        $newDiscount = $this->discount_value;

        if($this->discount_type == 'percentage'){
            $newDiscount = ($this->fee_amount * $this->discount_value) / 100;
        }

        if($newDiscount > $this->remaining_discount){

            $this->discount_provider_error =
                "Only {$this->remaining_discount} discount remaining for this provider this month.";

            return false;
        }

        $this->discount_provider_error = null;

        return true;
    }

    public function updatedDiscountProviderId()
    {
        $this->loadProviderDiscountInfo();
    }

  
    public $fee_installments = [];
    public $selected_fee_id;
    public $course_fee;
    public function showInstallments($fee_id)
    {
        $this->selected_fee_id = $fee_id;
        $this->course_fee = StudentCourseFee::find($fee_id);
        $this->fee_installments = StudentCourseFeeInstallment::where('student_course_fee_id',$fee_id)
            ->with('payments')
            ->get();

        $this->dispatch('open-modal', id: $this->installmentModalId);
    }

    public function openPayModal($id)
    {
        $this->installment_id = $id;
        $this->dispatch('open-modal', id: "payInstallmentModal");
    }

    public function payInstallment()
    {
        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }
        
        DB::beginTransaction();
       try {

            $installment = StudentCourseFeeInstallment::find($this->installment_id);

            if (!$installment) {
                return;
            }

            if ($installment->status == 'paid') {
                return;
            }

            $fee = StudentCourseFee::find($installment->student_course_fee_id);

            StudentCourseFeePayment::firstOrCreate(
                ['installment_id' => $installment->id],
                [
                    'student_course_fee_id' => $installment->student_course_fee_id,
                    'amount' => $installment->amount,
                    'payment_date' => now(),
                    'notes' => 'Installment payment',
                    'user_id' => auth()->id(),
                ]
            );

            $installment->update([
                'status' => 'paid'
            ]);

            $fee->paid_amount += $installment->amount;
            $fee->remaining_amount = $fee->total_amount - $fee->paid_amount;

            if ($fee->remaining_amount <= 0) {
                $fee->status = 'paid';
            } else {
                $fee->status = 'partial';
            }

            $fee->save();

            DB::commit();
            $this->dispatch('close-modal', id: "payInstallmentModal");
            $this->showInstallments($installment->student_course_fee_id);
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

    public function edit($id)
    {
        $this->resetValidation();

        $this->fee_id = $id;

        $fee = StudentCourseFee::with('payments')->findOrFail($id);
        // مقدارهای اصلی
        $this->course_id = $fee->course_id;

        $this->fee_amount = $fee->fee_amount;

        $this->discount_type = $fee->discount_type;
        $this->discount_provider_id = $fee->discount_provider_id;
        $this->loadProviderDiscountInfo();
        $this->g_discount_amount = $fee->g_discount_amount;
        $this->special_discount_amount = $fee->special_discount_amount;
        $this->special_discount_status = $fee->special_discount_status;

        $this->discount_value = $fee->discount_value;
        $this->discount_amount = $fee->discount_amount;
        $this->discount_reason = $fee->discount_reason;

        $this->total_amount = $fee->total_amount;

        //  خیلی مهم: پرداخت‌ها را هم نگه دار
        $this->paid_amount = $fee->payments->sum('amount');

        $this->remaining_amount = $this->total_amount - $this->paid_amount;

        $this->editMode = true;

        $this->dispatch('open-modal', id: $this->modalId);
    }

    public function update()
    {
        if (!edit(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert',
                type: 'error',
                message: __('label.permission_message')
            );
        }

        if (!$this->loadProviderDiscountInfo() && $this->discount_type) {
            return;
        }

        $this->validate();

        DB::beginTransaction();

        try {

            $studentCourseFee = StudentCourseFee::findOrFail($this->fee_id);

            $paid = StudentCourseFeePayment::where(
                'student_course_fee_id',
                $studentCourseFee->id
            )->sum('amount');

            if ($this->total_amount < $paid) {

                DB::rollBack();

                return $this->dispatch(
                    'alert',
                    type: 'error',
                    message: 'Discount cannot reduce fee below paid amount.'
                );
            }

            $remaining = $this->total_amount - $paid;

            $status = match (true) {
                $remaining <= 0 => 'paid',
                $paid > 0 => 'partial',
                default => 'unpaid',
            };

            $studentCourseFee->update([

                'discount_type' => $this->discount_type ?: null,
                'discount_provider_id' => $this->discount_provider_id ?: null,
                'g_discount_amount' => $this->g_discount_amount ?? 0,
                'special_discount_amount' => $this->special_discount_amount ?? 0,
                'special_discount_status' => $this->special_discount_status,
                'discount_value' => $this->discount_value ?? 0,
                'discount_amount' => $this->discount_amount ?? 0,
                'discount_reason' => $this->discount_reason,
                'total_amount' => $this->total_amount,
                'paid_amount' => $paid,
                'remaining_amount' => $remaining,
                'status' => $status,
            ]);

            SystemLog::create([
                'st_id' => $studentCourseFee->student_id,
                'user_id' => Auth::id(),
                'section' => 'Student Course Fee Updated (ID:' . $studentCourseFee->id . ')',
                'type_id' => 3,
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

            $fee = StudentCourseFee::with('course.program')->findOrFail($id);
            $payment_amount = StudentCourseFeePayment::where('student_course_fee_id',$fee->id)->sum('amount');
            // -----------start transaction-----------------------------
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
                $payment_amount,
                TransactionCategory::COURSE_FEE,
                'StudentCourseFee',
                $fee->id,
                $fee->course->program?->section_id,
                Action::DELETE
            );
            // -----------start transaction-----------------------------

             SystemLog::create([
                'st_id' => $this->student->id,
                'user_id' => Auth::user()->id,
                'section' => 'Student Course Fee ('.$fee->course?->name.' ID:'.$fee->id.')',
                'type_id' => 4,
            ]);

            $fee->delete();
            DB::commit();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
             DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }

    public $installmentToPrint;
    public $studentCourseFee;
    public function loadInstallmentForPrint($installmentId)
    {
        $this->installmentToPrint = StudentCourseFeeInstallment::with('payments')->find($installmentId);
        $this->studentCourseFee = $this->installmentToPrint->studentCourseFee;
        $this->dispatch('show-print-preview');
    }

    public $cancel_reason;
    public $installment_id;

    public function openCancelModal($id)
    {
        $this->installment_id = $id;
        $this->dispatch('open-modal', id: "cancelInstallmentModal");
    }

    public function cancelInstallment()
    {
        $this->validate([
            'cancel_reason' => 'required|string|max:255'
        ]);

        DB::beginTransaction();
       try {

            $installment = StudentCourseFeeInstallment::find($this->installment_id);

            $installment->update([
                'status' => 'cancelled',
                'cancel_reason' => $this->cancel_reason
            ]);

            $this->cancel_reason = null;
            
           // ---start system log-----------
            SystemLog::create([
                'st_id' => $this->student->id,
                'user_id' => Auth::user()->id,
                'section' => 'Installment Cancelled ('.$installment->course?->name.' ID:'.$installment->id.')',
                'type_id' => 3,
            ]);
            // ---end system log-------------
            $this->showInstallments($installment->student_course_fee_id);
            DB::commit();
            $this->dispatch('close-modal', id: 'cancelInstallmentModal');
            $this->dispatch('alert',type: 'success',message: __('label.successfully_done'));

        } catch (\Exception $e) {

            DB::rollBack();
            $this->dispatch('alert',type: 'error',message: __('label.store_error') . ': ' . $e->getMessage()
            );
        }
    }

    //-----start physical book-------------------------
    public function requestExemption($index)
    {
        $this->selected_physical_books[$index]['status'] = 'requested_exemption';
        $this->calculatePhysicalBooksTotal();
    }

    public function cancelExemption($index)
    {
        $this->selected_physical_books[$index]['status'] = 'paid';
        $this->calculatePhysicalBooksTotal();
    }
    public function calculatePhysicalBooksTotal()
    {
        $this->physical_books_total = collect($this->selected_physical_books)
            ->where('status', 'paid') 
            ->sum('price');
    }
    //-----end physical book--------------------------- 
    public $show_remaining_amount;
    public function openPaymentModal($id)
    {
        $this->selected_fee_id = $id;
        $this->install_amount = null;
        $this->show_remaining_amount = StudentCourseFee::findOrFail($this->selected_fee_id)->remaining_amount;
        $this->dispatch('open-modal', id: "openPaymentModal");
    }
    
    
    public $install_amount;
   public function updatedInstallAmount()
    {
        $fee = StudentCourseFee::find($this->selected_fee_id);

        if (!$fee) return;

        $total_installments = StudentCourseFeeInstallment::where('student_course_fee_id', $this->selected_fee_id)
            ->sum('amount');

        $remaining = $fee->total_amount - $total_installments;

        $this->resetErrorBag('install_amount');

        if ($remaining <= 0) {
            $this->install_amount = null; // پاک کردن input
            $this->addError('install_amount', "All installments have been paid. No more installments allowed.");
            return;
        }

        if ($this->install_amount !== null && $this->install_amount !== '') {
            if ($this->install_amount > $remaining) {
                $this->addError('install_amount', "Installment ({$this->install_amount}) cannot exceed remaining ({$remaining})");
            }
        }
    }
    
    public function storeInstallment()
    {
        $fee = StudentCourseFee::with('course.program')->find($this->selected_fee_id);

        if (!$fee) {
            $this->dispatch('alert', type: 'error', message: 'Fee not found');
            return;
        }

        // مجموع اقساط قبلی
        $total_installments = StudentCourseFeeInstallment::where('student_course_fee_id', $this->selected_fee_id)
            ->sum('amount');

        $remaining = $fee->total_amount - $total_installments;

        $this->validate([
            'install_amount' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($remaining) {
                    if ($value > $remaining) {
                        $fail("Installment ({$value}) cannot exceed remaining ({$remaining})");
                    }
                },
            ],
        ]);

        DB::beginTransaction();

        try {

            //  ایجاد installment (پرداخت شده)
            $installment = StudentCourseFeeInstallment::create([
                'student_course_fee_id' => $this->selected_fee_id,
                'due_date' => now()->toDateString(),
                'amount' => $this->install_amount,
                'status' => 'paid',
            ]);

            //  ثبت payment
            StudentCourseFeePayment::create([
                'installment_id' => $installment->id,
                'student_course_fee_id' => $installment->student_course_fee_id,
                'amount' => $installment->amount,
                'payment_date' => now(),
                'notes' => 'Installment payment',
                'user_id' => auth()->id(),
            ]);
            // -------------------start transaction----------------------
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
            TransactionService::income(
                $account_id,
                $fee->branch_id,
                $this->install_amount,
                TransactionCategory::COURSE_FEE,
                'StudentCourseFeeInstallment',
                $installment->id,
                $fee->course->program->section_id,
                Action::CREATE
            );
            // -------------------end transaction------------------------
            //  آپدیت fee 
            $fee->paid_amount += $this->install_amount;
            $fee->remaining_amount = $fee->total_amount - $fee->paid_amount;

            if ($fee->remaining_amount <= 0) {
                $fee->status = 'paid';
            } else {
                $fee->status = 'partial';
            }

            $fee->save();

           
            if ($fee->paid_amount > 0) {
                CourseStudent::where('student_id', $fee->student_id)
                    ->where('course_id', $fee->course_id)
                    ->update(['status' => 'active']);
            }

            DB::commit();

            $this->showInstallments($this->selected_fee_id);
            $this->reset('install_amount');

            $this->dispatch('close-modal', id: 'openPaymentModal');
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));

        } catch (\Exception $e) {

            DB::rollBack();

            $this->dispatch('alert', type: 'error', message: __('label.store_error') . ': ' . $e->getMessage());
        }
    }
}
