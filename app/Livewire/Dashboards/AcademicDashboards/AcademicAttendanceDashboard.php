<?php

namespace App\Livewire\Dashboards\AcademicDashboards;

use Livewire\Component;
use App\Models\Settings\Menu;
use App\Models\User;
use App\Models\Academic\Student;
use App\Models\Academic\Course;
use App\Models\Assessment\StudentAttendance;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Shift;
use App\Models\CenterSettings\Gender;
use App\Models\Submissions\Review;
use Auth;
use DB;
class AcademicAttendanceDashboard extends Component
{
    public $active_menu_id;
    public $active_menu;

    public $from_date;
    public $to_date;
    public $genders;
    public $gender;

    public $view_mode = 'dashboard';

    public $auth_branch_id = null;
    public $selected_branch_id = null;
    public $selected_branch_name = null;

    public function mount($active_menu_id = null)
    {
         // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->genders = Gender::all();
        $this->from_date = now()->toDateString();
        $this->to_date = now()->toDateString();
        $this->auth_branch_id = Auth::user()?->branch_id;
        $this->loadAttendanceData();

    }

    public function render()
    {

        return view('livewire.dashboards.academic-dashboards.academic-attendance-dashboard');
    }


    public function refreshData()
    {
      
        $this->loadAttendanceData();
        $this->loadBranchAttendanceData();

        switch ($this->view_mode) {

            case 'branch_shift':
                $this->loadBranchShiftAttendanceData();
                break;

            case 'shift_course':
                $this->loadShiftCourseAttendanceData();
                break;

            case 'course_students':
                $this->loadCourseStudents();
                break;

            case 'student_attendance':
                $this->loadStudentAttendanceRecords();
                break;
        }
    }

    public function updatedFromDate()
    {
        $this->refreshData();
    }

    public function updatedToDate()
    {
        $this->refreshData();
    }

    public function updatedGender()
    {
        $this->refreshData();
    }

    public $attendance_stats = [];

    public function loadAttendanceData()
    {
        
        $status_data = StudentAttendance::select('status', DB::raw('count(*) as total'))
            ->whereBetween('attendance_date', [$this->from_date, $this->to_date])
            ->when($this->auth_branch_id,function($query) {
                $query->whereHas('course',function($q) {

                    $q->where('status', 'ongoing');

                    $q->where('branch_id',$this->auth_branch_id);
                });
            })
            ->when($this->gender,function($query) {
                $query->whereHas('student',function($q) {
                    $q->where('gender_id',$this->gender);
                });
            })
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $present = $status_data['present'] ?? 0;
        $absent = $status_data['absent'] ?? 0;
        $late = $status_data['late'] ?? 0;
        $excused = $status_data['excused'] ?? 0;

        $total = $present + $absent + $late + $excused;

        $this->attendance_stats = [
            [
                'label' => 'Present',
                'count' => $present,
                'percent' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
                'color' => 'success',
            ],
            [
                'label' => 'Absent',
                'count' => $absent,
                'percent' => $total > 0 ? round(($absent / $total) * 100, 1) : 0,
                'color' => 'danger',
            ],
            [
                'label' => 'Late',
                'count' => $late,
                'percent' => $total > 0 ? round(($late / $total) * 100, 1) : 0,
                'color' => 'warning',
            ],
            [
                'label' => 'Excused',
                'count' => $excused,
                'percent' => $total > 0 ? round(($excused / $total) * 100, 1) : 0,
                'color' => 'info',
            ],
        ];

        $this->loadBranchAttendanceData();

    }

    public $branch_attendance_stats  = [];

    public function loadBranchAttendanceData()
    {
        $branches = Branch::query()
        ->when($this->auth_branch_id, function($q){
            $q->where('id',$this->auth_branch_id);
        })
        ->get();

        $this->branch_attendance_stats = [];

        foreach ($branches as $branch) {

            $status_counts = StudentAttendance::query()
                ->select('status', DB::raw('count(*) as total'))
                ->whereHas('course', function ($q) use ($branch) {
                    $q->where('branch_id', $branch->id);
                    $q->where('status', 'ongoing');
                })
                ->whereBetween('attendance_date', [$this->from_date, $this->to_date])
                ->when($this->gender, function ($query) {
                    $query->whereHas('student', function ($q) {
                        $q->where('gender_id', $this->gender);
                    });
                })
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();

            $present = $status_counts['present'] ?? 0;
            $absent = $status_counts['absent'] ?? 0;
            $late = $status_counts['late'] ?? 0;
            $excused = $status_counts['excused'] ?? 0;

            $this->branch_attendance_stats[] = [
                'branch_id' => $branch->id,
                'branch_name' => $branch->name,
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'excused' => $excused,
                'total' => $present + $absent + $late + $excused,
            ];
        }
    }

    // -------branch shift detaials ----------------------

    
    public function openBranchShiftDetails($branch_id)
    {
        $this->selected_branch_id = $branch_id;
        $this->selected_branch_name = Branch::findOrFail($branch_id)->name;
        $this->view_mode = 'branch_shift';

        $this->loadBranchShiftAttendanceData();
    }

