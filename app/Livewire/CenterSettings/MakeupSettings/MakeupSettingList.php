<?php

namespace App\Livewire\CenterSettings\MakeupSettings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\Financial\MakeupSetting;
use Auth;
class MakeupSettingList extends Component
{

    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'makeup-setting-list-addEditModal';
    public $table_name='makeup_settings';
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

    public $makeup_setting_id,$name,$exam_valid_days,$fee_valid_days,$fee_amount,$note,$status = true;

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
        $makeup_settings = MakeupSetting::query()
        ->when(!empty($this->search['name']), function ($query) {
            $query->where('name', 'like', '%' . $this->search['name'] . '%');
        })
        ->paginate($this->perPage);
        return view('livewire.center-settings.makeup-settings.makeup-setting-list',compact('makeup_settings'));
    }

    protected function rules()
    {
        return [

            'name' => 'required|string|max:255',
            'exam_valid_days' => 'required|numeric|min:1',
            'fee_valid_days' => 'required|numeric|min:1',
            'fee_amount' => 'required|numeric|min:0',
        ];
    }
    // Localized messages
    protected function messages()
    {
        return [
            'name.required' => __('label.name.required'),
            'exam_valid_days.required'   => __('label.exam_valid_days.required'),
            'fee_valid_days.required'   => __('label.fee_valid_days.required'),
            'fee_amount.required'   => __('label.fee_amount.required'),
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

            if ($this->status) {
                MakeupSetting::where('status', 1)->update([
                    'status' => 0
                ]);
            }

            $makeup_setting = MakeupSetting::create([
                'name' => $this->name,
                'exam_valid_days' => $this->exam_valid_days,
                'fee_valid_days' => $this->fee_valid_days,
                'fee_amount' => $this->fee_amount,
                'note' => $this->note,
                'status' => $this->status,
            ]);

            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.makeup_setting').' ('.$makeup_setting->name.' ID:'.$makeup_setting->id.')',
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
        $this->makeup_setting_id = $id;    
        $makeup_setting = MakeupSetting::find($id);
        $this->name = $makeup_setting->name;
        $this->exam_valid_days = $makeup_setting->exam_valid_days;
        $this->fee_valid_days = $makeup_setting->fee_valid_days;
        $this->fee_amount = $makeup_setting->fee_amount;
        $this->note = $makeup_setting->note;
        $this->status = $makeup_setting->status;
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
            $makeup_setting = MakeupSetting::findOrFail($this->makeup_setting_id);

            if ($this->status) {

                MakeupSetting::where('id', '!=', $this->makeup_setting_id)
                    ->update([
                        'status' => 0
                    ]);
            }

            $makeup_setting->update([
                'name' => $this->name,
                'exam_valid_days' => $this->exam_valid_days,
                'fee_valid_days' => $this->fee_valid_days,
                'fee_amount' => $this->fee_amount,
                'note' => $this->note,
                'status' => $this->status,
            ]);
            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.makeup_setting').' ('.$makeup_setting->name.' ID:'.$makeup_setting->id.')',
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
            $makeup_setting = MakeupSetting::findOrFail($id);
             // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.makeup_setting').' ('.$makeup_setting->name.' ID:'.$makeup_setting->id.')',
                'type_id' => 4,
            ]);
            // ---end system log-------------
            $makeup_setting->delete();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }
}
