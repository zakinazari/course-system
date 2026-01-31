<?php

namespace App\Livewire\Front\Ruling;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Website\WebMenu;
use App\Models\Website\Gazette;
use App\Models\Website\Ruling;
use App\Models\Website\RulingFile;
class Orders extends Component
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
        $orders = Ruling::query()
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
                        $q->where('ruling_number',$searchTerm);
                    });
                }
            })
            ->where('type','Order')
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.front.ruling.orders',compact('orders'));
    }
}
