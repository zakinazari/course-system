<?php

namespace App\Livewire\Academic\Students\StudentCourses;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\CenterSettings\Branch;
use App\Models\Academic\Student;
use App\Models\Academic\CourseStudent;
use App\Models\Academic\Course;
use App\Models\Assessment\StudentAttendance;
use App\Models\Academic\CourseWaitingList;
use App\Models\Financial\StudentCourseFee;
use Auth;
use DB;
class StudentCourseAttendanceList extends Component
{
     // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'student-course-attendance-list-addEditModal';
    public $table_name='student_attendances';
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
    public $student_courses = [];
    public function mount($active_menu_id = null, $student_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->student =Student::findOrFail($student_id);
        $this->student_courses = $this->student->courses()
        ->with('book:id,name')
        ->orderBy('pivot_enrolled_at', 'desc')->get();

        // $this->search['from'] = now()->format('Y-m-d');
        // $this->search['to'] = now()->format('Y-m-d');
        
    }

    public $attendance_id,$status,$note;

    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'student',
            'student_courses',
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
        $attendances = StudentAttendance::with([
            'course',
            'course.book:id,name',
            'course.time',
            'course.teacher:id,name,last_name'
        ])
        ->where('student_id',$this->student->id)
        ->where('course_id',$search['course_id'])
        // ->when(!empty($search['course_id']), function($q) use ($search){
        //         $q->where('course_id',$search['course_id']);
        //     })
        ->when(!empty($search['from']) && !empty($search['to']), function($q) use ($search){
                $q->where('attendance_date','>=',$search['from']);
                $q->where('attendance_date','<=',$search['to']);
            })
        ->orderBy('attendance_date','desc')
        ->paginate($this->perPage);
        return view('livewire.academic.students.student-courses.student-course-attendance-list',compact('attendances'));
    }

    protected function rules()
    {
        
        return [
            'attendance_id' => 'required|exists:student_attendances,id',
            'note'=>'required',
        ];
    }

    protected function messages()
    {
        return [
            'note.required' => __('label.note.required'),
        ];
    }

    public function edit($id)
    {
        $this->resetValidation(); 
        $this->attendance_id = $id;    
        $attendance = StudentAttendance::findOrFail($id);
        $this->status = $attendance->status;
        $this->note = $attendance->note;
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
            
            $attendance = StudentAttendance::findOrFail($this->attendance_id);
            $attendance->update([
                'status' => $this->status,
                'note' => $this->note,
            ]);

            // find absent days in this course--------------------

            $absents_days = StudentAttendance::where('course_id',$attendance->course_id)
            ->where('student_id',$attendance->student_id)
            ->where('status','absent')
            ->count();
            
            // find drop days in this book--------------------

            $course = Course::with('book')->findOrFail($attendance->course_id);
            
            $drop_days = $course->book->drop_days;
 
            if($absents_days < $drop_days){

                $course_student = CourseStudent::where('course_id',$attendance->course_id)
                ->where('student_id',$attendance->student_id)
                ->where('status','dropped')
                ->first();

                // ------check course fees record recored-----------------------------

                $course_fee = StudentCourseFee::where('course_id',$attendance->course_id)
                ->where('student_id',$attendance->student_id)
                ->exists();
                
                $status = $course_fee? 'active':'pending';

                if(!empty($course_student)){

                    $course_student->update([
                        'status' => $status,
                    ]);

                }

                // ===============================
                // Remove from Waiting List
                // ===============================

                CourseWaitingList::where([
                    'student_id' => $attendance->student_id,
                    'program_id' => $course->program_id,
                    'book_id' => $course->book_id,
                    // 'branch_id' => $course->branch_id,
                    // 'shift_id' => $course->shift_id,
                ])->delete();
            }
          
            // ---start system log-----------
            SystemLog::create([
                'st_id' => $attendance->student_id,
                'user_id' => Auth::user()->id,
                'section' => __('label.student_attendance').' ID:'.$attendance->id.')',
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

}
