<?php

namespace App\Livewire\Documents\Diplomas;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\CenterSettings\Branch;
use App\Models\Documents\Diploma;
use App\Models\Academic\Student;
use App\Models\Assessment\StudentCourseResult;
use Auth;
use Illuminate\Support\Str;
use DB;
use App\Services\QrCodeService;
class DiplomaPrint extends Component
{

    // -------start generals--------------------
        use WithPagination;
        public $perPage = 10;
        protected $paginationTheme = 'bootstrap';   
        public $editMode = false;
        public $active_menu_id;
        public $active_menu;
        public $modalId = 'account-list-addEditModal';
        public $table_name='diplomas';
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
            $this->diploma = $this->getDiploma();
        }

    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        if(request()->query('slug')){
            $menu = Menu::with(['parent', 'grandParent', 'subMenu'])
            ->where('slug',request()->query('slug'))->first();
            $this->active_menu = $menu;
            $this->active_menu_id = $menu->id;
            $this->dispatch('setActiveMenuFromPage', $menu->id);
        }else{

            $this->dispatch('setActiveMenuFromPage', $active_menu_id);
            $this->active_menu_id = $active_menu_id;
            $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        }
        // -------------start for activing menu in sidebar ----------------------

        if ($code = request()->query('code')) {

            $diploma = Diploma::with('student')
                ->where('verification_code', $code)
                ->first();

            $this->student_code = $diploma?->student?->student_code;
        }
    }

    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
        ]);
    }

    public $search = [
            'identity' => null,
            'branch_id' => null,
        ];

    public function render()
    {
     
        return view('livewire.documents.diplomas.diploma-print');
    }
 
    public $diploma;
    public $student_code;

    protected function rules()
    {
        
        $rules = [
            'student_code' => 'required|exists:students,student_code',
        ];

        return $rules;
    }


    // Localized messages
    protected function messages()
    {
        return [

            'student_code.required' => __('label.student_code.required'),
        ];
    }

    public function getDiploma(){

        $this->validate();

        $diploma = Diploma::with('student','student.photo')
        ->whereHas('student',function($query){
            $query->where('student_code',$this->student_code);
        })
        ->whereNot('is_revoked')
        ->first();
        
        $diploma->getGradeAndAverage();

        $qrService = app(QrCodeService::class);
        $url = route('diploma.verify', $diploma->verification_code);
        $diploma->qr_code = $qrService->diplomaQrGenerate($url);

        return $diploma;
    }
}
