<?php

namespace App\Livewire\CenterSettings\Branches;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CenterSettings\Branch;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use Auth;
class BranchList extends Component
{
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 5;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'branch-list-addEditModal';
    public $table_name='branches';
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

    public $name, $branch_id,$code,$status = 'active',$location;

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
        $branches = Branch::query()
        ->when(!empty($this->search['name']), function ($query) {
            $query->where('name', 'like', '%' . $this->search['name'] . '%');
        })
        ->paginate($this->perPage);

        return view('livewire.center-settings.branches.branch-list',compact('branches'));
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:branches,name,' . $this->branch_id,
            'code' => 'required|unique:branches,code,' . $this->branch_id,
        ];
    }
    // Localized messages
    protected function messages()
    {
        return [
            'name.required' => __('label.branch_name.required'),
            'name.string'   => __('label.branch_name.string'),
            'name.max'      => __('label.branch_name.max'),
            'name.unique'   => __('label.branch_name.unique'),
            'code.unique'   => __('label.branch_code.unique'),
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

            $branch = Branch::create([
                'name' => $this->name,
                'location' => $this->location,
                'status' => $this->status,
            ]);
            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.branch').' ('.$branch->name.' ID:'.$branch->id.')',
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
        $this->branch_id = $id;    
        $branch = Branch::find($id);
        $this->name = $branch->name;
        $this->code = $branch->code;
        $this->location = $branch->location;
        $this->status = $branch->status;
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
            $branch = Branch::findOrFail($this->branch_id);
            $branch->update([
                'name' => $this->name,
                'code' => $this->code,
                'location' => $this->location,
                'status' => $this->status,
            ]);
             // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.branch').' ('.$branch->name.' ID:'.$branch->id.')',
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
            $branch = Branch::findOrFail($id);
            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.branch').' ('.$branch->name.' ID:'.$branch->id.')',
                'type_id' => 4,
            ]);
            // ---end system log-------------
            $branch->delete();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }
}
