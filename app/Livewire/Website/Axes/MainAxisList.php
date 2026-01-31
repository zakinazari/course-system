<?php

namespace App\Livewire\Website\Axes;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\Submissions\MainAxis;
use Storage;
use Auth;
use Carbon\Carbon;
class MainAxisList extends Component
{
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'main-axis-list-addEditModal';
    public $table_name='main_axes';
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
         $this->dispatch('loadEditors'); 
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
    public $main_axis_id,$title_fa,$title_en;

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
            'identity' => null,
        ];

    public function render()
    {
        $main_axes = MainAxis::query()
            ->when(!empty($this->search['identity']), function ($query) {
                $searchTerm = $this->search['identity'];
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('title_fa', 'like', "%{$searchTerm}%")
                    ->orWhere('title_en', 'like', "%{$searchTerm}%");
                });
            })
            // ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.website.axes.main-axis-list',compact('main_axes'));
    }

     protected function rules()
    {
        return [
            'title_fa' => 'required|string|max:255',
        ];
    }
    
    protected function messages()
    {
        return [
            'title_fa.required' => __('label.title.required'),
        ];
    }

    public function store()
    {
        if (add(Auth::user()->role_ids, $this->active_menu_id)) {

            $this->validate();

            try {
                MainAxis::create([
                    'title_fa' => $this->title_fa,
                    'title_en' => $this->title_en,
                ]);

                $this->closeModal();
                $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));

            } catch (\Exception $e) {

                $this->dispatch('alert', type: 'error', message: __('label.store_error').' : '. $e->getMessage());
            }

        } else {
            $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }
    }

    public function edit($id)
    {
        $this->resetValidation();
        $main_axis = MainAxis::findOrFail($id);

        $this->main_axis_id = $id;
        $this->title_fa = $main_axis->title_fa;
        $this->title_en = $main_axis->title_en;
        $this->editMode = true;

        $this->dispatch('open-modal', id: $this->modalId);
         $this->dispatch('loadEditors'); 
    }

   
    public function update()
    {
        if(edit(Auth::user()->role_ids,$this->active_menu_id)){
            $this->validate();
            try {
                $main_axis = MainAxis::findOrFail($this->main_axis_id);
                $main_axis->update([
                    'title_fa' => $this->title_fa,
                    'title_en' => $this->title_en,
                ]);

                $this->closeModal();
                $this->dispatch('alert', type: 'success', message: __('label.successfully_updated'));
            } catch (\Exception $e) {
            
                $this->dispatch('alert', type: 'error', message: __('label.update_error').' : '. $e->getMessage());
            }
        }else{
            $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
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
        if(delete(Auth::user()->role_ids,$this->active_menu_id)){
            try {
                $main_axis = MainAxis::findOrFail($id);
                SystemLog::create([
                    'user_id' => Auth::user()->id,
                    'section' => 'این  محور اصلی  توسط این کاربر حذف شده است. (ID: '.$id.')'.'عنوان: '.$main_axis->title_fa,
                    'type_id' => 4,
                ]);
                $main_axis->delete();
                $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
            } catch (\Exception $e) {
                $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
            }
        }else{
            $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }
    }
}
