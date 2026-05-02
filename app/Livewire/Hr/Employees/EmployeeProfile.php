<?php

namespace App\Livewire\Hr\Employees;

use Livewire\Component;
use App\Models\Settings\Menu;
use App\Models\Hr\Employee;
use Auth;
use Carbon\Carbon;
use DB;
class EmployeeProfile extends Component
{
    
    // -------start generals------------

    public $active_menu_id;
    public $active_menu;
    public $employee;
    // -------end generals-------------
    
    public function mount($active_menu_id = null, $employee_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------
        $employee_id = decrypt($employee_id);
        $this->employee =Employee::with('photo')->findOrFail($employee_id);
    }

    public $activeTab = 'temporary_contract';
    protected $queryString = [
        'activeTab'
    ];
    public function changeTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.hr.employees.employee-profile');
    }
}
