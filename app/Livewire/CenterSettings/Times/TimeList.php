<?php

namespace App\Livewire\CenterSettings\Times;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CenterSettings\Time;
use App\Models\CenterSettings\Shift;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use Auth;
use Illuminate\Validation\Rule;
class TimeList extends Component
{
     // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $shifts=[];
    public $modalId = 'time-list-addEditModal';
    public $table_name='times';
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

        $this->shifts = Shift::all();
    }

    public $name,$shift_id, $time_id,$start_time,$end_time;

    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'shifts',
        ]);
    }
    public $search = [
            'name' => null,
            'shift_id' => null,
        ];

    public function render()
    {
        $times = Time::with('shift')
        ->when(!empty($this->search['shift_id']), function ($query) {
            $query->where('shift_id',$this->search['shift_id']);
        })
        ->orderBy('id','desc')
        ->paginate($this->perPage);

        return view('livewire.center-settings.times.time-list',compact('times'));
    }

    protected function rules()
    {
        return [
            'shift_id' => 'required',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',

            'start_time' => [
                    'required',
                    'date_format:H:i',
                    Rule::unique('times')
                        ->ignore($this->time_id)
                        ->where(function ($query) {
                            return $query->where('shift_id', $this->shift_id)
                                        ->where('start_time', $this->start_time)
                                        ->where('end_time', $this->end_time);
                        }),
                ],
            ];
    }
    // Localized messages
    protected function messages()
    {
        return [
            'shift_id.required'   => __('label.shif.required'),
            'start_time.required'   => __('label.start_time.required'),
            'end_time.required'   => __('label.end_time.required'),
            'start_time.unique' => __('label.start_time.unique'),
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

            $time = Time::create([
                'shift_id' => $this->shift_id,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
            ]);
            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.time').' ('.$time?->shift->name.' ID:'.$time->id.')',
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
        $this->time_id = $id;    
        $time = Time::find($id);
        $this->shift_id = $time->shift_id;
        $this->start_time = $time->start_time
        ? $time->start_time->format('H:i')
        : null;
        $this->end_time = $time->end_time
        ? $time->end_time->format('H:i')
        : null;
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
            $time = Time::findOrFail($this->time_id);
            $time->update([
                'shift_id' => $this->shift_id,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
            ]);

            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.time').' ('.$time->shift?->name.' ID:'.$time->id.')',
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
            
            $time = Time::findOrFail($id);
            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.time').' ('.$time->shift?->name.' ID:'.$time->id.')',
                'type_id' => 4,
            ]);
            // ---end system log-------------
            $time->delete();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }
}
