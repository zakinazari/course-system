<?php

namespace App\Livewire\CenterSettings\LeaveType;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Hr\LeaveType;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use Illuminate\Validation\Rule;
use Auth;
class LeaveTypeList extends Component
{
     // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $branches=[];
    public $modalId = 'leave_type-addEditModal';
    public $table_name='leave_types';
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

    public $leave_type_id,$name,$is_paid = true,$yearly_limit,$status = true;

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
        ];


    public function render()
    {
         $leave_types = LeaveType::query()
        ->when(!empty($this->search['name']), function ($query) {
            $query->where('name', 'like', '%' . $this->search['name'] . '%');
        })
       
        ->orderBy('id','desc')
        ->paginate($this->perPage);

        return view('livewire.center-settings.leave-type.leave-type-list',compact('leave_types'));
    }

    protected function rules()
    {
        $rules =  [
            'name' => 'required|string|max:255|unique:leave_types,name,'.$this->leave_type_id.',id',
        ];



        return $rules;
    }
    // Localized messages
    protected function messages()
    {
        return [
            'name.required' => __('label.name.required'),
            'name.unique'   => __('label.name.unique'),
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

            $leave_type = LeaveType::create([
                'name' => $this->name,
                'yearly_limit' => $this->yearly_limit,
                'status' => (bool) $this->status,
                'is_paid' => (bool) $this->is_paid,
            ]);

            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.leave_type').'('.$leave_type->name.' ID:'.$leave_type->id.')',
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
        $this->leave_type_id = $id;    
        $leave_type = LeaveType::find($id);
        $this->name = $leave_type->name;
        $this->yearly_limit = $leave_type->yearly_limit;
        $this->is_paid = $leave_type->is_paid;
        $this->status = $leave_type->status;
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
            $leave_type = LeaveType::findOrFail($this->leave_type_id);
            $leave_type->update([
                'name' => $this->name,
                'yearly_limit' => $this->yearly_limit,
                'status' => (bool) $this->status,
                'is_paid' => (bool) $this->is_paid,
            ]);
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.leave_type').'('.$leave_type->name.' ID:'.$leave_type->id.')',
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
            $leave_type = LeaveType::findOrFail($id);
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.leave_type').'('.$leave_type->name.' ID:'.$leave_type->id.')',
                'type_id' => 4,
            ]);
            $leave_type->delete();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }
}
