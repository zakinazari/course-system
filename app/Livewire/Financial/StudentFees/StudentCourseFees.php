<?php

namespace App\Livewire\Financial\StudentFees;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\DiscountProvider;
use App\Models\Academic\Student;
use App\Models\Financial\StudentCourseFee;
use App\Models\Financial\StudentCourseFeeInstallment;
use App\Models\Financial\StudentCourseFeePayment;

use Auth;
use Carbon\Carbon;
use DB;
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
    public function mount($active_menu_id = null, $student_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->student =Student::with('photo')->findOrFail($student_id);
        $this->discount_providers =DiscountProvider::where('status',true)->get();
    
        $this->branches =  Branch::all();
    }

        public $branch_id;         
        public $course_id;         
        public $discount_provider_id;         
        public $fee_amount;        
        public $discount_type=null;    
        public $discount_value;  
        public $discount_reason;  
        public $total_amount;     
        public $payment_type; 
        public $installments = [];


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
        ]);
    }

    public $search = [
            'identity' => null,
            'branch_id' => null,
            'status' => null,
        ];
    
    public function render()
    {
        $feeCourseIds = StudentCourseFee::where('student_id', $this->student->id)
        ->pluck('course_id');
        $this->student_courses = $this->student->courses()
        ->with('book') 
        ->wherePivot('status', 'active')
        ->whereNotIn('courses.id', $feeCourseIds)  
        ->orderBy('pivot_enrolled_at','desc') 
        ->get();

        $course_fees = StudentCourseFee::with('course')
        ->where('student_id',$this->student->id)
        ->when(!empty($this->search['name']), function ($query) {
            $query->where('name', 'like', '%' . $this->search['name'] . '%');
        })
        ->orderBy('id','desc')
        ->paginate($this->perPage);

        return view('livewire.financial.student-fees.student-course-fees',compact('course_fees'));
    }

    public function updatedCourseId($courseId)
    {
        $course = $this->student_courses->where('id', $courseId)->first();
        if ($course) {

            $this->fee_amount = $course->book?->fee ?? 0;
            $this->discount_type = $course->discount_type ?? null;
            $this->discount_value = $course->discount_value ?? 0;

            $this->calculateTotalAmount();
        } else {

            $this->fee_amount = 0;
            $this->discount_type = null;
            $this->discount_value = 0;
            $this->total_amount = 0;
        }
    }

    public $discount_error;

    public function calculateTotalAmount()
    {
        if ($this->discount_type === 'percentage' && $this->discount_value > 100) {
            $this->discount_value = 0; 
            $this->discount_error = "Discount percentage cannot be more than 100%";
        }elseif($this->discount_type === 'fixed' && $this->discount_value > $this->fee_amount){
            $this->discount_value = 0;
            $this->discount_error = "Discount Amount cannot be more than Total Amount";
        } else {
            $this->discount_error = null;
        }

        $fee = (float) $this->fee_amount;
        $discount = (float) $this->discount_value;

        $this->total_amount = $fee;

        if ($this->discount_type == 'percentage') {
            $this->total_amount -= ($fee * $discount / 100);
        }elseif ($this->discount_type == 'fixed') {
            $this->total_amount -= $discount;
        }

        $this->recalculateInstallments();
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

    public function updatedPaymentType($value)
    {
    
    }
    // --------------installment---------------------------------------

    public $installment_error; 

    public function addInstallment()
    {
        if (!$this->total_amount) {
            $this->installment_error = "Total amount must be set first";
            return;
        }

        $this->installments[] = [
            'due_date' => now()->toDateString(),
            'amount' => 0,
        ];

        $this->recalculateInstallments(); 
    }

    public function removeInstallment($index)
    {
        unset($this->installments[$index]);

        $this->installments = array_values($this->installments);

        $this->recalculateInstallments();
    }

    public function updatedInstallments($value, $name)
    {
        $this->validateInstallments();
       
    }

    public function recalculateInstallments()
    {
        $count = count($this->installments);

        if ($count == 0) {
            return;
        }

        $amountPerInstallment = floor($this->total_amount / $count);
        $remaining = $this->total_amount - ($amountPerInstallment * $count);

        foreach ($this->installments as $index => &$installment) {

            $installment['amount'] = $amountPerInstallment;

            if ($index == $count - 1) {
                $installment['amount'] += $remaining;
            }
        }

        $this->validateInstallments();
    }

    public function updatedTotalAmount()
    {
        $this->recalculateInstallments();
    }

    public function validateInstallments()
    {
        $sum = array_sum(array_column($this->installments, 'amount'));

        if ($sum > $this->total_amount) {
            $this->installment_error = "Sum of installments cannot exceed total amount ({$this->total_amount})";
            return false;
        }

        $this->installment_error = null;
        return true;
    }

    // ----------------store------------------------------

    protected function rules()
    {
        $rules = [
            'course_id' => 'required',
            'payment_type' => 'required',
            'fee_amount' => 'required|numeric',
            'total_amount' => 'required|numeric',
        ];

        if (!Auth::user()->branch_id) {
            $rules['branch_id'] = 'required';
        }

        if($this->discount_type !=''){
            $rules['discount_reason'] = 'required';
        }

        if ($this->payment_type === 'installment') {

            $rules['installments'] = 'required|array|min:1';

            $rules['installments.*.due_date'] = 'required|date';

            $rules['installments.*.amount'] = 'required|numeric|min:0';
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
            'installments.*.amount.required' => __('label.installments.*.amount.required'),
            'installments.*.due_date.required' => __('label.installments.*.due_date.required'),
        ];

        return $rules;
    }

    public function store()
    {
         
        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        if(!$this->loadProviderDiscountInfo()){
            return;
        }
        $this->validate();

        DB::beginTransaction();
       try {

            $sumInstallments = array_sum(array_column($this->installments, 'amount'));
             
            if (($sumInstallments > $this->total_amount || $sumInstallments < $this->total_amount) && $this->payment_type==='installment' ) {
                  DB::rollBack();
                $this->installment_error = "Sum of installments ({$sumInstallments}) cannot exceed total fee ({$this->total_amount})";
                return; 
            }

            // چک unique student + course
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

            $this->installment_error = null;
                
            $studentCourseFee = StudentCourseFee::create([
                'student_id' => $this->student->id,
                'course_id' => $this->course_id,
                'payment_type' => $this->payment_type,
                'fee_amount' => $this->fee_amount,
                'discount_type' => $this->discount_type ?: null,
                'discount_provider_id' => $this->discount_provider_id ?: null,
                'discount_value' => $this->discount_value ?? 0,
                'discount_reason' => $this->discount_reason,
                'total_amount' => $this->total_amount,
                'paid_amount' => 0,
                'remaining_amount' => $this->total_amount,
                'status' => 'unpaid',
                'notes' => $this->notes ?? null,
                'branch_id' => Auth::user()->branch_id ?: $this->branch_id,
                'user_id' => auth()->id(),
            ]);

            
            if ($this->payment_type === 'installment') {
                foreach ($this->installments as $installment) {
                    StudentCourseFeeInstallment::create([
                        'student_course_fee_id' => $studentCourseFee->id,
                        'due_date' => $installment['due_date'],
                        'amount' => $installment['amount'],
                        'status' => 'unpaid',
                    ]);
                }
            } else {
                
                StudentCourseFeeInstallment::create([
                    'student_course_fee_id' => $studentCourseFee->id,
                    'due_date' => now()->toDateString(),
                    'amount' => $this->total_amount,
                    'status' => 'unpaid',
                ]);
            }
            // ---start system log-----------
            SystemLog::create([
                'st_id' => $this->student->id,
                'user_id' => Auth::user()->id,
                'section' => 'Student Course Fee ('.$studentCourseFee->course?->name.' ID:'.$studentCourseFee->id.')',
                'type_id' => 2,
            ]);
            // ---end system log-------------
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
            ->sum('discount_value');

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

    public function showInstallments($fee_id)
    {
        $this->selected_fee_id = $fee_id;

        $this->fee_installments = StudentCourseFeeInstallment::where('student_course_fee_id',$fee_id)
            ->with('payments')
            ->get();

        $this->dispatch('open-modal', id: $this->installmentModalId);
    }

    public function payInstallment($id)
    {
        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }
        
        DB::beginTransaction();
       try {

            $installment = StudentCourseFeeInstallment::find($id);

            if (!$installment) {
                return;
            }

            if ($installment->status == 'paid') {
                return;
            }

            $fee = StudentCourseFee::find($installment->student_course_fee_id);

            StudentCourseFeePayment::create([
                'student_course_fee_id' => $installment->student_course_fee_id,
                'installment_id' => $installment->id,
                'amount' => $installment->amount,
                'payment_date' => now(),
                'notes' => 'Installment payment',
                'user_id' => auth()->id(),
            ]);

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
            $this->dispatch('close-modal', id: $this->installmentModalId);

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

        try {

            $fee = StudentCourseFee::findOrFail($id);
            SystemLog::create([
                'st_id' => $this->student->id,
                'user_id' => Auth::user()->id,
                'section' => 'Student Course Fee ('.$fee->course?->name.' ID:'.$fee->id.')',
                'type_id' => 4,
            ]);
            $fee->delete();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
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
}
