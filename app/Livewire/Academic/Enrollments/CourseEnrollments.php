<?php

namespace App\Livewire\Academic\Enrollments;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\Academic\Course;
use App\Models\Academic\CourseStudent;
use App\Models\Academic\Student;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Program;
use App\Models\CenterSettings\Book;
use App\Models\CenterSettings\Classroom;
use App\Models\CenterSettings\Shift;
use App\Models\CenterSettings\Time;
use App\Models\Financial\StudentCourseFee;
use App\Models\Academic\CourseWaitingList;
use App\Models\Hr\Employee;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Validation\Rule;
class CourseEnrollments extends Component
{
       // -------start generals--------------------
    use WithPagination;
    public $perPage = 12;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'course-enrollment-addEditModal';
    public $table_name='course_student';
    public $selectedFields = [];
    public $pdfOrientation ='landscape';
    public $course =[];
    public $target_courses = [];
    public $merge_courses = [];
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

    public function closePromoteModal(){
        $this->resetInputFields();
        $this->resetValidation();
        $this->dispatch('close-modal', id: 'promoteModal');

    }
    public function openPromoteModal(){
        $this->resetInputFields();
        $this->resetValidation();
        $this->dispatch('open-modal', id: 'promoteModal');
    }

    public function closeMergeModal(){
        $this->resetInputFields();
        $this->resetValidation();
        $this->dispatch('close-modal', id: 'mergeModal');

    }
    public function openMergeModal(){
        $this->resetInputFields();
        $this->resetValidation();
        $this->dispatch('open-modal', id: 'mergeModal');
    }

