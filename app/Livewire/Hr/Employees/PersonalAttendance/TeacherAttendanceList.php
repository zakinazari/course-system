<?php

namespace App\Livewire\Hr\Employees\PersonalAttendance;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\CenterSettings\Branch;
use App\Models\Hr\Position;
use App\Models\Hr\Employee;
use App\Models\Hr\EmployeeAttendance;
use App\Models\Assessment\TeacherAttendance;
use App\Models\Academic\Course;

use App\Models\CenterSettings\Year;
use App\Models\CenterSettings\Month;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Auth;
use DB;

class TeacherAttendanceList extends Component
{
     // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'teacher-attendance-addEditModal';
    public $table_name='teacher_attendances';
    public $pdfOrientation = 'landscape';

    public $selectedFields = [
        'no',
        'employee_id',
        'status',
        'gross_salary',
        'absent_days',
        'taxi_fare',
        'credit_card',
        'tax',
        'advance_deduction',
        'net_salary',
        'payment_date',
    ];

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
    public $years = [];
    public $months = [];

    public $positions = [];
    public $employee;
    public $teacher_courses = [];
    public function mount($active_menu_id = null,$employee_id= null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->employee_id = $employee_id;
        $this->employee =  Employee::find($this->employee_id);
       
        $this->positions =  Position::all();
        $this->years =  Year::orderBy('year','desc')->get();
        $this->months =  Month::all();

        $now = Verta::now();

        $this->year = $now->year;
        $this->month = $now->month;

        $startOfYear = Verta::parse($this->year . '/01/01')->DateTime()->format('Y-m-d');
        $endOfYear   = Verta::parse($this->year . '/12/29')->endDay()->DateTime()->format('Y-m-d');

        $this->loadTeacherCourses();
    }

    public function loadTeacherCourses()
    {
        $startOfYear = Carbon::instance(
            Verta::parse($this->year . '/01/01')->DateTime()
        )->startOfDay();

        $endOfYear = Carbon::instance(
            Verta::parse(($this->year + 1) . '/01/01')->DateTime()
        )->subSecond(); // آخرین لحظه سال جاری

        $this->teacher_courses = Course::where('teacher_id', $this->employee_id)
            ->where('start_date', '<=', $endOfYear)
            ->where('end_date', '>=', $startOfYear)
            ->orderByDesc('id')
            ->get();
    }

    public function updatedYear($value)
    {
        $this->loadTeacherCourses();
    }

    public $position_id;
    public $employee_id;
    public $branch_id;

    public $year;
    public $month;
    public $attendance_id;
    public $status;
    public $note;
    public $attendance_date;
    
  
    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'employee',
            'employee_id',
            'years',
            'year',
            'month',
            'months',
            'teacher_courses',
        ]);
    }
    public $search = [

            'course_id' => null,
            'from' => null,
            'to' => null,
        ];



    public function render()
    {
        $search = $this->search;
        [$start, $end] = jalaliToGregorianMonthRange($this->year, $this->month);
        $attendances = TeacherAttendance::with([
            'course',
            'course.book:id,name',
            'course.time',
            'LeaveType',
        ])
        ->where('teacher_id',$this->employee->id)
        ->where('course_id',$search['course_id'])

        ->when(!empty($search['from']) && !empty($search['to']), function ($q) use ($search) {
            $q->whereBetween('attendance_date', [$search['from'], $search['to']]);
        }, function ($q) use ($start, $end) {
            $q->whereBetween('attendance_date', [$start, $end]);
        })

        ->orderBy('attendance_date','desc')
        ->paginate($this->perPage);

        return view('livewire.hr.employees.personal-attendance.teacher-attendance-list',compact('attendances'));
    }

    protected function rules()
    {
        return [

            'attendance_id' => 'required',
            'status' => 'required',
        ];
    }
    // Localized messages
    protected function messages()
    {
        return [
            'attendance_id.required' => __('label.attendance_id.required'),
            'status.required' => __('label.status.required'),
        ];
    }
    
    // Create role
    public function store()
    {
        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        $this->validate();

        try {


            // ---start system log-----------
          
            // ---end system log-------------
            $this->closeModal();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));
            
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.store_error') . ': ' . $e->getMessage());
        }
    }


    public function edit($id)
    {
       
        $this->resetValidation(); 
        $this->attendance_id = $id;    
        $attendance = TeacherAttendance::find($id);
        $this->status = $attendance->status;
        $this->editMode = true;

        $this->dispatch('open-modal', id: $this->modalId);
    }
    // Update role
    public function update()
    {
        if(!edit(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        $this->validate();

        DB::beginTransaction();

        try {
            
            $attendance = TeacherAttendance::with('teacher')->findOrFail($this->attendance_id);

            $oldStatus = $attendance->status;

            $attendance->update([
                'status' => $this->status,
                'note' => $this->note,
            ]);

            // ---start system log-----------
            SystemLog::create([
                's_id' => $attendance->employee_id,
                'user_id' => Auth::id(),
                'section' => __(
                    'label.teacher_attendance'
                ) .
                ' | Teacher: ' . $attendance->teacher?->name .
                ' | Teacher ID: ' . $attendance->teacher?->employee_code .
                ' | Attendance Date: ' . $attendance->attendance_date .
                ' | Status: ' . $oldStatus . ' → ' . $this->status .
                ' | Attendance ID: ' . $attendance->id,
                'type_id' => 3,
            ]);
            // ---end system log-------------

            DB::commit();

            $this->closeModal();

            $this->dispatch('alert', type: 'success', message: __('label.successfully_updated'));

        } catch (\Exception $e) {

            DB::rollBack();

            $this->dispatch('alert', type: 'error', message: __('label.update_error').' : '. $e->getMessage());
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
            $attendance = TeacherAttendance::findOrFail($id);
             // ---start system log-----------
            SystemLog::create([
                's_id' => $attendance->employee_id,
                'user_id' => Auth::id(),
                'section' => __(
                    'label.teacher_attendance'
                ) .
                ' | Teacher: ' . $attendance->teacher?->name .
                ' | Teacher ID: ' . $attendance->teacher?->employee_code .
                ' | Attendance Date: ' . $attendance->attendance_date .
                ' | Attendance ID: ' . $attendance->id,
                'type_id' => 4,
            ]);
            // ---end system log-------------
            $attendance->delete();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }
}
