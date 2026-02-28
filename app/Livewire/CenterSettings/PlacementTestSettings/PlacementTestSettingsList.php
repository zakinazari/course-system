<?php

namespace App\Livewire\CenterSettings\PlacementTestSettings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CenterSettings\PlacementTestSetting;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use Illuminate\Validation\Rule;
use Auth;
class PlacementTestSettingsList extends Component
{
     // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $branches=[];
    public $modalId = 'placement-test-settings-addEditModal';
    public $table_name='palcement-test-settings';
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

    public $setting_id, $validity_months = 3,$fee_amount;
    public $has_fee=false;
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
                'branch_id' => null,
            ];
    public function render()
    {
        $settings = PlacementTestSetting::orderBy('id','desc')
        ->paginate($this->perPage);

        return view('livewire.center-settings.placement-test-settings.placement-test-settings-list',compact('settings'));
    }

    protected function rules()
    {
        $rules = [
            'validity_months' => 'required|integer|min:1',
            'has_fee'         => 'required|boolean',
        ];

        if ($this->has_fee) {
            $rules['fee_amount'] = 'required|numeric|min:0';
        }

    
        return $rules;
    }
    // Localized messages
    protected function messages()
    {
        return [
            'validity_months.required' => __('label.validity_months.required'),
            'fee_amount.required'   => __('label.fee_amount.required'),
            'has_fee.required'   => __('label.has_fee.required'),
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
            $existing = PlacementTestSetting::first();

            if ($existing) {
                return $this->dispatch(
                    'alert',
                    type: 'error',
                    message: __('label.setting_already_exists')
                );
            }

            $setting= PlacementTestSetting::create([
                'validity_months' => $this->validity_months,
                'has_fee' => $this->has_fee,
                'fee_amount' => $this->fee_amount,
            ]);
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.placement_test_settings').'('.$setting->branch?->name.' ID:'.$setting->id.')',
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
        $this->setting_id = $id;    
        $setting = PlacementTestSetting::find($id);
        $this->validity_months = $setting->validity_months;
        $this->has_fee = $setting->has_fee;
        $this->fee_amount = $setting->fee_amount;
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
            $setting = PlacementTestSetting::findOrFail($this->setting_id);
            $setting->update([
                'validity_months' => $this->validity_months,
                'has_fee' => $this->has_fee,
                'fee_amount' => $this->fee_amount,
            ]);
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.placement_test_settings').'( ID:'.$setting->id.')',
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
            $setting = PlacementTestSetting::find($id);
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.placement_test_settings').'('.$setting->id.')',
                'type_id' => 4,
            ]);
            $setting->delete();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }
}
