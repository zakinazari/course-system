<?php

namespace App\Livewire\Academic\Courses;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\Academic\Course;
use App\Models\Academic\CourseType;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Program;
use App\Models\CenterSettings\Book;
use App\Models\CenterSettings\Classroom;
use App\Models\CenterSettings\Shift;
use App\Models\CenterSettings\Time;
use App\Models\Hr\Employee;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Auth;
use Carbon\Carbon;
use DB;
class CourseList extends Component
{
     // -------start generals--------------------
    use WithPagination;
    use WithFileUploads;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'course-list-addEditModal';
    public $table_name='courses';
    public $selectedFields = [];

    public $pdfOrientation ='landscape';
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

    public $branches=[];
    public $programs=[];
    public $books=[];
    public $shifts=[];
    public $times=[];
    public $course_types=[];
    public $teachers=[];
    public $classrooms=[];

    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------
        $this->branches =  Branch::all();
        $this->programs = Program::where('status','active')->get();
        $this->loadClassroomAndTeacher($this->branch_id);
        
        $this->shifts = Shift::all();
        $this->times = Time::all();
        $this->course_types = CourseType::all();

        $this->selectedFields = [
            'no',
            'name',
            'course_type_id',
            'program_id',
            'book_id',
            'shift_id',
            'total_teaching_days',
            'min_capacity',
            'max_capacity',
            'start_date',
            'mid_exam_date',
            'final_exam_date',
            'teacher_id',
            'classroom_id',
            'status',
            'time_id',
        ];

