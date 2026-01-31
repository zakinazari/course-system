<?php

namespace App\Livewire\Front\Gazette;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Website\WebMenu;
use App\Models\Website\Gazette;
class Gazettes extends Component
{
     // -------start generals--------------------
    use WithPagination;
    public $perPage = 50;
    public $active_menu_id;
    public $active_menu;
    protected $paginationTheme = 'custom';   

    public function updatingPerPage()
    {
        $this->resetPage();
    }
    public function applySearch()
    {
        if (empty($this->search['identity'])) {
            $this->resetSearch();
            return;
        }
        $this->resetPage();
    }
    public function resetSearch()
    {
        $this->search['identity'] = null;
        $this->keyword_id = null;
        $this->resetPage();
    }
    // ---------------------------------end generals-------------

    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->active_menu_id = $active_menu_id;
        $this->dispatch('setActiveMenuFromPage', $this->active_menu_id);
        $this->active_menu = WebMenu::with(['parent', 'grandParent', 'subMenu'])->find($this->active_menu_id);
        // -------------start for activing menu in sidebar ----------------------
    }
    public $search = [
            'identity' => null,
            'type'=>'title',
        ];

    public function render()
    {
        $gazettes = Gazette::with('ruling','ruling.files')
        ->when(!empty($this->search['identity']), function ($query) {
            if($this->search['type']==='title'){
                $searchTerm = $this->search['identity'];
                    $query->where(function ($q) use ($searchTerm) {
                    $q->where('title_fa', 'like', "%{$searchTerm}%")
                    ->orWhere('title_en', 'like', "%{$searchTerm}%");
                });
            }else{
                $searchTerm = $this->search['identity'];
                    $query->where(function ($q) use ($searchTerm) {
                    $q->where('gazette_number',$searchTerm);
                });
            }
        })
        ->where('status',true)
        // ->latest()
        ->paginate($this->perPage);
        return view('livewire.front.gazette.gazettes',compact('gazettes'));
    }
}
