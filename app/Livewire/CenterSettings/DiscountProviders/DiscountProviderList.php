<?php

namespace App\Livewire\CenterSettings\DiscountProviders;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\CenterSettings\DiscountProvider;
use Auth;
class DiscountProviderList extends Component
{
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'discount-provider-list-addEditModal';
    public $table_name='discount-providers';
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

    public $name,$last_name,$phone_no,$provider_id,$monthly_discount_total,$status = true;

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
        $discount_providers = DiscountProvider::query()
        ->when(!empty($this->search['name']), function ($query) {
            $query->where('name', 'like', '%' . $this->search['name'] . '%');
        })
        ->paginate($this->perPage);
        return view('livewire.center-settings.discount-providers.discount-provider-list',compact('discount_providers'));
    }

    protected function rules()
    {
        return [
            'name' => 'required',
            'phone_no' => 'required|string|max:255|unique:discount_providers,phone_no,' . ($this->editMode ? $this->provider_id : 'NULL') . ',id',
            'monthly_discount_total' => 'required|numeric|min:0',
        ];
    }
    // Localized messages
    protected function messages()
    {
        return [
            'name.required' => __('label.name.required'),
            'phone_no.required'   => __('label.phone_no.required'),
            'phone_no.unique'   => __('label.phone_no.unique'),
            'monthly_discount_total.required'      => __('label.monthly_discount_total.required'),
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

            $provider = DiscountProvider::create([
                'name' => $this->name,
                'last_name' => $this->last_name,
                'phone_no' => $this->phone_no,
                'monthly_discount_total' => $this->monthly_discount_total,
                'status' => $this->status,
            ]);

            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.discount_provider').' ('.$provider->name.' ID:'.$provider->id.')',
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
        $this->provider_id = $id;    
        $provider = DiscountProvider::find($id);
        $this->name = $provider->name;
        $this->last_name = $provider->last_name;
        $this->phone_no = $provider->phone_no;
        $this->monthly_discount_total = $provider->monthly_discount_total;
        $this->status = $provider->status;
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
            $provider = DiscountProvider::findOrFail($this->provider_id);
            $provider->update([
                'name' => $this->name,
                'last_name' => $this->last_name,
                'phone_no' => $this->phone_no,
                'monthly_discount_total' => $this->monthly_discount_total,
                'status' => $this->status,
            ]);
            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.discount_provider').' ('.$provider->name.' ID:'.$provider->id.')',
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
            $provider = DiscountProvider::findOrFail($id);
             // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.discount_provider').' ('.$provider->name.' ID:'.$provider->id.')',
                'type_id' => 4,
            ]);
            // ---end system log-------------
            $provider->delete();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }
}