        if (auth()->user()->isDeveloper() || auth()->user()->isAdmin()) {
            if (!in_array('branch_id', $this->selectedFields)) {
                $this->selectedFields[] = 'branch_id';
            }
        }

    }

    public $name,$course_code,$course_id,$program_id,$book_id,$course_type_id,$total_teaching_days,$shift_id,$start_time,$end_time,$start_date,$end_date,
        $mid_exam_date, 
        $final_exam_date, 
        $branch_id,
        $classroom_id,
        $min_capacity,
        $max_capacity,
        $image,
        $existing_image,
        $teacher_ids=[],
        $teacher_id,
        $status = 'pending';
        public $time_id;
    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'branches',
            'programs',
            'books',
            'shifts',
            'times',
            'course_types',
            'teachers',
            'classrooms',
        ]);
    }
    public $search = [
            'name' => null,
            'program_id' => null,
            'book_id' => null,
            'branch_id' => null,
            'status' => null,
            'course_type_id' => null,
            'shift_id' => null,
            'time_id' => null,
            'teacher_id' => null,
        ];

    public function render()
    {
    
        $courses = Course::with('branch','courseType','program','book','classroom','time','teacher')
        ->when(!empty($this->search['name']), function ($query) {
            $query->where('name', 'like', '%' . $this->search['name'] . '%');
        })
        ->when(!empty($this->search['branch_id']), function ($query) {
            $query->where('branch_id',$this->search['branch_id']);
        })
        ->when(!empty($this->search['program_id']), function ($query) {
            $query->where('program_id',$this->search['program_id']);
        })
        ->when(!empty($this->search['book_id']), function ($query) {
            $query->where('book_id',$this->search['book_id']);
        })
        ->when(!empty($this->search['course_type_id']), function ($query) {
            $query->where('course_type_id',$this->search['course_type_id']);
        })
        ->when(!empty($this->search['shift_id']), function ($query) {
            $query->where('shift_id',$this->search['shift_id']);
        })
        ->when(!empty($this->search['status']), function ($query) {
            $query->where('status',$this->search['status']);
        })
        ->when(!empty($this->search['teacher_id']), function ($query) {
            $query->where('teacher_id',$this->search['teacher_id']);
        })
        ->orderBy('id','desc')
        ->paginate($this->perPage);

        return view('livewire.academic.courses.course-list',compact('courses'));
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
    }

    protected function rules()
    {
        $rules =  [
            'program_id' => 'required',
            'book_id' => 'required',
            'course_type_id' => 'required',
            'shift_id' => 'required',
            'time_id' => 'required',
            'total_teaching_days' => 'required|integer|min:1',
            'min_capacity' => 'required|integer|min:1',
            'max_capacity' => 'required|integer|gte:min_capacity',
            'start_date' => 'required',
            'end_date' => 'required',
            'mid_exam_date' => 'required',
            'final_exam_date' => 'required',
            'classroom_id' => 'required',
            'teacher_id' => 'required',
            // 'teacher_ids' => 'required|array',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:512',
        ];
        if (!Auth::user()->branch_id) {
            $rules['branch_id'] = 'required';
        }
        return $rules;
    }
    // Localized messages
    protected function messages()
    {
        return [

            'branch_id.required'   => __('label.branch.required'),
            'program_id.required'   => __('label.program.required'),
            'book_id.required'   => __('label.book.required'),
            'course_type_id.required'   => __('label.course_type.required'),
            'shift_id.required'   => __('label.shift.required'),
            'time_id.required'   => __('label.time.required'),
            'classroom_id.required'   => __('label.classroom.required'),
            'teacher_id.required'   => __('label.teacher.required'),
            'teacher_ids.required'   => __('label.teacher.required'),
            'total_teaching_days.required'   => __('label.total_teaching_days.required'),
            'min_capacity.required'   => __('label.min_capacity.required'),
            'max_capacity.required'   => __('label.max_capacity.required'),
          
            'start_date.required'   => __('label.start_date.required'),
            'end_date.required'   => __('label.end_date.required'),
            'mid_exam_date.required'   => __('label.mid_exam_date.required'),
            'final_exam_date.required'   => __('label.final_exam_date.required'),
            'image.image'   => __('label.course_image'),
            'image.max'   => __('label.course_image_max'),
        ];
    }
    
   
    public function updatedBookId($value)
    {
        
        $book = Book::find($value);

        if ($book) {
            $this->total_teaching_days = $book->total_teaching_days;
            $this->min_capacity = $book->min_capacity;
            $this->max_capacity = $book->max_capacity;

            if (!is_null($this->total_teaching_days)) {
                $this->resetErrorBag('total_teaching_days');
            }

            if (!is_null($this->min_capacity)) {
                $this->resetErrorBag('min_capacity');
            }

            if (!is_null($this->max_capacity)) {
                $this->resetErrorBag('max_capacity');
            }
        } else {
            $this->total_teaching_days = null;
            $this->min_capacity = null;
            $this->max_capacity = null;
        }
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

            $exists = Course::where('branch_id', Auth::user()->branch_id ?: $this->branch_id)
            ->where('classroom_id', $this->classroom_id)
            ->where('time_id', $this->time_id)
            ->whereDate('start_date', $this->start_date)
            ->exists();

            if ($exists) {
                DB::rollBack();
                return $this->dispatch('alert', type: 'error', message: __('label.course_already_exists'));
            }

            $course = Course::create([
                'course_type_id' => $this->course_type_id,
                'program_id' => $this->program_id,
                'book_id' => $this->book_id,
                'shift_id' => $this->shift_id,
                'time_id' => $this->time_id,
                'classroom_id' => $this->classroom_id,
                'min_capacity' => $this->min_capacity,
                'max_capacity' => $this->max_capacity,
                'total_teaching_days' => $this->total_teaching_days,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'mid_exam_date' => $this->mid_exam_date,
                'final_exam_date' => $this->final_exam_date,

                'teacher_id' => $this->teacher_id,

                'branch_id' =>  Auth::user()->branch_id ?: $this->branch_id,
                'user_id' => Auth::Id(),
            ]);

            if ($this->image) {

                $file = $this->image;
                $storedName = Str::random(40) . '.' . $file->getClientOriginalExtension();

                $path = $file->storeAs(
                    'course-image/' . $course->id,
                    $storedName,
                    'public' 
                );

                $course->image = $path;
                $course->save();
            }

            // if(!empty($this->teacher_ids)){
            //     $course->teachers()->sync($this->teacher_ids);
            // }

            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.course').' ('.$course->name.' ID:'.$course->id.')',
                'type_id' => 2,
            ]);
            // ---end system log-------------

            DB::commit();
            $this->closeModal();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.store_error') . ': ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->resetValidation(); 
        $course = Course::find($id);
        $this->course_id = $id;  
        $this->branch_id = $course->branch_id;
        $this->course_type_id = $course->course_type_id;
        $this->program_id = $course->program_id;
        $this->loadProgramBook($this->program_id);
        $this->book_id = $course->book_id;
        $this->loadClassroomAndTeacher($this->branch_id);
        $this->classroom_id = $course->classroom_id;
        // $this->teacher_ids = $course->teachers?->pluck('id')->toArray();
        $this->teacher_id = $course->teacher_id;
        $this->shift_id = $course->shift_id;
        $this->time_id = $course->time_id;
        $this->total_teaching_days = $course->total_teaching_days;
        $this->min_capacity = $course->min_capacity;
        $this->max_capacity = $course->max_capacity;
        
        $this->start_date = $course->start_date
        ? $course->start_date->format('Y-m-d')
        : null;

        $this->end_date = $course->end_date
        ? $course->end_date->format('Y-m-d')
        : null;

        $this->mid_exam_date = $course->end_date
        ? $course->mid_exam_date->format('Y-m-d')
        : null;

        $this->final_exam_date = $course->final_exam_date
        ? $course->final_exam_date->format('Y-m-d')
        : null;
        $this->existing_image = $course->image;
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
            $course = Course::findOrFail($this->course_id);

            $course->update([
                'course_type_id' => $this->course_type_id,
                'program_id' => $this->program_id,
                'book_id' => $this->book_id,
                'shift_id' => $this->shift_id,
                'time_id' => $this->time_id,
                'classroom_id' => $this->classroom_id,
                'total_teaching_days' => $this->total_teaching_days,
                'min_capacity' => $this->min_capacity,
                'max_capacity' => $this->max_capacity,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'mid_exam_date' => $this->mid_exam_date,
                'final_exam_date' => $this->final_exam_date,
                'teacher_id' => $this->teacher_id,

                'branch_id' =>  Auth::user()->branch_id ?: $this->branch_id,
                'user_id' => Auth::Id(),
            ]);

            if ($this->image) {

                if ($course->image && \Storage::disk('public')->exists($course->image)) {
                    \Storage::disk('public')->delete($course->image);
                }

                $file = $this->image;
                $storedName = Str::random(40) . '.' . $file->getClientOriginalExtension();

                $path = $file->storeAs(
                    'course-image/' . $course->id,
                    $storedName,
                    'public' 
                );

                $course->image = $path;
                $course->save();
            }

            // if(!empty($this->teacher_ids)){
            //     $course->teachers()->sync($this->teacher_ids);
            // }

            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.course').' ('.$course->name.' ID:'.$course->id.')',
                'type_id' => 3,
            ]);
            // ---end system log-------------

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
            $course = Course::findOrFail($id);
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.course').'('.$course->name.' ID:'.$course->id.')',
                'type_id' => 4,
            ]);
            $course->delete();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }

   public function exportPdf()
    {
        $fields = $this->selectedFields;
        $realColumns = collect($fields)
            ->reject(fn($field) => in_array($field, ['no']))
            ->values()
            ->toArray();

        $query = Course::query()
            ->when(!empty($this->search['name']), fn($q) =>
                $q->where('name', 'like', "%{$this->search['name']}%")
            )
            ->when(!empty($this->search['branch_id']), function ($query) {
                $query->where('branch_id',$this->search['branch_id']);
            })
            ->when(!empty($this->search['program_id']), function ($query) {
                $query->where('program_id',$this->search['program_id']);
            })
            ->when(!empty($this->search['book_id']), function ($query) {
                $query->where('book_id',$this->search['book_id']);
            })
            ->when(!empty($this->search['course_type_id']), function ($query) {
                $query->where('course_type_id',$this->search['course_type_id']);
            })
            ->when(!empty($this->search['shift_id']), function ($query) {
                $query->where('shift_id',$this->search['shift_id']);
            })
            ->when(!empty($this->search['status']), function ($query) {
                $query->where('status',$this->search['status']);
            })
            ->when(!empty($this->search['teacher_id']), function ($query) {
                $query->whereHas('teachers',function($q){
                    $q->where('teacher_id',$this->search['teacher_id']);
                });
            });

            if (in_array('branch_id', $fields)) {
                $query->with('branch');
            }
            if (in_array('course_type_id', $fields)) {
                $query->with('courseType');
            }
            if (in_array('shift_id', $fields)) {
                $query->with('shift');
            }

            $query->with('time');
            
            if (in_array('program_id', $fields)) {
                $query->with('program');
            }
            if (in_array('book_id', $fields)) {
                $query->with('book');
            }
            if (in_array('classroom_id', $fields)) {
                $query->with('classroom');
            }

            if (in_array('teacher_id', $fields)) {
                $query->with('teacher');
            }

        $courses = $query
            ->orderBy('id', 'desc')
            ->get();

        $pdf = Pdf::loadView(
            'livewire.academic.courses.course-list-pdf',
            [
                'courses' => $courses,
                'fields' => $fields
            ]
        )->setPaper('a4',$this->pdfOrientation)
        ->setOption('defaultFont', 'dejavusans');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'course-list-' . Carbon::now()->format('Y-m-d -H-i-A') . '.pdf'
        );
    }

    public function loadShiftTime($shift_id)
    {
        $this->times = Time::where('shift_id', $shift_id)->get();
    }

    public function loadProgramBook($program_id)
    {
        $this->books = Book::where('status', 'active')
            ->where('program_id', $program_id)->get();
    }
    
    public function loadClassroomAndTeacher($branch_id)
    {
        $branch_id = Auth::user()->branch_id ?: $branch_id;
        $this->classrooms = Classroom::where('status', 'active')
            ->where('branch_id', $branch_id)->get();

        $this->teachers = Employee::whereHas('employeeRoles', function($query) {
            $query->where('name', 'Teacher');
        })->get();

        $this->teacher_ids = [];
    }

}
