<?php

namespace App\Livewire\Financial\StudentFees;

use Livewire\Component;
use App\Models\Settings\Menu;
use App\Models\Academic\Student;
use App\Models\Academic\Course; 
use Auth;
use Carbon\Carbon;
use DB;
class StudentFinancialProfile extends Component
{
    
    // -------start generals--------------------

    protected $paginationTheme = 'bootstrap';   
    public $active_menu_id;
    public $active_menu;
    public $student;
    // ---------------------------------end generals-------------
    
    public function mount($active_menu_id = null, $student_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------
        $student_id = decrypt($student_id);
        $this->student =Student::with('photo')->findOrFail($student_id);
    }

    public $activeTab = 'course_fee';

    public function changeTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.financial.student-fees.student-financial-profile');
    }

}
