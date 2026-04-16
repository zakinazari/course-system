<?php

namespace App\Livewire\Financial\StudentFees\ExamFine;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\CenterSettings\Branch;
use App\Models\Academic\Student;
use App\Models\Financial\ExamFine;

use Auth;
use Carbon\Carbon;
use DB;
class ExamFineList extends Component
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
    public $table_name='exam_fines';
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

        $this->fee_type_name = 'Exam Fine';

        $this->modalId = 'student-exam-fine-addEditModal'.$this->fee_type_name;
        $this->billModalId = 'student-exam-fine-billModal'.$this->fee_type_name;
    }

        public $fee_type_id;                
        public $fee_id;                            
        public $branch_id;         
        public $amount;         
        public $payment_date;  
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
        ]);
    }

    public $search = [
            'identity' => null,
            'branch_id' => null,
        ];

    public function render()
    {
        $exam_fines = ExamFine::with('course','examType')
            ->where('student_id',$this->student_id)
            ->orderBy('id','desc')
            ->paginate($this->perPage);
        return view('livewire.financial.student-fees.exam-fine.exam-fine-list',compact('exam_fines'));
    }

    protected function rules()
    {
        $rules= [
            'student_id' => 'required',
        ];
        return $rules;
    }
    // Localized messages
    protected function messages()
    {
        return [
            'student_id.required' => __('label.student_id.required'),
        ];
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

            $exam_fine = ExamFine::find($id);

            SystemLog::create([
                'student_id' => $this->student_id,
                'user_id' => Auth::user()->id,
                'section' => $exam_fine->course?->name.' ('.$exam_fine->amount.' ID:'.$exam_fine->id.')',
                'type_id' => 4,
            ]);

            $exam_fine->delete();

            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));

        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : '.$e->getMessage());
        }
    }

    public $reason;
    public $exam_fine_id;

    public function openWaivedModal($id)
    {
        $this->exam_fine_id = $id;
        $this->dispatch('open-modal', id: "waivedModal");
    }

    public function waivedStore()
    {
        $this->validate([
            'reason' => 'required|string|max:255'
        ]);

        DB::beginTransaction();
       try {

            $exam_fine = ExamFine::find($this->exam_fine_id);

            $exam_fine->update([
                'status' => 'waived',
                'reason' => $this->reason,
            ]);

            $this->reason = null;
            
           // ---start system log-----------
            SystemLog::create([
                'st_id' => $this->student->id,
                'user_id' => Auth::user()->id,
                'section' => __('label.waived_exam_fine').'('.$exam_fine->course?->name.' ID:'.$exam_fine->id.')',
                'type_id' => 3,
            ]);
            // ---end system log-------------
            DB::commit();
            $this->dispatch('close-modal', id: 'waivedModal');
            $this->dispatch('alert',type: 'success',message: __('label.successfully_done'));

        } catch (\Exception $e) {

            DB::rollBack();
            $this->dispatch('alert',type: 'error',message: __('label.store_error') . ': ' . $e->getMessage()
            );
        }
    }

    public function openPayModal($id)
    {
        $this->exam_fine_id = $id;
        $this->dispatch('open-modal', id: "payExamFineModal");
    }

    public function payExamFine()
    {
        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }
        
        DB::beginTransaction();
       try {

            $exam_fine = ExamFine::find($this->exam_fine_id);

            if (!$exam_fine) {
                return;
            }

            if ($exam_fine->status == 'paid') {
                return;
            }

            $exam_fine->update([
                'status' => 'paid',
                'payment_date' => now(),
            ]);

            DB::commit();
            $this->dispatch('close-modal', id: "payExamFineModal");

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
}
