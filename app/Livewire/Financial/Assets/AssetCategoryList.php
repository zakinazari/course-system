<?php

namespace App\Livewire\Financial\Assets;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Financial\AssetCategory;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use Auth;
class AssetCategoryList extends Component
{
     // -------start generals--------------------
    use WithPagination;
    public $perPage = 5;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'asset-category-list-addEditModal';
    public $table_name='asset_categories';
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

    public $name,$code, $category_id;

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
        $categories = AssetCategory::query()
        ->when(!empty($this->search['name']), function ($query) {
            $query->where('name', 'like', '%' . $this->search['name'] . '%');
        })
        ->paginate($this->perPage);

        return view('livewire.financial.assets.asset-category-list',compact('categories'));
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:asset_categories,name,' . $this->category_id,
            'code' => 'required|string|max:255|unique:asset_categories,code,' . $this->category_id,
        ];
    }
    // Localized messages
    protected function messages()
    {
        return [
            'name.required' => __('label.name.required'),
            'name.unique'   => __('label.name.unique'),
            'code.unique'   => __('label.code.required'),
            'code.unique'   => __('label.code.unique'),
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

            $category = AssetCategory::create([
                'name' => $this->name,
                'code' => $this->code,
            ]);
            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.asset_category').' ('.$category->name.' ID:'.$category->id.')',
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
        $this->category_id = $id;    
        $category = AssetCategory::find($id);
        $this->name = $category->name;
        $this->code = $category->code;
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
            $category = AssetCategory::findOrFail($this->category_id);
            $category->update([
                'name' => $this->name,
                'code' => $this->code,
            ]);
             // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.asset_category').' ('.$category->name.' ID:'.$category->id.')',
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
            $category = AssetCategory::findOrFail($id);
            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.asset_category').' ('.$category->name.' ID:'.$category->id.')',
                'type_id' => 4,
            ]);
            // ---end system log-------------
            $category->delete();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }
}
