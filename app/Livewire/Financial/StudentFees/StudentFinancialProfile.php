<?php

namespace App\Livewire\Financial\StudentFees;

use Livewire\Component;
use App\Models\Settings\Menu;
use App\Models\Academic\Student;
use App\Models\Academic\Course; 
use App\Models\Financial\FeeType; 
use Auth;
use Carbon\Carbon;
use DB;
class StudentFinancialProfile extends Component
{
    
    // -------start generals------------

    protected $paginationTheme = 'bootstrap';   
    public $active_menu_id;
    public $active_menu;
    public $student;
    public $fee_types;
    // -------end generals-------------
    
    public function mount($active_menu_id = null, $student_id = null,$slug = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        if($slug){
            $this->active_menu_id = Menu::where('slug',$slug)->value('id');
        }else{
            $this->active_menu_id = $active_menu_id;
        }
          $this->dispatch('setActiveMenuFromPage', $this->active_menu_id);
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($this->active_menu_id);
        // -------------start for activing menu in sidebar ----------------------
        $student_id = decrypt($student_id);
        $this->student =Student::with('photo')->findOrFail($student_id);
    }

    public $activeTab = 'course_fee';
    protected $queryString = [
        'activeTab'
    ];
    public function changeTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.financial.student-fees.student-financial-profile');
    }
}