    public function closeChangeTimeModal(){
        $this->resetInputFields();
        $this->resetValidation();
        $this->dispatch('close-modal', id: 'changeTimeModal');

    }
    public function openChangeTimeModal(){
        $this->resetInputFields();
        $this->resetValidation();
        $this->dispatch('open-modal', id: 'changeTimeModal');
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

    public $course_id;
    public $student_id;
    public $target_course_id;
    public $merging_course_ids=[];

    public $promote_type=1;
    public $start_time,$end_time;
    public $start_date,$end_date;
    public $mid_exam_date; 
    public $final_exam_date;
    public $total_teaching_days;
    public function mount($active_menu_id = null,$course_id,$student_id=null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->course_id = $course_id;
        $course = Course::findOrFail($course_id);
        $this->target_courses = Course::with('book')
        ->whereHas('book', function($q) use ($course) {
            $q->where('level_number', $course->book?->level_number + 1);
        })
        ->where('program_id',$course->program_id)
        ->whereNotIn('status',['archived','cancelled'])
        ->where('id','<>',$this->course_id)
        ->where('branch_id',$course->branch_id)->get();

        $this->merge_courses = Course::with('book')
        ->whereHas('book', function($q) use ($course) {
            $q->where('level_number', $course->book?->level_number);
        })
        ->where('program_id',$course->program_id)
        ->whereNotIn('status',['archived','cancelled'])
        ->where('id','<>',$this->course_id)
        ->where('branch_id',$course->branch_id)->get();

        $this->student_id = $student_id;
    }

    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'course',
            'course',
            'course_id',
            'target_courses',
            'merge_courses',
        ]);
    }
    public $search = [
            'name' => null,
            'status' => null,
        ];


    public function render()
    {   
        $this->course = Course::with('students','branch','courseType','program','book','classroom','shift','time')
            ->findOrFail($this->course_id);

        $studentsQuery = $this->course->students()
            ->when(!empty($this->search['identity']), function ($query) {
                $search = $this->search['identity'];
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('student_code', 'like', "%{$search}%");
                });
            })
            ->when(!empty($this->search['status']), function ($query) {
                $status = $this->search['status'];
                $query->where('course_student.status', $status); 
            });

        return view('livewire.academic.enrollments.course-enrollments', [
            'students' => $studentsQuery->paginate($this->perPage),
        ]);
    }

    protected function rules()
    {
        
        if ($this->pivot_id) {
            return [
                'status' => ['required'],
            ];
        }

        return [
            'student_id' => [
                'required',
                Rule::unique('course_student')
                    ->where(function ($query) {
                        return $query->where('course_id', $this->course_id);
                    })
            ],
        ];
    }
    // Localized messages
    protected function messages()
    {
        return [

            'course_id.required'   => __('label.branch.required'),
            'student_id.required'   => __('label.student.required'),
            'student_id.unique'   => __('label.student.unique'),
            'status.required'   => __('label.status.required'),
        ];
    }
    
    // Create role
    public function store()
    {
        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        $this->validate();
        DB::beginTransaction();

        try {

            //  قفل روی کورس برای جلوگیری از ثبت همزمان
            $course = Course::where('id', $this->course_id)
                ->lockForUpdate()
                ->firstOrFail();

            $is_full = DB::table('course_student')
                ->where('course_id', $course->id)
                ->where('status', 'active')
                ->selectRaw('COUNT(*) as total')
                ->havingRaw('total >= ?', [$course->max_capacity])
                ->exists();

            if ($course->max_capacity !== null && $is_full) {
                DB::rollBack();
                return $this->dispatch('alert', type: 'error', message: __('label.course_capacity_full'));
            }

            $fee = StudentCourseFee::where('course_id',$this->course_id)
            ->where('student_id',$this->student_id)->exists();

            $course->students()->sync([
                $this->student_id => [
                    'status' => $fee ? 'active' : 'pending',
                    'enrolled_at' => now(),
                ]
            ], false);
            // --------active course-----------
            $course->status= 'ongoing';
            $course->save();
            // -------active student----------------
            Student::find($this->student_id)->update([
                'status'=>'active',
            ]);

            DB::commit();

            $this->closeModal();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.store_error') . ': ' . $e->getMessage());
        }
    }

    public $pivot_id;
    public $status;

    public function edit($id)
    {
        $this->resetValidation(); 
        $this->pivot_id = $id;
        $pivot = DB::table('course_student')->find($id);
        $this->status = $pivot->status; 
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
        try {

         DB::table('course_student')
            ->where('id', $this->pivot_id)
            ->update([
                'status' => $this->status,
            ]);
        
            $this->closeModal();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_updated'));
        } catch (\Exception $e) {
        
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
            $courseStudent = CourseStudent::findOrFail($id);
            $student = Student::find($courseStudent->student_id);
            $course = Course::find($courseStudent->course_id);
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => 'Course ('.$course->name.') Student ('.$student->name.' ID:'.$student->student_id.')',
                'type_id' => 4,
            ]);
            $courseStudent->delete();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }

   public function exportPdf()
    {
        $defaultFields = [
            'no',
            'student_code',
            'name',
            'last_name',
            'father_name',
            'enrolled_at',
            'status',
        ];

         $fields = !empty($this->selectedFields)
            ? $this->selectedFields
            : $defaultFields;

        $course = Course::with('students')->findOrFail($this->course_id);

        $studentsQuery = $course->students()

            ->when(!empty($this->search['identity']), function ($query) {
                $search = $this->search['identity'];

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('student_code', 'like', "%{$search}%");
                });
            })

            ->when(!empty($this->search['status']), function ($query) {
                $status = $this->search['status'];
                $query->where('course_student.status', 'like', "%{$status}%");
            })

            ->orderBy('students.id', 'desc');

        $students = $studentsQuery->get();

        $pdf = Pdf::loadView(
            'livewire.academic.courses.course-student-list-pdf',
            [
                'students' => $students,
                'course' => $course,
                'fields' => $fields
            ]
        )->setPaper('a4',$this->pdfOrientation)
        ->setOption('defaultFont', 'dejavusans');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'course-student-list-' . Carbon::now()->format('Y-m-d -H-i-A') . '.pdf'
        );
    }


    public function calculateEndDate()
    {   
        
        
        if (!$this->start_date || !$this->total_teaching_days || $this->total_teaching_days <= 0) {
            $this->end_date = null;
            $this->mid_exam_date = null;
            $this->final_exam_date = null;
            return;
        }

        $start = Carbon::parse($this->start_date);
        $date = $start->copy();
        $days_counted = 0;

        // 1️ محاسبه end_date 
        while ($days_counted < $this->total_teaching_days) {
            if ($date->dayOfWeek != Carbon::FRIDAY) {
                $days_counted++;
            }

            if ($days_counted < $this->total_teaching_days) {
                $date->addDay();
            }
        }

        // 2️ اضافه کردن 2 روز امتحان (بدون جمعه)
        $exam_days = 0;
        while ($exam_days < 2) {
            $date->addDay();

            if ($date->dayOfWeek != Carbon::FRIDAY) {
                $exam_days++;
            }
        }

        $end = $date->copy();

        //  محاسبه وسط زمانی
        $mid = $start->copy()->addSeconds($start->diffInSeconds($end) / 2);

        //  اگر وسط افتاد روی جمعه → برو روز بعد
        while ($mid->dayOfWeek == Carbon::FRIDAY) {
            $mid->addDay();
        }

        // نتایج
        $this->end_date = $end->toDateString();
        $this->mid_exam_date = $mid->toDateString();
        $this->final_exam_date = $this->end_date;

        if (!is_null($this->end_date)) {
            $this->resetErrorBag('end_date');
        }
    }

    // وقتی تاریخ شروع تغییر کند
    public function updatedStartDate($value)
    {
        
        $this->calculateEndDate();
        $this->resetErrorBag('start_date');
        $this->resetErrorBag('end_date');
        $this->resetErrorBag('mid_exam_date');
        $this->resetErrorBag('final_exam_date');
        
    }

    public function updatedPromoteTimeId(){
        $this->resetErrorBag('promote_time_id');
    }

    public $new_level;
    public $course_times = [];
    public $promote_time_id;
    public function updatedPromoteType(){
        $this->resetErrorBag('promote_type');
        if ($this->promote_type == 2) {
            $this->new_level = Book::where('program_id', $this->course->program_id)
                ->where('level_number', $this->course->book->level_number + 1)
                ->first();

            if (!$this->new_level) {
                $this->addError('promote_type', __('label.next_level_is_not_defined'));
                return;
            }

            if ($this->new_level) {
                $this->total_teaching_days = $this->new_level->total_teaching_days;
                $this->course_times = Time::all();
            }

        } else {
            // وقتی existing انتخاب شد → ریست
            $this->new_level = null;
            $this->total_teaching_days = null;
            $this->start_date = null;
            $this->end_date = null;
            $this->mid_exam_date = null;
            $this->final_exam_date = null;
            $this->course_times = [];
            $this->promote_time_id =null;
        }

    }

    public function promoteCourse()
    {
        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        if ($this->promote_type == 1) {

            $this->validate([
                'target_course_id' => 'required|exists:courses,id',
            ]
            ,
            [
                'target_course_id.required' => __('label.course_id.required'),
            ]);
        } else {
            $this->resetErrorBag('promote_type');
            if (!$this->new_level) {
                $this->addError('promote_type', __('label.next_level_is_not_defined'));
                return;
            }

            $this->validate([
                'total_teaching_days' => 'required|integer|min:1',
                'start_date' => 'required',
                'end_date' => 'required',
                'mid_exam_date' => 'required',
                'final_exam_date' => 'required',
                'promote_time_id' => 'required',
            ],[
                'total_teaching_days.required'   => __('label.total_teaching_days.required'),
                'start_date.required'   => __('label.start_date.required'),
                'end_date.required'   => __('label.end_date.required'),
                'mid_exam_date.required'   => __('label.mid_exam_date.required'),
                'final_exam_date.required'   => __('label.final_exam_date.required'),
                'promote_time_id.required'   => __('label.time.required'),
            ]);
        }

        DB::beginTransaction();

        try {

            // کورس فعلی (source)
            $source_course = Course::where('id', $this->course_id)
                ->lockForUpdate()
                ->firstOrFail();

            if($this->promote_type==2){

                if (empty($this->new_level)) {

                    DB::rollBack();
                    return $this->dispatch('alert', type: 'error', message: __('label.book.required'));
                }
                $exists = Course::where('branch_id', $source_course->branch_id)
                ->where('classroom_id', $source_course->classroom_id)
                ->where('time_id', $source_course->time_id)
                ->whereDate('start_date', $this->start_date)
                ->exists();

                if ($exists) {
                    DB::rollBack();
                    return $this->dispatch('alert', type: 'error', message: __('label.course_already_exists'));
                }

                $target_course = Course::create([
                    'course_type_id' => $source_course->course_type_id,
                    'program_id' => $source_course->program_id,
                    'book_id' => $this->new_level->id,
                    'shift_id' => $source_course->shift_id,
                    'time_id' => $this->promote_time_id,
                    'classroom_id' => $source_course->classroom_id,
                    'min_capacity' => $this->new_level->min_capacity,
                    'max_capacity' => $this->new_level->max_capacity,
                    'total_teaching_days' => $this->total_teaching_days,
                    'start_date' => $this->start_date,
                    'end_date' => $this->end_date,
                    'mid_exam_date' => $this->mid_exam_date,
                    'final_exam_date' => $this->final_exam_date,

                    'teacher_id' => $source_course->teacher_id,

                    'branch_id' =>  $source_course->branch_id,
                    'user_id' => Auth::Id(),
                ]);

            }else{
                // کورس هدف (target)
                $target_course = Course::where('id', $this->target_course_id)
                    ->lockForUpdate()
                    ->firstOrFail();
            }
            
            // گرفتن دانشجوهای کورس فعلی
            $query = CourseStudent::where('course_id', $source_course->id)
                ->whereHas('courseResult', function ($query) {
                    $query->where('status', 'passed');
                });

            if (!$query->exists()) {
                DB::rollBack();
                return $this->dispatch('alert', type: 'error', message: __('label.no_students_has_passed_this_course'));
            }

            $students = $query->get();

            
            // چک ظرفیت کورس هدف
            $current_count = DB::table('course_student')
                ->where('course_id', $target_course->id)
                // ->where('status', 'active')
                ->count();

            if ($target_course->max_capacity !== null 
                && ($current_count + $students->count()) > $target_course->max_capacity) {

                DB::rollBack();
                return $this->dispatch('alert', type: 'error', message: __('label.course_capacity_full'));
            }

            foreach ($students as $student) {

                // چک تکراری نبودن
                $exists = DB::table('course_student')
                    ->where('course_id', $target_course->id)
                    ->where('student_id', $student->student_id)
                    ->exists();

                if ($exists) continue;

                // بررسی فیس
                $fee = StudentCourseFee::where('course_id', $target_course->id)
                    ->where('student_id', $student->student_id)
                    ->exists();

                // ثبت در کورس جدید
                $target_course->students()->sync([
                    $student->student_id => [
                        'status' => $fee ? 'active' : 'pending',
                        'enrolled_at' => now(),
                    ]
                ], false);
            }

            $target_course->status = 'ongoing';
            $target_course->save();

            // --------------اضافه نمودن دانشجویانی ناکام در لیست انتظار -------------------------
                $failed_students = CourseStudent::where('course_id', $source_course->id)
                ->where(function ($q) {
                    $q->where('status', 'dropped')
                    ->orWhereHas('courseResult', function ($query) {
                        $query->where('status', 'failed');
                    });
                })
                ->get();

                foreach ($failed_students as $student) {

                    CourseWaitingList::firstOrCreate(
                        [
                            'student_id' => $student->student_id,
                            'branch_id' => $source_course->branch_id,
                            'program_id' => $source_course->program_id,
                            'book_id' => $source_course->book_id,
                            'shift_id' => $source_course->shift_id,
                        ],
                        [
                            'status' => 'waiting',
                            'user_id' => Auth::id(),
                        ]
                    );
                }
            // --------------اضافه نمودن دانشجویانی ناکام در لیست انتظار-------------------------

            $this->dispatch('reset-select2');
            $this->target_course_id = null;

            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => 'Course Promoted ('.$source_course->name.')',
                'type_id' => 3,
            ]);

            DB::commit();
        
            $this->closePromoteModal();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.store_error') . ': ' . $e->getMessage());
        }
    }

    public function mergeCourse()
    {
        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        $this->validate(
            [
                'merging_course_ids' => 'required|array|min:1',
                'merging_course_ids.*' => 'exists:courses,id',
            ],
            [
                'merging_course_ids.required' => __('label.course_id.required'),
                'merging_course_ids.*.exists' => 'One of the selected courses is not valid.',
            ]
        );

        DB::beginTransaction();

        try {
            // کورس مقصد
            $target_course = Course::where('id', $this->course_id)
                ->lockForUpdate()
                ->firstOrFail();

            // دانشجویان فعلی کورس مقصد
            $current_students = DB::table('course_student')
                ->where('course_id', $target_course->id)
                ->pluck('student_id')
                ->toArray();

            $new_students = [];

            foreach ($this->merging_course_ids as $source_id) {
                $students = DB::table('course_student')
                    ->where('course_id', $source_id)
                    ->pluck('student_id')
                    ->toArray();

                foreach ($students as $student_id) {
                    if (!in_array($student_id, $current_students) && !in_array($student_id, $new_students)) {
                        $new_students[] = $student_id;
                    }
                }
            }

            // چک ظرفیت کورس
            if ($target_course->max_capacity !== null 
                && (count($current_students) + count($new_students)) > $target_course->max_capacity) {

                DB::rollBack();
                return $this->dispatch('alert', type: 'error', message: __('label.course_capacity_full'));
            }

            // اضافه کردن دانشجویان جدید
            foreach ($new_students as $student_id) {
                $fee = StudentCourseFee::where('course_id', $target_course->id)
                    ->where('student_id', $student_id)
                    ->exists();

                $target_course->students()->sync([
                    $student_id => [
                        'status' => $fee ? 'active' : 'pending',
                        'enrolled_at' => now(),
                    ]
                ], false);
            }

            $target_course->status = 'ongoing';
            $target_course->save();

            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => 'Course Merged ('.$target_course->name.')',
                'type_id' => 3,
            ]);
            DB::commit();

            $this->merging_course_ids = [];
            $this->dispatch('reset-select2');
            $this->closeMergeModal();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.store_error') . ': ' . $e->getMessage());
        }
    }

    public function changeTime($id)
    {
        $this->pivot_id = $id;
        $this->dispatch('open-modal', id: 'changeTimeModal');
    }

    public function changeTimeStore()
    {
        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        $this->validate([
            'target_course_id' => 'required|exists:courses,id',
        ], [
            'target_course_id.required' => __('label.course_id.required'),
        ]);

        DB::beginTransaction();

        try {

            // pivot (دانشجو در کورس فعلی)
            $pivot = DB::table('course_student')
                ->where('id', $this->pivot_id)
                ->lockForUpdate()
                ->first();

            if (!$pivot) {
                throw new \Exception('Student record not found.');
            }

            $student_id = $pivot->student_id;

            // کورس مقصد
            $target_course = Course::where('id', $this->target_course_id)
                ->lockForUpdate()
                ->firstOrFail();

            // چک duplicate
            $exists = DB::table('course_student')
                ->where('course_id', $target_course->id)
                ->where('student_id', $student_id)
                ->exists();

            if ($exists) {
                DB::rollBack();
                return $this->dispatch('alert', type: 'error', message: __('label.student_already_exists_in_this_course'));
            }

            // چک ظرفیت
            $current_count = DB::table('course_student')
                ->where('course_id', $target_course->id)
                ->count();

            if ($target_course->max_capacity !== null 
                && ($current_count + 1) > $target_course->max_capacity) {

                DB::rollBack();
                return $this->dispatch('alert', type: 'error', message: __('label.course_capacity_full'));
            }

    

            // اضافه به کورس جدید
            $target_course->students()->sync([
                $student_id => [
                    'status' => $pivot->status,
                    'enrolled_at' => now(),
                ]
            ], false);

            //  حذف از کورس قبلی (اصل change time)
            DB::table('course_student')
                ->where('id', $this->pivot_id)
                ->delete();

            SystemLog::create([
                'st_id' => $student_id,
                'user_id' => Auth::user()->id,
                'section' => 'Changed Time ('.$this->course->name.') to this course '.$target_course->name,
                'type_id' => 3,
            ]);

            DB::commit();

            // reset
            $this->dispatch('reset-select2');
            $this->target_course_id = null;

            $this->closeChangeTimeModal();

            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.store_error') . ': ' . $e->getMessage());
        }
    }

    public function archiveCourse()
    {
        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        DB::beginTransaction();

        try {
            // کورس مقصد
            $course = Course::where('id', $this->course_id)
                ->lockForUpdate()
                ->firstOrFail();
            $course->status='archived';
            $course->save();

            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => 'Course Archived('.$course->name.')',
                'type_id' => 3,
            ]);

            DB::commit();
            $this->dispatch('close-modal', id: 'archiveModal');
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.store_error') . ': ' . $e->getMessage());
        }
    }
}
