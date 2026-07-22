<?php

namespace App\Livewire\Dashboards\AcademicDashboards;

use Livewire\Component;
use App\Models\Settings\Menu;
use App\Models\User;
use App\Models\Academic\Student;
use App\Models\Academic\CourseStudent;
use App\Models\Hr\Employee;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Program;
use App\Models\CenterSettings\Book;
use App\Models\CenterSettings\Section;
use App\Models\CenterSettings\Shift;
use App\Models\CenterSettings\Gender;

use Auth;
use DB;
use Carbon\Carbon;
class ActiveStudentsDashboard extends Component
{
    public $active_menu_id = 1;
    public $active_menu;

    public $from_date;
    public $to_date;
    public $genders;
    public $gender;
    public $shifts;

    public $view_mode_general = 'dashboard';
    public $view_mode = 'dashboard';

    public $auth_branch_id = null;
    public $loadBranchSectionStudentData = null;
    public $selected_branch_name = null;

    public function mount($active_menu_id = null)
    {
         // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->from_date = now()->toDateString();
        $this->to_date = now()->toDateString();
        $this->auth_branch_id = Auth::user()?->branch_id;

        $this->shifts = Shift::all();
         $this->genders = Gender::all();
       $this->loadBranchStudentData();
    }

    public function render()
    {
        return view('livewire.dashboards.academic-dashboards.active-students-dashboard');
    }

