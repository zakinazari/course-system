<?php

namespace App\Livewire\CenterSettings\Classrooms;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Classroom;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use Illuminate\Validation\Rule;
use Auth;
class ClassroomList extends Component
{
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $branches=[];
    public $modalId = 'classroom-addEditModal';
    public $table_name='classrooms';
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

    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->branches =  Branch::all();
    }

    public $name,$room_id, $branch_id,$status = 'active';

    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'branches',
        ]);
    }
    public $search = [
            'name' => null,
            'branch_id' => null,
        ];

    public function render()
    {
         $classrooms = Classroom::with('branch')
        ->when(!empty($this->search['name']), function ($query) {
            $query->where('name', 'like', '%' . $this->search['name'] . '%');
        })
        ->when(!empty($this->search['branch_id']), function ($query) {
            $query->where('branch_id',$this->search['branch_id']);
        })
        ->orderBy('id','desc')
        ->paginate($this->perPage);

        return view('livewire.center-settings.classrooms.classroom-list',compact('classrooms'));
    }

    protected function rules()
    {
        $rules =  [
            'name' => [
                'required',
                Rule::unique('classrooms')
                    ->where(function ($query) {
                        return $query->where('branch_id', $this->branch_id);
                    })
                    ->ignore($this->room_id, 'id'),
            ],
        ];

        if (!Auth::user()->branch_id) {
            $rules['branch_id'] = 'required|exists:branches,id';
        }

        return $rules;
    }
    // Localized messages
    protected function messages()
    {
        return [
            'name.required' => __('label.name.required'),
            'name.unique'   => __('label.name.unique'),
            'branch_id.required'   => __('label.branch.required'),
        ];
    }
    
    // Create role
    public function store()
    {
        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        $this->validate();

        try {

            $classroom = Classroom::create([
                'name' => $this->name,
                'branch_id' =>  Auth::user()->branch_id ?: $this->branch_id,
                'status' => $this->status,
            ]);
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.classroom').'('.$classroom->name.' ID:'.$classroom->id.')',
                'type_id' => 3,
            ]);
            $this->closeModal();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));
            
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.store_error') . ': ' . $e->getMessage());
        }
    }


    public function edit($id)
    {
        $this->resetValidation(); 
        $this->room_id = $id;    
        $classroom = Classroom::find($id);
        $this->name = $classroom->name;
        $this->branch_id = $classroom->branch_id;
        $this->status = $classroom->status;
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
            $classroom = Classroom::findOrFail($this->room_id);
            $classroom->update([
                'name' => $this->name,
                'branch_id' =>  Auth::user()->branch_id ?: $this->branch_id,
                'status' => $this->status,
            ]);
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.classroom').'('.$classroom->name.' ID:'.$classroom->id.')',
                'type_id' => 3,
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
            $classroom = Classroom::findOrFail($id);
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.classroom').'('.$classroom->name.' ID:'.$classroom->id.')',
                'type_id' => 4,
            ]);
            $classroom->delete();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }
}
