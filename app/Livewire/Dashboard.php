<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Settings\Menu;
use App\Models\User;
use App\Models\Academic\Student;
use App\Models\Hr\Employee;
use App\Models\CenterSettings\Branch;
use App\Models\Submissions\Review;
use Auth;
use DB;
class Dashboard extends Component
{
    public $active_menu_id = 1;
    public $active_menu;
    public $students=[];
    public $users=[];
    public $employees=[];
    public $branch_students = [];
    public $branch_labels = [];
    public function mount($active_menu_id = null)
    {
        // ---------- فعال کردن منو ----------
        $this->dispatch('setActiveMenuFromPage', 1);
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);

        // ---------- تعداد دانشجویان بر اساس وضعیت ----------
        $this->students = Student::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // اگر برخی وضعیت‌ها خالی هستند، صفر کنیم
        $statuses = ['new','active','inactive','suspended','graduated'];
        foreach ($statuses as $status) {
            if (!isset($this->students[$status])) {
                $this->students[$status] = 0;
            }
        }

        $branchData = Student::select('branch_id', DB::raw('count(*) as total'))
            ->where('status', 'active')
            ->groupBy('branch_id')
            ->pluck('total', 'branch_id')
            ->toArray();
        // گرفتن نام branch ها
        $branchLabels = Branch::whereIn('id', array_keys($branchData))
            ->pluck('name','id')
            ->toArray();
        foreach ($branchData as $id => $total) {
            $this->branch_labels[] = $branchLabels[$id] ?? "N/A";
            $this->branch_students[] = $total;
        }


        // -----------employees----------------------------
        $employees = Employee::with('employeeRoles')->get();
        // فقط معلم‌ها
        $teachersCount = $employees->filter(fn($emp) => $emp->isTeacher() && !$emp->isStaff())->count();

        // فقط کارکنان
        $staffsCount = $employees->filter(fn($emp) => $emp->isStaff() && !$emp->isTeacher())->count();

        // هم Teacher و هم Staff
        $teacherAndStaffCount = $employees->filter(fn($emp) => $emp->isTeacher() && $emp->isStaff())->count();

        // مجموع همه (هر کارمند با هر نقش)
        $totalCount = $employees->filter(fn($emp) => $emp->isTeacher() || $emp->isStaff())->count();

        $this->employees = [
            'teachers_only'       => $teachersCount,
            'staffs_only'         => $staffsCount,
            'teacher_and_staff'   => $teacherAndStaffCount,
            'total'               => $totalCount,
        ];

        // ---------- تعداد کاربران بر اساس نقش ----------
       
        $this->users = [
            'reviewer' => 2,
            'author' => 2,
            'admin' => 3,
            'all' => User::count(),
        ];
    }
    public function render()
    {
        return view('livewire.dashboard');
    }
}
