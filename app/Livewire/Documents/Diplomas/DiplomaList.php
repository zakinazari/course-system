<?php

namespace App\Livewire\Documents\Diplomas;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\CenterSettings\Branch;
use App\Models\Documents\Diploma;
use App\Models\Academic\Student;
use App\Models\Assessment\StudentCourseResult;
use Auth;
use Illuminate\Support\Str;
use DB;
use App\Services\QrCodeService;
class DiplomaList extends Component
{
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'account-list-addEditModal';
    public $table_name='diplomas';
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
    public $branches;
    
    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->branches =  Branch::all();
        
        $this->graduated_at = now()->format('Y-m-d');
    }

     public $diploma_id,$student_id,$branch_id;
     public $graduated_at;

     public $is_revoked = false;

    public $name_fa;
    public $name_pa;
    public $last_name_fa;
    public $last_name_pa;
    public $father_name_fa;
    public $father_name_pa;
    public $date_of_birth;

    public $average;

    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'branches',
            'graduated_at',
        ]);
    }


    public $search = [
            'identity' => null,
            'branch_id' => null,
        ];


    public function render()
    {
      
        $diplomas = Diploma::with('branch','student','student.photo')
        
        ->when(!empty($this->search['identity']), function ($query) {

            $query->whereHas('student',function($q) {

                $q->where(function ($st_q) {

                    $st_q->where('name', 'like', '%' . $this->search['identity'] . '%')
                    ->orWhere('student_code', $this->search['identity']);

                });

            });

        })
        ->when(!empty($this->search['branch_id']), function ($query) {
            $query->where('branch_id',$this->search['branch_id']);
        })
        ->orderBy('id','desc')
        ->paginate($this->perPage);

        return view('livewire.documents.diplomas.diploma-list',compact('diplomas'));
    }

    protected function rules()
    {
        
        $rules = [
            'student_id' => 'required|exists:students,id',
            'average' => 'nullable|numeric|min:0|max:100',
        ];

        return $rules;
    }


    // Localized messages
    protected function messages()
    {
        return [

            'student_id.required' => __('label.student.required'),
        ];
    }


    // Create Diploma
    public function store()
    {
        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {

            return $this->dispatch('alert',type: 'error',message: __('label.permission_message'));
        }

        $this->validate();

        DB::beginTransaction();

        try {

            $exists = Diploma::where('student_id', $this->student_id)
                ->where('is_revoked', false)
                ->exists();

            if ($exists) {
                return $this->dispatch(
                    'alert',
                    type: 'error',
                    message: __('label.diploma_duplicate_message'),
                );
            }

            $student  = Student::findOrFail($this->student_id);

            $diploma = Diploma::create([

                'student_id' => $this->student_id,

                'branch_id' => $student->branch_id,

                'serial_number' => 'DIP-' . now()->format('YmdHis'),

                'verification_code' => Str::uuid(),

                'graduated_at' => $this->graduated_at,
                
                'average' => $this->average,

                'is_revoked' => false,

                'printed_at' => null,
            ]);

            // --------update student info-----------------

            $student->name_fa = $this->name_fa;
            $student->name_pa = $this->name_pa;
            $student->last_name_fa = $this->last_name_fa;
            $student->last_name_pa = $this->last_name_pa;
            $student->father_name_fa = $this->father_name_fa;
            $student->father_name_pa = $this->father_name_pa;
            $student->date_of_birth = $this->date_of_birth;
            
            $student->save();

            // ---start system log-----------
            SystemLog::create([
                'st_id' => $this->student_id,
                'user_id' => Auth::user()->id,
                'section' => __('label.diploma') .
                    ' (' . $diploma->serial_number . ' ID:' . $diploma->id . ')',
                'type_id' => 2,
            ]);
            // ---end system log-------------

            DB::commit();

            $this->closeModal();

            $this->dispatch('alert',type: 'success',message: __('label.successfully_done')
            );

        } catch (\Exception $e) {

            DB::rollBack();

            $this->dispatch('alert',type: 'error', message: __('label.store_error') . ': ' . $e->getMessage()
            );
        }
    }


    public function edit($id)
    {
        $this->resetValidation(); 
        $this->diploma_id = $id;    
        $diploma = Diploma::find($id);

        $this->graduated_at = $diploma->graduated_at->format('Y-m-d');
        $this->is_revoked = $diploma->is_revoked;
        $this->student_id = $diploma->student_id;
        $this->average = $diploma->average;

        $student = Student::findOrFail($diploma->student_id);
        $this->name_fa = $student->name_fa;
        $this->name_pa = $student->name_pa;
        $this->last_name_fa = $student->last_name_fa;
        $this->last_name_pa = $student->last_name_pa;

        $this->father_name_fa = $student->father_name_fa;
        $this->father_name_pa = $student->father_name_pa;

        $this->date_of_birth = $student->date_of_birth?->format('Y-m-d');
        
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
            
            $exists = Diploma::where('student_id', $this->student_id)
                ->where('is_revoked', false)
                ->where('id','<>',$this->diploma_id)
                ->exists();

            if ($exists) {
                return $this->dispatch(
                    'alert',
                    type: 'error',
                    message: __('label.diploma_duplicate_message'),
                );
            }


            $diploma = Diploma::findOrFail($this->diploma_id);
            $diploma->update([
                'graduated_at' => $this->graduated_at,
                'average' => $this->average ?: null,
                'is_revoked' => $this->is_revoked,
            ]);

            // --------update student info-----------------
            $student = Student::findOrFail($diploma->student_id);

            $student->name_fa = $this->name_fa;
            $student->name_pa = $this->name_pa;
            $student->last_name_fa = $this->last_name_fa;
            $student->last_name_pa = $this->last_name_pa;
            $student->father_name_fa = $this->father_name_fa;
            $student->father_name_pa = $this->father_name_pa;
            $student->date_of_birth = $this->date_of_birth;
            
            $student->save();

            // ---start system log-----------
            SystemLog::create([
                'st_id' => $diploma->student_id,
                'user_id' => Auth::user()->id,
                'section' => __('label.diploma') .
                    ' (' . $diploma->serial_number . ' ID:' . $diploma->id . ')',
                'type_id' => 3,
            ]);
            // ---end system log-------------
            DB::commit();
            $this->closeModal();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_updated'));
        } catch (\Exception $e) {
            
            DB::commit();
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
            $diploma = Diploma::findOrFail($id);
             // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.diploma') .
                    ' (' . $diploma->serial_number . ' ID:' . $diploma->id . ')',
                'type_id' => 4,
            ]);
            // ---end system log-------------
            $diploma->delete();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }

}
