<?php

namespace App\Livewire\Academic\Students\StudentCourses;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\CenterSettings\Branch;
use App\Models\Academic\Student;
use App\Models\Academic\CourseStudent;
use App\Models\Financial\StudentCourseFee;
use App\Models\Academic\Course;
use Auth;
use DB;
class StudentCourseList extends Component
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
    
        $student_courses = $this->student->courses()
        ->withoutGlobalScopes()
        ->with([
            'book',
            'program',
            'time',

            'teacher' => function ($q) {
                $q->withoutGlobalScopes()
                ->select('id', 'name', 'last_name');
            },

            'classroom' => function ($q) {
                $q->withoutGlobalScopes();
            },
        ])
        ->orderBy('pivot_enrolled_at', 'desc')
        ->paginate($this->perPage);
        return view('livewire.academic.students.student-courses.student-course-list',compact('student_courses'));
    }

    protected function rules()
    {
        
        return [
           'target_course_id'=>'required',
        ];
    }
    // Localized messages
    protected function messages()
    {
        return [
            'target_course_id.required'   => __('label.course_id.required'),
        ];
    }

    public $course_id;
    public $target_course_id;
    public $target_courses = [];

    public function changeTime($course_id)
    {
        
        $this->course_id = $course_id;
        $student_id = $this->student->id;
        $course = Course::findOrFail($course_id);
        $this->target_courses = Course::where('program_id',$course->program_id)
        ->where('book_id',$course->book_id)
        ->whereNotIn('status',['archived','cancelled'])
        ->where('id','<>',$course_id)
        ->whereDoesntHave('students', function ($q) use ($student_id) {
            $q->where('course_student.student_id', $student_id);
        })
        ->where('branch_id',$this->student->branch_id)->get();
        
        $this->dispatch('open-modal', id: 'changeTimeModal');
    }

    public function changeTimeStore()
    {
        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        $this->validate([
            'course_id' => 'required|exists:courses,id',
            'target_course_id' => 'required|exists:courses,id',
        ]);

        DB::beginTransaction();

        try {
            //  گرفتن pivot با lock
            $pivot = CourseStudent::where('course_id', $this->course_id)
                ->where('student_id', $this->student->id)
                ->lockForUpdate()
                ->first();

            if (!$pivot) {
                throw new \Exception('Student not found in this course');
            }

            //  کورس مقصد
            $target_course = Course::lockForUpdate()->findOrFail($this->target_course_id);

            //  duplicate check
            $exists = CourseStudent::where('course_id', $target_course->id)
                ->where('student_id', $this->student->id)
                ->exists();

            if ($exists) {
                DB::rollBack();
                return $this->dispatch('alert', type: 'error', message: __('label.student_already_exists_in_this_course'));
            }

            //  capacity check
            $count = CourseStudent::where('course_id', $target_course->id)->count();

            if ($target_course->max_capacity !== null && ($count + 1) > $target_course->max_capacity) {
                DB::rollBack();
                return $this->dispatch('alert', type: 'error', message: __('label.course_capacity_full'));
            }

            //  اضافه به کورس جدید
            CourseStudent::create([
                'course_id'   => $target_course->id,
                'student_id'  => $this->student->id,
                'status'      => $pivot->status,
                'enrolled_at' => now(),
            ]);

            $student_course_fee = StudentCourseFee::query()
                    ->where('student_id', $this->student->id)
                    ->where('course_id', $this->course_id)
                    ->lockForUpdate()
                    ->first();

                if ($student_course_fee) {

                    $student_course_fee->update([
                        'course_id' => $target_course->id,
                    ]);
                }

            //  حذف از کورس قبلی
            $pivot->delete();

            $target_course->status='ongoing';
            $target_course->save();
            //  log
            SystemLog::create([
                'st_id' => $this->student->id,
                'user_id' => auth()->id(),
                'section' => 'Changed course time from '.$this->course_id.' to '.$target_course->id,
                'type_id' => 3,
            ]);

            DB::commit();

            // reset
            $this->target_course_id = null;
            $this->course_id = null;
            $this->dispatch('reset-select2');
            $this->dispatch('close-modal', id: 'changeTimeModal');

            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));

        } catch (\Exception $e) {

            DB::rollBack();

            $this->dispatch('alert', type: 'error', message: __('label.store_error').' : '.$e->getMessage());
        }
    }
}
