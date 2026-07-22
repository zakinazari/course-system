<?php

namespace App\Livewire\Academic\CourseWaiting;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\Academic\CourseType;
use App\Models\Academic\CourseWaitingList;
use App\Models\Academic\CourseWaitingListComment;

use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Program;
use App\Models\CenterSettings\Book;
use App\Models\CenterSettings\Shift;
use Barryvdh\DomPDF\Facade\Pdf;
use Auth;
use Carbon\Carbon;
use DB;
class WaitingStudentCommentReport extends Component
{
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'course-waiting-list-addEditModal';
    public $table_name='course_waiting_lists';
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
    public $course_types=[];

    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------
        $this->branches =  Branch::all();
        $this->programs = Program::where('status','active')->get();
        $this->shifts = Shift::all();

        $this->search['from'] = now()->format('Y-m-d');
        $this->search['to'] = now()->format('Y-m-d');
    }

    public $waiting_id,$program_id,$book_id,$shift_id,$branch_id,
    $status = 'placement';
    public $student_id;
    public $comment;
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
            'course_types',
        ]);
    }
    public $search = [
            'identity' => null,
            'program_id' => null,
            'book_id' => null,
            'branch_id' => null,
            'status' => null,
            'shift_id' => null,
            'student_id' => null,

            'from' => null,
            'to' => null,
        ];

    public function render()
    {
        $from = Carbon::parse($this->search['from'])->startOfDay();
        $to = Carbon::parse($this->search['to'])->endOfDay();

        $waiting_students = CourseWaitingListComment::with(
            'waitingList',
            'waitingList.student',
            'waitingList.program',
            'waitingList.book',
            'waitingList.shift',
            )

        ->whereHas('waitingList.student',function($q){
            $q->when(!empty($this->search['identity']), function ($query) {
                $search = $this->search['identity'];
                $query->whereHas('student',function($q) use ($search){
                    $q->where(function ($qq) use ($search) {
                        $qq->where('name', 'like', "%{$search}%")
                        ->orWhere('student_code', 'like', "%{$search}%");
                    });
                });
            });
        })
        ->whereHas('waitingList',function($q){
            $q->when(!empty($this->search['branch_id']), function ($query) {
                $query->where('branch_id',$this->search['branch_id']);
            });
            $q->when(!empty($this->search['program_id']), function ($query) {
                $query->where('program_id',$this->search['program_id']);
            });
            $q->when(!empty($this->search['book_id']), function ($query) {
                $query->where('book_id',$this->search['book_id']);
            });

            $q->when(!empty($this->search['shift_id']), function ($query) {
                $query->where('shift_id',$this->search['shift_id']);
            });
            $q->when(!empty($this->search['status']), function ($query) {
                $query->where('status',$this->search['status']);
            });
        })
        ->whereBetween('created_at', [$from, $to])
        ->orderBy('id','desc')
        ->get();

        $waiting_students = $waiting_students
        ->sortBy(function ($item) {
            return $item->waitingList?->student_id;
        })
        ->values();

        return view('livewire.academic.course-waiting.waiting-student-comment-report',compact('waiting_students'));
    }


    protected function rules()
    {
        return [
            'student_id' => $this->editMode ? 'nullable' : 'required',
            'branch_id' => 'required',
            'program_id' => 'required',
            'book_id' => 'required',
            'shift_id' => 'required',
        ];
    }
    // Localized messages
    protected function messages()
    {
        return [

            'student_id.required'   => __('label.student.required'),
            'branch_id.required'   => __('label.branch.required'),
            'program_id.required'   => __('label.program.required'),
            'book_id.required'   => __('label.book.required'),
            'shift_type_id.required'   => __('label.shift.required'),
        ];
    }

    public function loadProgramBook($program_id)
    {
        $this->books = Book::where('status', 'active')
            ->where('program_id', $program_id)->get();
    }

     public function print()
    {
        
        $this->dispatch('show-print-preview');
    }

}
