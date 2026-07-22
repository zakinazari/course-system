<?php

namespace App\Livewire\Dashboards\AcademicDashboards;

use Livewire\Component;
use App\Models\Settings\Menu;
use App\Models\User;
use App\Models\Academic\Student;
use App\Models\Academic\Course;
use App\Models\Academic\CourseWaitingList;
use App\Models\Academic\CourseWaitingListComment;
use App\Models\Academic\CourseStudent;
use App\Models\Assessment\StudentCourseResult;
use App\Models\Assessment\StudentExamScore;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Shift;
use App\Models\CenterSettings\Program;
use App\Models\CenterSettings\Book;
use App\Models\CenterSettings\Gender;
use App\Models\Submissions\Review;
use Auth;
use DB;
use Carbon\Carbon;
class AcademicResultsDashboard extends Component
{
    public $active_menu_id;
    public $active_menu;

    public $from_date;
    public $to_date;
    public $genders;
    public $gender;
    public $shifts;

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
        $this->shifts = Shift::all();
        $this->from_date = now()->toDateString();
        $this->to_date = now()->toDateString();
        $this->auth_branch_id = Auth::user()?->branch_id;
        $this->loadResultsData();

    }

    public function render()
    {

        return view('livewire.dashboards.academic-dashboards.academic-results-dashboard');
    }


    public function refreshData()
    {
      
        $this->loadResultsData();
        $this->loadBranchResultsData();

        switch ($this->view_mode) {

            case 'branch_shift':
                $this->loadBranchShiftResultsData();
                break;

            case 'shift_course':
                $this->loadShiftCourseResultsData();
                break;
            case 'program_book':
                $this->loadProgramBookResultsData();
                break;

            case 'course_students':
                $this->loadCourseStudents();
                break;

            case 'student_results':
                $this->loadStudentResultsDetails();
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

    public $results_stats = [];

    public function loadResultsData()
    {
        $query = CourseWaitingList::query()
            ->whereBetween('created_at', [
                Carbon::parse($this->from_date)->startOfDay(),
                Carbon::parse($this->to_date)->endOfDay(),
            ])

            ->when($this->auth_branch_id, function ($q) {
                $q->where('branch_id', $this->auth_branch_id);
            })

            ->when($this->gender, function ($q) {
                $q->whereHas('student', function ($sq) {
                    $sq->where('gender_id', $this->gender);
                });
            });

        // counts
        $placement = (clone $query)->where('status', 'placement')->count();
        $passed = (clone $query)->where('status', 'passed')->count();
        $failed = (clone $query)->where('status', 'failed')->count();
        $makeup = (clone $query)->where('status', 'makeup')->count();
        $dropped = (clone $query)->where('status', 'dropped')->count();

        $total = $passed + $failed + $makeup + $dropped + $placement;

        $this->results_stats = [
            [
                'label' => __('label.placement'),
                'count' => $placement,
                'percent' => $total > 0 ? round(($placement / $total) * 100, 1) : 0,
                'color' => 'info',
            ],
            [
                'label' => __('label.passed'),
                'count' => $passed,
                'percent' => $total > 0 ? round(($passed / $total) * 100, 1) : 0,
                'color' => 'success',
            ],
            [
                'label' => __('label.failed'),
                'count' => $failed,
                'percent' => $total > 0 ? round(($failed / $total) * 100, 1) : 0,
                'color' => 'danger',
            ],
            [
                'label' => __('label.makeup'),
                'count' => $makeup,
                'percent' => $total > 0 ? round(($makeup / $total) * 100, 1) : 0,
                'color' => 'warning',
            ],
            [
                'label' => __('label.dropped'),
                'count' => $dropped,
                'percent' => $total > 0 ? round(($dropped / $total) * 100, 1) : 0,
                'color' => 'secondary',
            ],
        ];

        $this->loadBranchResultsData();
    }

    public $branch_results_stats  = [];

    public function loadBranchResultsData()
    {
        $branches = Branch::query()
            ->when($this->auth_branch_id, function ($q) {
                $q->where('id', $this->auth_branch_id);
            })
            ->get();

        $this->branch_results_stats = [];

        foreach ($branches as $branch) {

            $baseQuery = CourseWaitingList::query()
                ->where('branch_id', $branch->id)
                ->whereBetween('created_at', [
                    Carbon::parse($this->from_date)->startOfDay(),
                    Carbon::parse($this->to_date)->endOfDay(),
                ])

                ->when($this->gender, function ($q) {
                    $q->whereHas('student', function ($sq) {
                        $sq->where('gender_id', $this->gender);
                    });
                });

            // counts
            $passed = (clone $baseQuery)->where('status', 'passed')->count();
            $failed = (clone $baseQuery)->where('status', 'failed')->count();
            $makeup = (clone $baseQuery)->where('status', 'makeup')->count();
            $dropped = (clone $baseQuery)->where('status', 'dropped')->count();
            $placement = (clone $baseQuery)->where('status', 'placement')->count();

            $this->branch_results_stats[] = [
                'branch_id' => $branch->id,
                'branch_name' => $branch->name,

                'placement' => $placement,
                'passed' => $passed,
                'failed' => $failed,
                'makeup' => $makeup,
                'dropped' => $dropped,

                'total' => $passed + $failed + $makeup + $dropped + $placement,
            ];
        }
    }

    // -------branch shift detaials ----------------------

    
    // public function openBranchShiftDetails($branch_id)
    // {
    //     $this->selected_branch_id = $branch_id;
    //     $this->selected_branch_name = Branch::findOrFail($branch_id)->name;
    //     $this->view_mode = 'branch_shift';

    //     $this->loadBranchShiftResultsData();
    // }

//    public function loadBranchShiftResultsData()
//     {
//         $shift_ids = CourseWaitingList::query()

//             ->when($this->selected_branch_id, function ($q) {
//                 $q->where('branch_id', $this->selected_branch_id);
//             })

//             ->whereBetween('created_at', [
//                 Carbon::parse($this->from_date)->startOfDay(),
//                 Carbon::parse($this->to_date)->endOfDay(),
//             ])

//             ->when($this->gender, function ($q) {
//                 $q->whereHas('student', function ($sq) {
//                     $sq->where('gender_id', $this->gender);
//                 });
//             })

//             ->distinct()
//             ->pluck('shift_id');

//         $shifts = Shift::whereIn('id', $shift_ids)->get();

//         $this->branch_results_stats = [];

//         foreach ($shifts as $shift) {

//             $baseQuery = CourseWaitingList::query()

//                 ->where('shift_id', $shift->id)

//                 ->when($this->selected_branch_id, function ($q) {
//                     $q->where('branch_id', $this->selected_branch_id);
//                 })

//                 ->whereBetween('created_at', [
//                     Carbon::parse($this->from_date)->startOfDay(),
//                     Carbon::parse($this->to_date)->endOfDay(),
//                 ])

//                 ->when($this->gender, function ($q) {
//                     $q->whereHas('student', function ($sq) {
//                         $sq->where('gender_id', $this->gender);
//                     });
//                 });

//             $placement = (clone $baseQuery)
//                 ->where('status', 'placement')
//                 ->count();

//             $passed = (clone $baseQuery)
//                 ->where('status', 'passed')
//                 ->count();

//             $failed = (clone $baseQuery)
//                 ->where('status', 'failed')
//                 ->count();

//             $makeup = (clone $baseQuery)
//                 ->where('status', 'makeup')
//                 ->count();

//             $dropped = (clone $baseQuery)
//                 ->where('status', 'dropped')
//                 ->count();

//             $this->branch_results_stats[] = [
//                 'shift_id' => $shift->id,
//                 'shift_name' => $shift->name,

//                 'placement' => $placement,
//                 'passed' => $passed,
//                 'failed' => $failed,
//                 'makeup' => $makeup,
//                 'dropped' => $dropped,

//                 'total' => $placement + $passed + $failed + $makeup + $dropped,
//             ];
//         }
//     }


    public function backToDashboard()
    {
        $this->view_mode = 'dashboard';

        $this->selected_branch_id = null;
        $this->selected_branch_name = null;

        $this->loadBranchResultsData();
    }


    // -------------shift course details ----------------------------

    public $selected_shift_id = null;
    public $selected_shift_name = null;

    public $shift_course_stats = []; 


    public function openBranchProgramDetails($branch_id)
    {
        $this->selected_branch_id = $branch_id;
        $this->selected_branch_name = Branch::findOrFail($branch_id)->name;

        $this->view_mode = 'shift_course';

        $this->loadShiftCourseResultsData();
    }

    public function loadShiftCourseResultsData()
    {
        $program_ids = CourseWaitingList::query()

            ->when($this->selected_branch_id, function ($q) {
                $q->where('branch_id', $this->selected_branch_id);
            })

            ->whereBetween('created_at', [
                Carbon::parse($this->from_date)->startOfDay(),
                Carbon::parse($this->to_date)->endOfDay(),
            ])

            ->when($this->gender, function ($q) {
                $q->whereHas('student', function ($sq) {
                    $sq->where('gender_id', $this->gender);
                });
            })

            ->distinct()
            ->pluck('program_id');

        $programs = Program::whereIn('id', $program_ids)->get();

        $this->shift_course_stats = [];

        foreach ($programs as $program) {

            $baseQuery = CourseWaitingList::query()

                ->where('program_id', $program->id)

                ->when($this->selected_branch_id, function ($q) {
                    $q->where('branch_id', $this->selected_branch_id);
                })

                ->whereBetween('created_at', [
                    Carbon::parse($this->from_date)->startOfDay(),
                    Carbon::parse($this->to_date)->endOfDay(),
                ])

                ->when($this->gender, function ($q) {
                    $q->whereHas('student', function ($sq) {
                        $sq->where('gender_id', $this->gender);
                    });
                });

            $placement = (clone $baseQuery)
                ->where('status', 'placement')
                ->count();

            $passed = (clone $baseQuery)
                ->where('status', 'passed')
                ->count();

            $failed = (clone $baseQuery)
                ->where('status', 'failed')
                ->count();

            $makeup = (clone $baseQuery)
                ->where('status', 'makeup')
                ->count();

            $dropped = (clone $baseQuery)
                ->where('status', 'dropped')
                ->count();

            $this->shift_course_stats[] = [
                'program_id' => $program->id,
                'program_name' => $program->name,

                'placement' => $placement,
                'passed' => $passed,
                'failed' => $failed,
                'makeup' => $makeup,
                'dropped' => $dropped,

                'total' => $placement + $passed + $failed + $makeup + $dropped,
            ];
        }
    }

    public function backToBranchShift()
    {
        $this->view_mode = 'branch_shift';

        $this->loadBranchShiftResultsData();
    }
    

    // نظربه کتاب-------------------
    public $program_book_stats = [];
    public function loadProgramBookResultsData()
    {
        $book_ids = CourseWaitingList::query()

            ->where('program_id', $this->selected_program_id)

            ->when($this->selected_branch_id, function ($q) {
                $q->where('branch_id', $this->selected_branch_id);
            })

            // ->when($this->selected_shift_id, function ($q) {
            //     $q->where('shift_id', $this->selected_shift_id);
            // })

            ->whereBetween('created_at', [
                Carbon::parse($this->from_date)->startOfDay(),
                Carbon::parse($this->to_date)->endOfDay(),
            ])

            ->when($this->gender, function ($q) {
                $q->whereHas('student', function ($sq) {
                    $sq->where('gender_id', $this->gender);
                });
            })

            ->distinct()
            ->pluck('book_id');

        $books = Book::whereIn('id', $book_ids)->get();

        $this->program_book_stats = [];

        foreach ($books as $book) {

            $baseQuery = CourseWaitingList::query()

                ->where('book_id', $book->id)

                ->where('program_id', $this->selected_program_id)

                ->when($this->selected_branch_id, function ($q) {
                    $q->where('branch_id', $this->selected_branch_id);
                })

                // ->when($this->selected_shift_id, function ($q) {
                //     $q->where('shift_id', $this->selected_shift_id);
                // })

                ->whereBetween('created_at', [
                    Carbon::parse($this->from_date)->startOfDay(),
                    Carbon::parse($this->to_date)->endOfDay(),
                ])

                ->when($this->gender, function ($q) {
                    $q->whereHas('student', function ($sq) {
                        $sq->where('gender_id', $this->gender);
                    });
                });

            $placement = (clone $baseQuery)->where('status', 'placement')->count();
            $passed    = (clone $baseQuery)->where('status', 'passed')->count();
            $failed    = (clone $baseQuery)->where('status', 'failed')->count();
            $makeup    = (clone $baseQuery)->where('status', 'makeup')->count();
            $dropped   = (clone $baseQuery)->where('status', 'dropped')->count();

            $this->program_book_stats[] = [
                'book_id' => $book->id,
                'book_name' => $book->name,

                'placement' => $placement,
                'passed' => $passed,
                'failed' => $failed,
                'makeup' => $makeup,
                'dropped' => $dropped,

                'total' => $placement + $passed + $failed + $makeup + $dropped,
            ];
        }
    }



    public $selected_program_id;
    public $selected_program_name;

    public function openProgramStudents($program_id)
    {
        $program = Program::findOrFail($program_id);

        $this->selected_program_id = $program->id;
        $this->selected_program_name = $program->name;

        $this->view_mode = 'program_book';

        $this->loadProgramBookResultsData();
    }

    public function backToProgramResults()
    {
        $this->view_mode = 'shift_course';

        $this->loadShiftCourseResultsData();
    }

    // course student details---------------------------------------------


   public $selected_book_id = null;
    public $selected_book_name = null;

    public $course_students = [];

    public function openBookStudents($book_id)
    {
        $book = Book::findOrFail($book_id);

        $this->selected_book_id = $book->id;
        $this->selected_book_name = $book->name;

        $this->view_mode = 'course_students';

        $this->loadCourseStudents();
    }

    public function updatedSelectedShiftId(){

        $this->selected_shift_name = Shift::find($this->selected_shift_id)?->name;

        $this->loadCourseStudents();

    }

    public function loadCourseStudents()
    {
        $this->course_students = CourseWaitingList::query()

            ->with([
                'student',
                'branch',
                'program',
                'book',
                'shift',
                'comments',
            ])

            ->where('book_id', $this->selected_book_id)

            ->when($this->selected_program_id, function ($q) {
                $q->where('program_id', $this->selected_program_id);
            })

            ->when(!empty($this->selected_shift_id), function ($q) {
                $q->where('shift_id', $this->selected_shift_id);
            })

            ->when($this->selected_branch_id, function ($q) {
                $q->where('branch_id', $this->selected_branch_id);
            })

            ->when($this->gender, function ($query) {
                $query->whereHas('student', function ($q) {
                    $q->where('gender_id', $this->gender);
                });
            })

            ->whereBetween('created_at', [
                Carbon::parse($this->from_date)->startOfDay(),
                Carbon::parse($this->to_date)->endOfDay(),
            ])

            ->latest('created_at')

            ->get();
    }


    public function backToBook()
    {
        $this->view_mode = 'program_book';

        $this->loadProgramBookResultsData();
    }

    // student attendance records ----------------------------------

    public $selected_student_id = null;
    public $selected_student_name = null;
    public $selected_student_code = null;

    public $student_results_records = [];

    public function openStudentResultsDetails($student_id)
    {
        $student = Student::findOrFail($student_id);

        $this->selected_student_id = $student_id;
        $this->selected_student_name = $student->name;
        $this->selected_student_code = $student->student_code;

        $this->view_mode = 'student_results';

        $this->loadStudentResultsDetails();
    }

    public $show_comments=[];
    public $selected_waiting_id;
    public function showComments($waiting_id)
    {
        $this->selected_waiting_id = $waiting_id;
       
        $this->show_comments = CourseWaitingListComment::where('course_waiting_list_id',$waiting_id)->get();

        $this->dispatch('open-modal', id: 'show_comments_modal');
    }
  

}
