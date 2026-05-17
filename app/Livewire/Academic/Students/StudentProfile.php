<?php

namespace App\Livewire\Academic\Students;

use Livewire\Component;

use App\Models\Settings\Menu;
use App\Models\Academic\Student;
use Auth;
use Carbon\Carbon;
use DB;
class StudentProfile extends Component
{
     // -------start generals------------

    public $active_menu_id;
    public $active_menu;
    public $student;
    // -------end generals-------------
    
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

    public $activeTab = 'student_courses';
    protected $queryString = [
        'activeTab'
    ];
    public function changeTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.academic.students.student-profile');
    }
}
