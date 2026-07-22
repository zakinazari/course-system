<?php

namespace App\Livewire\Academic\Students\StudentCourses;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Book;
use App\Models\Academic\Student;
use App\Models\Academic\CourseWaitingList;
use App\Models\Academic\CourseStudent;
use App\Models\Academic\Course;
use App\Models\Assessment\StudentCourseResult;
use App\Models\Assessment\StudentExamScore;

use App\Models\Assessment\StudentExamScoreLog;
use App\Models\Assessment\StudentCourseResultLog;
use Auth;
use DB;
class StudentCourseResultList extends Component
{
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'student-course-list-addEditModal';
    public $table_name='student_course_list';
    public $selectedFields = [];
    public $pdfOrientation = 'landscape';
    public $branches=[];

    protected $listeners = ['modalClosed' => 'closeModal','globalDelete' => 'handleGlobalDelete'];

    public function closeModal(){
        $this->resetInputFields();
        $this->resetValidation();
        $this->dispatch('close-modal', id: $this->modalId);
         $this->dispatch('reset-select2');

    }

    public function openModal(){
        $this->resetInputFields();
        $this->resetValidation();
        $this->dispatch('open-modal', id: $this->modalId);
         $this->dispatch('reset-select2');
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
    public $student;

    public function mount($active_menu_id = null, $student_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->student =Student::findOrFail($student_id);

    }

     public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'student',
        ]);
    }

    public $search = [
            'name' => null,
            'status' => null,
        ];

    public function render()
    {
        $course_results = $this->student->courseResults()
        ->with([
            'course' => function ($q) {
                $q->withoutGlobalScopes();
            },

            'course.book',

            'course.teacher' => function ($q) {
                $q->withoutGlobalScopes()
                ->select('id', 'name', 'last_name');
            },

            'examScores' => function ($q) {
                $q->where('student_id', $this->student->id)
                    ->with(['examType'])
                    ->join('exam_types', 'exam_types.id', '=', 'student_exam_scores.exam_type_id')
                    ->orderBy('exam_types.order', 'asc')
                    ->select('student_exam_scores.*');
            }
        ])
        ->paginate($this->perPage);

        return view('livewire.academic.students.student-courses.student-course-result-list',compact('course_results'));
    }

    public $student_course_result_id;

    public $scores = [];

    public $course_id;

    public $student_id;

    protected function rules()
    {
        
        return [
           'course_id'=>'required',
        ];
    }
    // Localized messages
    protected function messages()
    {
        return [
            'course_id.required'   => __('label.course_id.required'),
            'student_id.required'   => __('label.student_id.required'),
        ];
    }

    public $exam_period = 'midterm';
    public function edit($id)
    {
        $this->resetValidation();

        $this->student_course_result_id = $id;
        
        $this->exam_period = 'midterm';

        $this->loadScores();

        $this->editMode = true;

        $this->dispatch('open-modal', id: $this->modalId);
    }

    public function updatedExamPeriod()
    {
        $this->loadScores();
    }

    public function getTotalProperty()
    {
        return collect($this->scores)
            ->sum(fn ($row) => floatval($row['score'] ?? 0));
    }

    public function loadScores()
    {
        $result = StudentCourseResult::findOrFail($this->student_course_result_id);

        $exam_period = $this->exam_period;
        $this->course_id = $result->course_id;
        $this->student_id = $result->student_id;

        $result->load([
            'course.book.examTypes' => function ($q) use ($exam_period) {

                $q->when($exam_period !== 'all', function ($q) use ($exam_period) {
                    $q->where('exam_period', $exam_period);
                })
                ->orderBy('order', 'asc');
            },

            'examScores' => function ($q) use ($result, $exam_period) {

                $q->where('student_id', $result->student_id)
                ->when($exam_period !== 'all', function ($q) use ($exam_period) {
                    $q->whereHas('examType', function ($q2) use ($exam_period) {
                        $q2->where('exam_period', $exam_period);
                    });
                })
                ->with('examType');
            }
        ]);

        $percentages = $result->course->book->examTypes
            ->pluck('pivot.percentage', 'id')
            ->toArray();

        $this->scores = [];

        foreach ($result->examScores as $score) {

            $this->scores[$score->id] = [
                'exam_type_id'   => $score->exam_type_id,
                'exam_type_name' => $score->examType?->name,
                'percentage'     => $percentages[$score->exam_type_id] ?? 0,
                'score'          => $score->score,
            ];
        }
    }

    // Update role
   public function update()
    {
        if (!edit(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        $this->validate();

        DB::beginTransaction();

        try {

            $course = Course::with('book', 'book.examTypes')
                ->findOrFail($this->course_id);

            $book = $course->book;

            $pass_mark = $book?->pass_mark ?? 50;
            $makeup_mark = $book?->makeup_mark ?? 40;

            $student_id = $this->student_id;

            /**
             * =========================================================
             * 1. GET EXISTING SCORES FROM DB (SOURCE OF TRUTH)
             * =========================================================
             */
            $existingScores = StudentExamScore::where([
                'student_id' => $student_id,
                'course_id'  => $this->course_id,
            ])->get()->keyBy('exam_type_id');

            /**
             * =========================================================
             * 2. UPDATE SCORES + LOG CHANGES
             * =========================================================
             */
            foreach ($this->scores as $row) {

                $exam_type_id = $row['exam_type_id'];
                $new_score    = floatval($row['score']);
                $max          = $row['percentage'] ?? 100;

                // clamp
                $new_score = min(max($new_score, 0), $max);

                $old = $existingScores[$exam_type_id] ?? null;

                StudentExamScore::updateOrCreate(
                    [
                        'student_id'   => $student_id,
                        'course_id'    => $this->course_id,
                        'exam_type_id' => $exam_type_id,
                    ],
                    [
                        'score'   => $new_score,
                        'user_id' => Auth::id(),
                    ]
                );

                if ($old && floatval($old->score) !== floatval($new_score)) {

                    StudentExamScoreLog::create([
                        'student_id'   => $student_id,
                        'course_id'    => $this->course_id,
                        'exam_type_id' => $exam_type_id,
                        'score_old'    => $old->score,
                        'score_new'    => $new_score,
                        'user_id'      => Auth::id(),
                    ]);
                }
            }

            /**
             * =========================================================
             * 3. CALCULATE TOTAL FROM DATABASE (VERY IMPORTANT FIX)
             * =========================================================
             */
            $total = StudentExamScore::where([
                'student_id' => $student_id,
                'course_id'  => $this->course_id,
            ])->sum('score');

            /**
             * =========================================================
             * 4. STATUS CALCULATION
             * =========================================================
             */
            if ($this->exam_period === 'midterm') {
                $status = 'in_progress';
            } else {

                if ($total >= $pass_mark) {
                    $status = 'passed';
                } elseif ($total >= $makeup_mark) {
                    $status = 'makeup';
                } else {
                    $status = 'failed';
                }
            }

            /**
             * =========================================================
             * 5. UPDATE RESULT TABLE
             * =========================================================
             */
            $result = StudentCourseResult::updateOrCreate(
                [
                    'student_id' => $student_id,
                    'course_id'  => $this->course_id,
                ],
                [
                    'total'                 => $total,
                    'status'                => $status,
                    'pass_mark_snapshot'    => $pass_mark,
                    'makeup_mark_snapshot'  => $makeup_mark,
                    'user_id'               => Auth::id(),
                ]
            );

            /**
             * =========================================================
             * 6. COURSE STUDENT + WAITING LIST
             * =========================================================
             */
            if ($this->exam_period !== 'midterm') {

                CourseStudent::where('student_id', $student_id)
                    ->where('course_id', $this->course_id)
                    ->update([
                        'status' => $status,
                    ]);

                CourseWaitingList::where('student_id', $student_id)
                    ->where('program_id', $book->program_id)
                    ->delete();

                if ($status === 'passed') {

                    $next_book = Book::where('program_id', $book->program_id)
                        ->where('level_number', $book->level_number + 1)
                        ->first();

                    if ($next_book) {

                        CourseWaitingList::updateOrCreate(
                            [
                                'student_id' => $student_id,
                                'program_id' => $book->program_id,
                                'book_id'    => $next_book->id,
                            ],
                            [
                                'branch_id' => $course->branch_id,
                                'shift_id'  => $course->shift_id,
                                'status'    => $status,
                                'user_id'   => Auth::id(),
                            ]
                        );
                    }

                } else {

                    CourseWaitingList::updateOrCreate(
                        [
                            'student_id' => $student_id,
                            'program_id' => $book->program_id,
                            'book_id'    => $book->id,
                        ],
                        [
                            'branch_id' => $course->branch_id,
                            'shift_id'  => $course->shift_id,
                            'status'    => $status,
                            'user_id'   => Auth::id(),
                        ]
                    );
                }
            }

            /**
             * =========================================================
             * 7. SYSTEM LOG
             * =========================================================
             */
            SystemLog::create([
                'st_id'   => $student_id,
                'user_id' => Auth::id(),
                'section' => __('label.student_course_result') . ' ID:' . $result->id,
                'type_id' => 3,
            ]);

            DB::commit();

            $this->closeModal();

            $this->dispatch('alert', type: 'success', message: __('label.successfully_updated'));

        } catch (\Exception $e) {

            DB::rollBack();

            $this->dispatch('alert', type: 'error', message: __('label.update_error') . ' : ' . $e->getMessage());
        }
    }


    public $result_card = [];
    public function resultCard($result_id)
    {
       
        $this->result_card = StudentCourseResult::with('student','course','course.branch','course.book:id,name')->findOrFail($result_id);
        $this->dispatch('open-modal', id: 'resultCardModal');
    }

    public function print()
    {
        
        $this->dispatch('show-print-preview');
    }
}