    public function loadBranchShiftAttendanceData()
    {
        $shifts = Shift::query()->get();
        
        $this->branch_attendance_stats = [];

        foreach ($shifts as $shift) {

            $status_counts = StudentAttendance::query()
                ->select('status', DB::raw('count(*) as total'))
                ->whereHas('course', function ($q) use($shift){
                    $q->when($this->selected_branch_id,function($qqq) {
                            $qqq->where('branch_id',$this->selected_branch_id);
                            $qqq->where('status', 'ongoing');
                    });

                    $q->where('shift_id', $shift->id);
                })
                
                ->whereBetween('attendance_date', [$this->from_date, $this->to_date])
                ->when($this->gender, function ($query) {
                    $query->whereHas('student', function ($q) {
                        $q->where('gender_id', $this->gender);
                    });
                })
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();

            $present = $status_counts['present'] ?? 0;
            $absent = $status_counts['absent'] ?? 0;
            $late = $status_counts['late'] ?? 0;
            $excused = $status_counts['excused'] ?? 0;

            $this->branch_attendance_stats[] = [
                'shift_id' => $shift->id,
                'shift_name' => $shift->name,

                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'excused' => $excused,

                'total' => $present + $absent + $late + $excused,
            ];
        }
    }


    public function backToDashboard()
    {
        $this->view_mode = 'dashboard';

        $this->selected_branch_id = null;
        $this->selected_branch_name = null;

        $this->loadBranchAttendanceData();
    }


    // -------------shift course details ----------------------------

    public $selected_shift_id = null;
    public $selected_shift_name = null;

    public $shift_course_stats = []; 


    public function openShiftCourseDetails($shift_id)
    {
        $this->selected_shift_id = $shift_id;

        $this->selected_shift_name = Shift::findOrFail($shift_id)->name;

        $this->view_mode = 'shift_course';

        $this->loadShiftCourseAttendanceData();
    }

    public function loadShiftCourseAttendanceData()
    {
        $courses = Course::query()
        ->when($this->selected_branch_id,function($query){
            $query->where('branch_id',$this->selected_branch_id);
        })
        ->where('shift_id', $this->selected_shift_id)
        ->where('status', 'ongoing')
         
        ->get();
        
        $this->shift_course_stats = [];

        foreach ($courses as $course) {
            $attendance_taken_today = StudentAttendance::where('course_id', $course->id)
            ->whereDate('attendance_date', today())
            ->exists();

            $status_counts = StudentAttendance::query()
                ->select('status', DB::raw('count(*) as total'))
                ->whereHas('course', function ($q) use ($course) {
                    $q->where('id', $course->id)
                    ->where('branch_id', $this->selected_branch_id)
                    ->where('shift_id', $this->selected_shift_id);
                })
                ->whereBetween('attendance_date', [$this->from_date, $this->to_date])
                ->when($this->gender, function ($query) {
                    $query->whereHas('student', function ($q) {
                        $q->where('gender_id', $this->gender);
                    });
                })
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();

            $present = $status_counts['present'] ?? 0;
            $absent = $status_counts['absent'] ?? 0;
            $late = $status_counts['late'] ?? 0;
            $excused = $status_counts['excused'] ?? 0;

            $this->shift_course_stats[] = [
                'course_id' => $course->id,
                'course_name' => $course->name,
                'mid_exam_date' => $course->mid_exam_date,
                'final_exam_date' => $course->final_exam_date,

                'attendance_taken_today' => $attendance_taken_today,

                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'excused' => $excused,

                'total' => $present + $absent + $late + $excused,
            ];
        }
    }

    public function backToBranchShift()
    {
        $this->view_mode = 'branch_shift';

        $this->loadBranchShiftAttendanceData();
    }


    // course student details---------------------------------------------

    public $selected_course_id = null;
    public $selected_course_name = null;

    public $course_students = [];

    public function openCourseStudents($course_id)
    {
        $course = Course::findOrFail($course_id);

        $this->selected_course_id = $course->id;
        $this->selected_course_name = $course->name;

        $this->view_mode = 'course_students';

        $this->loadCourseStudents();
    }

    public function loadCourseStudents()
    {
        $this->course_students = StudentAttendance::query()
            ->select(
                'student_id',
                DB::raw("SUM(CASE WHEN status='present' THEN 1 ELSE 0 END) as present"),
                DB::raw("SUM(CASE WHEN status='absent' THEN 1 ELSE 0 END) as absent"),
                DB::raw("SUM(CASE WHEN status='late' THEN 1 ELSE 0 END) as late"),
                DB::raw("SUM(CASE WHEN status='excused' THEN 1 ELSE 0 END) as excused")
            )
            ->with('student')
            ->when($this->gender, function ($query) {
                $query->whereHas('student', function ($q) {
                    $q->where('gender_id', $this->gender);
                });
            })
            ->whereHas('course', function ($q) {
                $q->where('id', $this->selected_course_id);
            })
            ->whereBetween('attendance_date', [
                $this->from_date,
                $this->to_date
            ])
            ->groupBy('student_id')
            ->get();
    }

    public function backToShiftCourse()
    {
        $this->view_mode = 'shift_course';

        $this->loadShiftCourseAttendanceData();
    }

    // student attendance records ----------------------------------

    public $selected_student_id = null;
    public $selected_student_name = null;
    public $selected_student_code = null;

    public $student_attendance_records = [];

    public function openStudentAttendanceDetails($student_id)
    {
        $student = Student::findOrFail($student_id);

        $this->selected_student_id = $student_id;
        $this->selected_student_name = $student->name;
        $this->selected_student_code = $student->student_code;

        $this->view_mode = 'student_attendance';

        $this->loadStudentAttendanceRecords();
    }

    public function loadStudentAttendanceRecords()
    {
        $this->student_attendance_records = StudentAttendance::query()
            ->with('student:id,student_code,name')
            ->where('student_id', $this->selected_student_id)
            ->whereHas('course', function ($q) {
                $q->where('id', $this->selected_course_id);
            })
            ->whereBetween('attendance_date', [
                $this->from_date,
                $this->to_date
            ])
            ->orderBy('attendance_date')
            ->get();
    }

    public function backToCourseStudents()
    {
        $this->view_mode = 'course_students';

        $this->loadCourseStudents();
    }

}
