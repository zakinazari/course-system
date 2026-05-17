<?php

namespace App\Livewire\Warehouse;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Section;
use App\Models\Warehouse\Warehouse;
use Auth;
class WarehouseList extends Component
{
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'warehouse-list-addEditModal';
    public $table_name='warehoses';
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
    public $branches, $sections;
    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->branches =  Branch::all();
        $this->sections =  Section::all();
    }

     public $warehouse_id,$name,$branch_id,$section_id;
     public $type='branch';

     public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'branches',
            'sections',
        ]);
    }
    public $search = [
            'name' => null,
            'branch_id' => null,
        ];

    public function render()
    {
        $warehouses = Warehouse::with('branch','section')
        ->when(!empty($this->search['name']), function ($query) {
            $query->where('name', 'like', '%' . $this->search['name'] . '%');
        })
        ->when(!empty($this->search['branch_id']), function ($query) {
            $query->where('branch_id',$this->search['branch_id']);
        })
        ->orderBy('id','desc')
        ->paginate($this->perPage);
        return view('livewire.warehouse.warehouse-list',compact('warehouses'));
    }

    protected function rules()
    {
        $rules =  [
            'name' => 'required|string|max:255|unique:warehouses,name,' . ($this->editMode ? $this->warehouse_id : 'NULL') . ',id',
            'section_id'=>'required',
            'type'=>'required',
            
        ];

        if (!Auth::user()->branch_id && $this->type != 'central') {
            $rules['branch_id'] = 'required';
        }

        return $rules;
    }
    // Localized messages
    protected function messages()
    {
        return [
            'name.required' => __('label.name.required'),
            'branch_id.required' => __('label.branch.required'),
            'section_id.required' => __('label.category.required'),
            'type.required' => __('label.type.required'),
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

            // جلوگیری از ایجاد بیش از یک warehouse مرکزی
            if ($this->type === 'central') {

                $exists = Warehouse::where('type', 'central')
                    ->where('section_id', $this->section_id)
                    ->whereNull('branch_id')
                    ->when($this->editMode, function ($q) {
                        $q->where('id', '!=', $this->warehouse_id);
                    })
                    ->exists();

                if ($exists) {

                    return $this->dispatch(
                        'alert',
                        type: 'error',
                        message: 'Central warehouse for this section already exists'
                    );
                }
            }

            $warehouse = Warehouse::create([
                'name' => $this->name,
                'section_id' => $this->section_id,
                'type' => $this->type,
                'branch_id' => $this->type === 'central'
                    ? null
                    : (Auth::user()->branch_id ?: $this->branch_id),
            ]);

            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.warehouse') . ' (' . $warehouse->name . ' ID:' . $warehouse->id . ')',
                'type_id' => 2,
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
        $this->warehouse_id = $id;    
        $warehouse = Warehouse::find($id);
        $this->name = $warehouse->name;
        $this->section_id = $warehouse->section_id;
        $this->branch_id = $warehouse->branch_id;
        $this->type = $warehouse->type;
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
            
            if ($this->type === 'central') {

                $exists = Warehouse::where('type', 'central')
                    ->where('section_id', $this->section_id)
                    ->whereNull('branch_id')
                    ->where('id', '!=', $this->warehouse_id)
                    ->exists();

                if ($exists) {

                    return $this->dispatch(
                        'alert',
                        type: 'error',
                        message: 'Central warehouse for this section already exists'
                    );
                }
            }
        
            $warehouse = Warehouse::findOrFail($this->warehouse_id);
            $warehouse->update([
                'name' => $this->name,
                'section_id' => $this->section_id,
                'type' => $this->type,
                'branch_id' => $this->type === 'central'
                    ? null
                    : (Auth::user()->branch_id ?: $this->branch_id),
            ]);
            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.warehouse').' ('.$warehouse->name.' ID:'.$warehouse->id.')',
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
            $warehouse = Warehouse::findOrFail($id);
             // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.warehouse').' ('.$warehouse->name.' ID:'.$warehouse->id.')',
                'type_id' => 4,
            ]);
            // ---end system log-------------
            $warehouse->delete();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }
}
