<?php

namespace App\Livewire\CenterSettings\Programs;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CenterSettings\Program;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use Auth;
class ProgramList extends Component
{
     // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'program-list-addEditModal';
    public $table_name='programs';
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
    }

    public $name, $program_id,$status = 'active';

     public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
        ]);
    }
    public $search = [
            'name' => null,
        ];

        
    public function render()
    {
        $programs = Program::query()
        ->when(!empty($this->search['name']), function ($query) {
            $query->where('name', 'like', '%' . $this->search['name'] . '%');
        })
        ->paginate($this->perPage);

        return view('livewire.center-settings.programs.program-list',compact('programs'));
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:programs,name,' . $this->program_id,
        ];
    }
    // Localized messages
    protected function messages()
    {
        return [
            'name.required' => __('label.program_name.required'),
            'name.string'   => __('label.program_name.string'),
            'name.max'      => __('label.program_name.max'),
            'name.unique'   => __('label.program_name.unique'),
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

            $program = Program::create([
                'name' => $this->name,
                'status' => $this->status,
            ]);

            // ---start system log-----------
            $program = SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.program').' ('.$program->name.' ID:'.$program->id.')',
                'type_id' => 2,
            ]);
            // ---end system log-------------
            $this->closeModal();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));
            
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.store_error') . ': ' . $e->getMessage());
        }
    }


    public function edit($id)
    {
        $this->resetValidation(); 
        $this->program_id = $id;    
        $program = Program::find($id);
        $this->name = $program->name;
        $this->status = $program->status;
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
            $program = Program::findOrFail($this->program_id);
            $program->update([
                'name' => $this->name,
                'status' => $this->status,
            ]);
            // ---start system log-----------
            $program = SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.program').' ('.$program->name.' ID:'.$program->id.')',
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
            $program = Program::findOrFail($id);
             // ---start system log-----------
            $program = SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.program').' ('.$program->name.' ID:'.$program->id.')',
                'type_id' => 4,
            ]);
            // ---end system log-------------
            $program->delete();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }
}
