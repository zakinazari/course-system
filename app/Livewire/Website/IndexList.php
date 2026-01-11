<?php

namespace App\Livewire\Website;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\Website\Index;
use Storage;
use Auth;
class IndexList extends Component
{
    // -------start generals--------------------
    use WithPagination;
    use WithFileUploads;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'index-list-addEditModal';
    public $table_name='indexes';
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

     public $index_id,$name,$url,$image,$existing_image;

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
        $indexes = Index::query()
            ->when(!empty($this->search['identity']), function ($query) {
                $searchTerm = $this->search['identity'];
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);
        return view('livewire.website.index-list',compact('indexes'));
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:64',
            'url' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
    
    protected function messages()
    {
        return [
           
        ];
    }

    public function store()
    {
        if (add(Auth::user()->role_ids, $this->active_menu_id)) {

            $this->validate();

            try {
                $index = Index::create([
                    'name' => $this->name,
                    'url' => $this->url,
                ]);

                if ($this->image) {

                    $file = $this->image;

                    $storedName = Str::random(40) . '.' . $file->getClientOriginalExtension();

                    $path = $file->storeAs(
                        'website/indexes/' . $index->id,
                        $storedName,
                        'public' 
                    );

                    $index->image = $path;
                    $index->save();
                }

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
        $this->index_id = $id;    
        $index = Index::find($id);
        $this->name = $index->name;
        $this->url = $index->url;
        $this->existing_image = $index->image;
        $this->editMode = true;
        $this->dispatch('open-modal', id: $this->modalId);
    }
   
    public function update()
    {
        if(edit(Auth::user()->role_ids,$this->active_menu_id)){
            $this->validate();

            try {

                $index = Index::findOrFail($this->index_id);
                $index->update([
                    'name' => $this->name,
                    'url' => $this->url,
                ]);

                if ($this->image) {

                    if ($index->image && \Storage::disk('public')->exists($index->image)) {
                        \Storage::disk('public')->delete($index->image);
                    }

                    $file = $this->image;
                    $storedName = Str::random(40) . '.' . $file->getClientOriginalExtension();

                    $path = $file->storeAs(
                        'website/indexes/' . $index->id,
                        $storedName,
                        'public' 
                    );

                    $index->image = $path;
                    $index->save();
                }
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
                $index = Index::findOrFail($id);
                if ($index->image && \Storage::disk('public')->exists($index->image)) {
                    \Storage::disk('public')->delete($index->image);
                }

                $index->delete();
                SystemLog::create([
                    'user_id' => Auth::user()->id,
                    'section' => 'این نمایه توسط این کاربر حذف شده است. (ID: '.$id.')',
                    'type_id' => 4,
                ]);
                $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
            } catch (\Exception $e) {
                $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
            }
        }else{
            $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }
    }
}
