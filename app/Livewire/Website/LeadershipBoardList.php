<?php

namespace App\Livewire\Website;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\Website\LeadershipBoard;
use Storage;
use Auth;
use Carbon\Carbon;
class LeadershipBoardList extends Component
{
      // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'leadership-board-list-addEditModal';
    public $table_name='leadership-board';
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

    public $board_id,$title_fa,$title_en,$content_fa,$content_en;

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
        $boards = LeadershipBoard::query()
            ->when(!empty($this->search['identity']), function ($query) {
                $searchTerm = $this->search['identity'];
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('title_fa', 'like', "%{$searchTerm}%")
                    ->orWhere('title_en', 'like', "%{$searchTerm}%");
                });
            })
            ->orderBy('id', 'asc')
            ->paginate($this->perPage);

        return view('livewire.website.leadership-board-list',compact('boards'));
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
                $board = LeadershipBoard::create([
                    'title_fa' => $this->title_fa,
                    'title_en' => $this->title_en,
                    'content_fa' => $this->content_fa,
                    'content_en' => $this->content_en,
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
        $board = LeadershipBoard::findOrFail($id);

        $this->board_id = $id;
        $this->title_fa = $board->title_fa;
        $this->title_en = $board->title_en;
        $this->content_fa = $board->content_fa;
        $this->content_en = $board->content_en;
        $this->editMode = true;

        $this->dispatch('open-modal', id: $this->modalId);
         $this->dispatch('loadEditors'); 
    }

   
    public function update()
    {
        if(edit(Auth::user()->role_ids,$this->active_menu_id)){
            $this->validate();
            try {
                $board = LeadershipBoard::findOrFail($this->board_id);
                $board->update([
                    'title_fa' => $this->title_fa,
                    'title_en' => $this->title_en,
                    'content_en' => $this->content_en,
                    'content_fa' => $this->content_fa,
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
                $board = LeadershipBoard::findOrFail($id);

                $board->delete();
                SystemLog::create([
                    'user_id' => Auth::user()->id,
                    'section' => 'این  بورد رهبری توسط این کاربر حذف شده است. (ID: '.$id.')',
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