    public function refreshData()
    {
        $this->loadBranchStudentData();

   
        switch ($this->view_mode) {

            case 'branch_section_student':
                $this->loadBranchSectionStudentData();
                break;

            case 'branch_section_program_student':
                $this->loadBranchSectionProgramStudentData();
                break;

            case 'branch_section_program_book_student':
                $this->loadBranchSectionProgramBookStudentData();
                
                break;

            case 'course_students':

                $this->loadBookCourseStudentData();

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

   public $branch_student_stats = [];

    public function loadBranchStudentData()
    {
        $branch_data = CourseStudent::query()
            ->where('status', 'active')
            ->with('course:id,branch_id')
             ->whereHas('course',function($query) {
                    $query->where('status','ongoing');
                })
            ->when($this->gender,function($query) {
                $query->whereHas('student',function($q) {
                    $q->where('gender_id',$this->gender);
                });
            })
            ->get()
            ->groupBy(fn ($course_student) => $course_student?->course?->branch_id)
            ->map(fn ($course_students) => $course_students->count())
            ->toArray();

        $branch_names = Branch::pluck('name', 'id')->toArray();


        $colors = [
            'primary',
            'success',
            'info',
            'warning',
            'danger',
            'secondary',
        ];

        $this->branch_student_stats = [];


        $total_all = array_sum($branch_data);

        $index = 0;

        foreach ($branch_data as $branch_id => $count) {

            $this->branch_student_stats[] = [
                'branch_id'   => $branch_id,
                'label'   => $branch_names[$branch_id] ?? __('label.unknown'),
                'count'   => $count,
                'percent' => $total_all > 0
                    ? round(($count / $total_all) * 100, 1)
                    : 0,
                'color'   => $colors[$index % count($colors)],
            ];

            $index++;
        }

        $this->loadSectionStudentData();
    }

    // -----------start brach section Data---------------------
    public $section_student_stats = [];

    public function loadSectionStudentData()
    {
        $section_data = CourseStudent::query()
            ->where('status', 'active')
            ->with('course.program:id,section_id')
            ->whereHas('course',function($query){
                    $query->where('status','ongoing');
                })
            ->when($this->gender,function($query) {
                $query->whereHas('student',function($q) {
                    $q->where('gender_id',$this->gender);
                });
            })
            ->get()
            ->groupBy(fn ($course_student) => $course_student?->course?->program?->section_id)
            ->map(fn ($course_students) => $course_students->count())
            ->toArray();

        $section_names = Section::pluck('name', 'id')->toArray();

        $this->section_student_stats = [];

        foreach ($section_data as $section_id => $count) {

            $this->section_student_stats[] = [
                'label' => $section_names[$section_id] ?? __('label.unknown'),
                'count' => $count,
            ];
        }
    }
    // -----------end Central Warehouse Data-----------------------


    // -------branch shift detaials ----------------------

    public $selected_branch_id;
    public function openBranchSectionStudent($branch_id)
    {
        $this->selected_branch_id = $branch_id;
        $this->selected_branch_name = Branch::findOrFail($branch_id)->name;
        $this->view_mode = 'branch_section_student';

        $this->loadBranchSectionStudentData();
    }
    public $branch_section_student_stats = [];
    public function loadBranchSectionStudentData()
    {
        
        $branch_id = $this->selected_branch_id;

        $section_data = CourseStudent::query()
                ->where('status', 'active')
                ->with('course.program:id,section_id')
                ->whereHas('course',function($query) use($branch_id){
                    $query->where('status','ongoing');
                    $query->where('branch_id',$branch_id);
                })
                ->when($this->gender,function($query) {
                    $query->whereHas('student',function($q) {
                        $q->where('gender_id',$this->gender);
                    });
                })
                ->get()
                ->groupBy(fn ($course_student) => $course_student?->course?->program?->section_id)
                ->map(fn ($course_students) => $course_students->count())
                ->toArray();

            $section_names = Section::pluck('name', 'id')->toArray();

            $this->branch_section_student_stats = [];

            foreach ($section_data as $section_id => $count) {

                $this->branch_section_student_stats[] = [
                    'section_id' => $section_id,
                    'label' => $section_names[$section_id] ?? __('label.unknown'),
                    'count' => $count,
                ];
            }
    }


    public function backToDashboard()
    {
        $this->view_mode_general = 'dashboard';
        $this->view_mode = 'dashboard';

        $this->branch_section_student_stats = null;

        $this->loadBranchStudentData();
    }


    // -------------sechtion details-----------------------------------

    public $selected_section_id = null;
    public $selected_section_name = null;

    public $branch_section_program_student_stats = [];
    
    public function openBranchSectionProgaramStudent($section_id)
    {
        $this->selected_section_id = $section_id;

        $this->selected_section_name = Section::findOrFail($section_id)->name;

        $this->view_mode = 'branch_section_program_student';

        $this->loadBranchSectionProgramStudentData();
    }

    public function loadBranchSectionProgramStudentData()
    {
        $branch_id = $this->selected_branch_id;
        $section_id = $this->selected_section_id;

        $program_data = CourseStudent::query()
            ->where('status', 'active')
            ->whereHas('course', function ($query) use ($branch_id, $section_id) {

                $query->where('status', 'ongoing')
                    ->where('branch_id', $branch_id)
                    ->whereHas('program', function ($query) use ($section_id) {
                        $query->where('section_id', $section_id);
                    });

            })
            ->when($this->gender,function($query) {
                $query->whereHas('student',function($q) {
                    $q->where('gender_id',$this->gender);
                });
            })
            ->get()
            ->groupBy(fn ($course_student) => $course_student->course->program_id)
            ->map(fn ($course_students) => $course_students->count())
            ->toArray();

        $program_names = Program::pluck('name', 'id')->toArray();

        $this->branch_section_program_student_stats = [];

        foreach ($program_data as $program_id => $count) {

            $this->branch_section_program_student_stats[] = [
                'program_id' => $program_id,
                'label'      => $program_names[$program_id] ?? __('label.unknown'),
                'count'      => $count,
            ];
        }
    }

    public function backToBranchSectionStudent()
    {
        $this->view_mode = 'branch_section_student';

        $this->loadBranchSectionStudentData();
    }

    // --------------branch,section,program,book students-------------------
    public $selected_program_id = null;
    public $selected_program_name = null;

    public $branch_section_program_book_student_stats = [];
    
    public function openBranchSectionProgaramBookStudent($program_id)
    {
        $this->selected_program_id = $program_id;

        $this->selected_program_name = Program::findOrFail($program_id)->name;

        $this->view_mode = 'branch_section_program_book_student';

        $this->loadBranchSectionProgramBookStudentData();
    }

    public function loadBranchSectionProgramBookStudentData()
    {
        $branch_id = $this->selected_branch_id;
        $section_id = $this->selected_section_id;
        $program_id = $this->selected_program_id;

        $book_data = CourseStudent::query()
            ->where('status', 'active')
            ->whereHas('course', function ($query) use ($branch_id, $section_id, $program_id) {

                $query->where('status', 'ongoing')
                    ->where('branch_id', $branch_id)
                    ->where('program_id', $program_id)
                    ->whereHas('program', function ($query) use ($section_id) {
                        $query->where('section_id', $section_id);
                    });

            })
            ->when($this->gender,function($query) {
                $query->whereHas('student',function($q) {
                    $q->where('gender_id',$this->gender);
                });
            })
            ->with('course:id,book_id')
            ->get()
            ->groupBy(fn ($course_student) => $course_student->course->book_id)
            ->map(fn ($course_students) => $course_students->count());

        $books = Book::query()
            ->orderBy('level_number')
            ->get(['id', 'name', 'level_number']);

        $this->branch_section_program_book_student_stats = [];

        foreach ($books as $book) {

            if (!isset($book_data[$book->id])) {
                continue;
            }

            $this->branch_section_program_book_student_stats[] = [
                'book_id' => $book->id,
                'label'   => $book->name,
                'count'   => $book_data[$book->id],
            ];
        }
    }

    public function backToBranchSectionProgramStudent()
    {
        $this->view_mode = 'branch_section_program_student';

        $this->loadBranchSectionProgramStudentData();
    }


    // ---------branch,section,program,book, student course list -----------------------------

    public $selected_book_id = null;
    public $selected_book_name = null;
    public $selected_shift_id = null;
    
    public function openBookStudents($book_id)
    {
        $book = Book::findOrFail($book_id);

        $this->selected_book_id = $book->id;
        $this->selected_book_name = $book->name;

        $this->view_mode = 'course_students';

        $this->loadBookCourseStudentData();
    }

    public function updatedSelectedShiftId(){

        $this->loadBookCourseStudentData();

    }

    public $branch_section_program_book_course_students = [];

    public function loadBookCourseStudentData()
    {
        $branch_id = $this->selected_branch_id;
        $section_id = $this->selected_section_id;
        $program_id = $this->selected_program_id;
        $book_id = $this->selected_book_id;
        $shift_id = $this->selected_shift_id;

        $course_data = CourseStudent::query()
            ->where('status', 'active')
            ->whereHas('course', function ($query) use (
                $branch_id,
                $section_id,
                $program_id,
                $book_id,
                $shift_id
            ) {

                $query->where('status', 'ongoing')
                    ->where('branch_id', $branch_id)
                    ->where('program_id', $program_id)
                    ->where('book_id', $book_id)
                    ->when($shift_id,function($q) use($shift_id){
                        $q->where('shift_id',$shift_id);
                    })
                    ->whereHas('program', function ($query) use ($section_id) {
                        $query->where('section_id', $section_id);
                    });

            })
            ->with(['course:id,name,shift_id', 'course.shift:id,name'])
            ->when($this->gender,function($query) {
                $query->whereHas('student',function($q) {
                    $q->where('gender_id',$this->gender);
                });
            })
            ->get()
            ->groupBy(fn ($course_student) => $course_student->course_id)
            ->map(function ($course_students) {

                $first = $course_students->first();

                return [
                    'course_name'  => $first->course->name,
                    'shift_name'   => $first->course?->shift?->name ?? __('label.unknown_shift'),
                    'student_count'=> $course_students->count(),
                ];
            })
            ->values()
            ->toArray();

        $this->branch_section_program_book_course_students = $course_data;
    }

    
    public function backToBranchSectionProgramBookStudent()
    {
        $this->view_mode = 'branch_section_program_book_student';

        $this->loadBranchSectionProgramBookStudentData();
    }
}
