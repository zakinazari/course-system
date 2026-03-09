<?php

namespace App\Livewire\Academic\CourseWaiting;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\Academic\CourseType;
use App\Models\Academic\CourseWaitingList;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Program;
use App\Models\CenterSettings\Book;
use App\Models\CenterSettings\Shift;
use Barryvdh\DomPDF\Facade\Pdf;
use Auth;
use Carbon\Carbon;
use DB;
class WaitingStudents extends Component
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
    }

    public $waiting_id,$program_id,$book_id,$shift_id,$branch_id,
    $status = 'waiting';
    public $student_id;
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
        ];

    public function render()
    {
        $waiting_students = CourseWaitingList::with('student','branch','program','book','shift')

        ->when(!empty($this->search['identity']), function ($query) {
            $search = $this->search['identity'];
            $query->whereHas('student',function($q) use ($search){
                $q->where(function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%")
                    ->orWhere('student_code', 'like', "%{$search}%");
                });
            });
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

        ->when(!empty($this->search['shift_id']), function ($query) {
            $query->where('shift_id',$this->search['shift_id']);
        })
        ->when(!empty($this->search['status']), function ($query) {
            $query->where('status',$this->search['status']);
        })
        ->orderBy('id','desc')
        ->paginate($this->perPage);

        return view('livewire.academic.course-waiting.waiting-students',compact('waiting_students'));
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
    
    // Create role
    public function store()
    {
        
        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        $this->validate();
        DB::beginTransaction();
        try {

            $waiting = CourseWaitingList::firstOrCreate(
                [
                    'student_id' => $this->student_id,
                    'branch_id' =>  Auth::user()->branch_id ?: $this->branch_id,
                    'program_id' => $this->program_id,
                    'book_id' => $this->book_id,
                    'shift_id' => $this->shift_id,
                ],
                [
                    'status' => 'waiting',
                    'user_id' => Auth::Id(),
                ]
            );
             // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'st_id' => $waiting->student?->id,
                'section' => __('label.waiting').' ('.$waiting->student?->name.' ID:'.$waiting->id.')',
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
        $waiting = CourseWaitingList::find($id);
        $this->waiting_id = $id;  
        $this->student_id = $waiting->student_id;
        $this->branch_id = $waiting->branch_id;
        $this->program_id = $waiting->program_id;
        $this->loadProgramBook($this->program_id);
        $this->book_id = $waiting->book_id;
        $this->shift_id = $waiting->shift_id;

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
            $exists = CourseWaitingList::where('student_id', $this->student_id)
                ->where('branch_id', $this->branch_id)
                ->where('program_id', $this->program_id)
                ->where('book_id', $this->book_id)
                ->where('shift_id', $this->shift_id)
                ->where('id', '!=', $this->waiting_id)
                ->exists();

            if ($exists) {
                $this->addError('duplicate',__('label.student.unique'));
                return;
            }


            $waiting = CourseWaitingList::findOrFail($this->waiting_id);

            $waiting->update([
                'branch_id' =>  Auth::user()->branch_id ?: $this->branch_id,
                'program_id' => $this->program_id,
                'book_id' => $this->book_id,
                'shift_id' => $this->shift_id,
                'status' => 'waiting',
                'user_id' => Auth::id(),
            ]);

             // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'st_id' => $waiting->student?->id,
                'section' => __('label.waiting').' ('.$waiting->student?->name.' ID:'.$waiting->id.')',
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
            $waiting = CourseWaitingList::findOrFail($id);
            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'st_id' => $waiting->student?->id,
                'section' => __('label.waiting').' ('.$waiting->student?->name.' ID:'.$waiting->id.')',
                'type_id' => 4,
            ]);
            // ---end system log-------------
            $waiting->delete();
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
            'phone_no',
            'program_id',
            'book_id',
            'shift_id',
            'status',
        ];

         $fields = !empty($this->selectedFields)
            ? $this->selectedFields
            : $defaultFields;

        if (auth()->user()->isDeveloper() || auth()->user()->isAdmin()) {
            if (!in_array('branch_id', $fields)) {
                $fields[] = 'branch_id';
            }
        }

        $query = CourseWaitingList::with('student','branch','program','book','shift')

            ->when(!empty($this->search['identity']), function ($query) {
                $search = $this->search['identity'];
                $query->whereHas('student',function($q) use ($search){
                    $q->where(function ($qq) use ($search) {
                        $qq->where('name', 'like', "%{$search}%")
                        ->orWhere('student_code', 'like', "%{$search}%");
                    });
                });
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
            ->when(!empty($this->search['shift_id']), function ($query) {
                $query->where('shift_id',$this->search['shift_id']);
            })
            ->when(!empty($this->search['status']), function ($query) {
                $query->where('status',$this->search['status']);
            });

        $course_waiting = $query
            ->orderBy('id', 'asc')
            ->get();

        $pdf = Pdf::loadView(
            'livewire.academic.course-waiting.waiting-students-pdf',
            [
                'course_waiting' => $course_waiting,
                'fields' => $fields
            ]
        )->setPaper('a4',$this->pdfOrientation)
        ->setOption('defaultFont', 'dejavusans');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'course-waiting-list' . Carbon::now()->format('Y-m-d -H-i-A') . '.pdf'
        );
    }

    public function loadProgramBook($program_id)
    {
        $this->books = Book::where('status', 'active')
            ->where('program_id', $program_id)->get();
    }
    
}
