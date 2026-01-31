<?php

namespace App\Livewire\Website\Abstract;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\Submissions\AcceptedAbstract;
use Storage;
use Auth;
use Carbon\Carbon;
class AcceptedAbstractList extends Component
{
   
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'abstract-list-addEditModal';
    public $table_name='accepted_abstract_list';
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

    public $abstract_id,$title_fa,$title_en,$author_name,$author_last_name;

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
        $abstracts = AcceptedAbstract::query()
            ->when(!empty($this->search['identity']), function ($query) {
                $searchTerm = $this->search['identity'];
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('title_fa', 'like', "%{$searchTerm}%")
                    ->orWhere('title_en', 'like', "%{$searchTerm}%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);
        return view('livewire.website.abstract.accepted-abstract-list',compact('abstracts'));
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
                AcceptedAbstract::create([
                    'title_fa' => $this->title_fa,
                    'title_en' => $this->title_en,
                    'author_name' => $this->author_name,
                    'author_last_name' => $this->author_last_name,
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
        $abstract = AcceptedAbstract::findOrFail($id);

        $this->abstract_id = $id;
        $this->title_fa = $abstract->title_fa;
        $this->title_en = $abstract->title_en;
        $this->author_name = $abstract->author_name;
        $this->author_last_name = $abstract->author_last_name;
        $this->editMode = true;

        $this->dispatch('open-modal', id: $this->modalId);
         $this->dispatch('loadEditors'); 
    }

   
    public function update()
    {
        if(edit(Auth::user()->role_ids,$this->active_menu_id)){
            $this->validate();
            try {
                $abstract = AcceptedAbstract::findOrFail($this->abstract_id);
                $abstract->update([
                    'title_fa' => $this->title_fa,
                    'title_en' => $this->title_en,
                    'author_name' => $this->author_name,
                    'author_last_name' => $this->author_last_name,
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
                $abstract = AcceptedAbstract::findOrFail($id);
                SystemLog::create([
                    'user_id' => Auth::user()->id,
                    'section' => 'این  خلاصه های پذیرفته شده  توسط این کاربر حذف شده است. (ID: '.$id.')'.'عنوان: '.$abstract->title_fa,
                    'type_id' => 4,
                ]);
                $abstract->delete();
                $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
            } catch (\Exception $e) {
                $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
            }
        }else{
            $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }
    }
}
