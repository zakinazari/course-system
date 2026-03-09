<?php

namespace App\Livewire\Financial\StudentFees;

use Livewire\Component;
use App\Models\Settings\Menu;
use App\Models\Academic\Student; 
use Auth;
use Carbon\Carbon;
use DB;
class StudentFeesSearch extends Component
{
    // -------start generals--------------------

    protected $paginationTheme = 'bootstrap';   
    public $active_menu_id;
    public $active_menu;

     // Hook for real time error message
    public function updated($propertyName)
    {
        if (array_key_exists($propertyName, $this->rules())) {
            $this->validateOnly($propertyName);
        }
    }


    public function applySearch()
    {
        $this->student = [];
        $this->dispatch('$refresh');
        $this->loadStudent();
    }
    
    // ---------------------------------end generals-------------

    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------
    }

    public $search = [
            'identity' => null,
            'father_name' => null,
        ];

    public $students=[];
    public function render()
    {
        $students = $this->students;
        return view('livewire.financial.student-fees.student-fees-search',compact('students'));
    }

    protected function rules()
    {
        $rules =  [
            'search.student_id' => 'required',

        ];
        return $rules;
    }

    public function loadStudent()
    {
        
        $identity = trim($this->search['identity'] ?? '');
        $father   = trim($this->search['father_name'] ?? '');

        if ($identity === '' && $father === '') {
            $this->students = collect();
            return;
        }

        $this->students = Student::with('photo')
            ->when($identity !== '', function ($q) use ($identity) {
                $q->where(function ($qq) use ($identity) {
                    $qq->where('name', 'like', "%{$identity}%")
                    ->orWhere('student_code', 'like', "%{$identity}%");
                });
            })
            ->when($father !== '', function ($q) use ($father) {
                $q->where('father_name', 'like', "%{$father}%");
            })
            ->orderBy('id', 'desc')
            ->get();
    }
}
