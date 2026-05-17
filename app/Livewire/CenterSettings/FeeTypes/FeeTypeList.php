<?php

namespace App\Livewire\CenterSettings\FeeTypes;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\Financial\FeeType;
use App\Models\CenterSettings\Section;
use Auth;
class FeeTypeList extends Component
{
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'fee-type-list-addEditModal';
    public $table_name='fee_types';
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
    public $sections = [];
    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

         $this->sections = Section::all();
    }

     public $fee_type_id,$name,$code,$fee_amount,$has_fees,$section_id;

     public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'sections',
        ]);
    }
    public $search = [
            'name' => null,
            'section_id' => null,
        ];

    public function render()
    {
        $fee_types = FeeType::with('section')
        ->when(!empty($this->search['name']), function ($query) {
            $query->where('name', 'like', '%' . $this->search['name'] . '%');
        })
        ->when(!empty($this->search['section_id']), function ($query) {
            $query->where('section_id', $this->search['section_id']);
        })
        ->paginate($this->perPage);

        return view('livewire.center-settings.fee-types.fee-type-list',compact('fee_types'));
    }

    protected function rules()
    {
        return [
            'name' => 'required',
            'code' => 'required|string|max:255|unique:fee_types,code,' . ($this->editMode ? $this->fee_type_id : 'NULL') . ',id',
            'fee_amount' => 'required|numeric|min:0',
            'section_id' => 'required',
        ];
    }
    // Localized messages
    protected function messages()
    {
        return [
            'name.required' => __('label.name.required'),
            'code.required'   => __('label.code.required'),
            'fee_amount.required'      => __('label.amount.required'),
            'section_id.required'   => __('label.section.required'),
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

            $fee_type = FeeType::create([
                'name' => $this->name,
                'code' => $this->code,
                'fee_amount' => $this->fee_amount,
                'section_id' => $this->section_id,
            ]);

            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.fee_type').' ('.$fee_type->name.' ID:'.$fee_type->id.')',
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
        $this->fee_type_id = $id;    
        $fee_type = FeeType::with('studentFees')->find($id);
        $this->name = $fee_type->name;
        $this->code = $fee_type->code;
        $this->fee_amount = $fee_type->fee_amount;
        $this->section_id = $fee_type->section_id;
        $this->has_fees = $fee_type->studentFees()->exists();
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
            $fee_type = FeeType::findOrFail($this->fee_type_id);
            $fee_type->update([
                'name' => $this->name,
                'code' => $this->code,
                'fee_amount' => $this->fee_amount,
                'section_id' => $this->section_id,
            ]);
            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.fee_type').' ('.$fee_type->name.' ID:'.$fee_type->id.')',
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
            $fee_type = FeeType::findOrFail($id);
             // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.fee_type').' ('.$fee_type->name.' ID:'.$fee_type->id.')',
                'type_id' => 4,
            ]);
            // ---end system log-------------
            $fee_type->delete();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }
}
