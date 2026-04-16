<?php

namespace App\Livewire\CenterSettings\Shifts;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CenterSettings\Shift;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use Auth;
use Illuminate\Validation\Rule;
class ShiftList extends Component
{
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'shift-list-addEditModal';
    public $table_name='shifts';
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

    public $name,$shift_id;

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
        $shifts = Shift::query()
        ->when(!empty($this->search['name']), function ($query) {
            $query->where('name', 'like', '%' . $this->search['name'] . '%');
        })
        ->orderBy('id','desc')
        ->paginate($this->perPage);

        return view('livewire.center-settings.shifts.shift-list',compact('shifts'));
    }

    protected function rules()
    {
        return [
            'name' => 'required|unique:shifts,name,' . $this->shift_id,
        ];
    }
    // Localized messages
    protected function messages()
    {
        return [
            'name.required' => __('label.name.required'),
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

            $shift = Shift::create([
                'name' => $this->name,
            ]);
            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.shift').' ('.$shift->name.' ID:'.$shift->id.')',
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
        $this->shift_id = $id;    
        $shift = Shift::find($id);
        $this->name = $shift->name;
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
            $shift = Shift::findOrFail($this->shift_id);
            $shift->update([
                'name' => $this->name,
            ]);

            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.shift').' ('.$shift->name.' ID:'.$shift->id.')',
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
            
            $shift = Shift::findOrFail($id);
            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.shift').' ('.$shift->name.' ID:'.$shift->id.')',
                'type_id' => 4,
            ]);
            // ---end system log-------------
            $shift->delete();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }
}
