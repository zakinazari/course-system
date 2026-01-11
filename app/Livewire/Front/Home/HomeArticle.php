<?php

namespace App\Livewire\Front\Home;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Submissions\Submission;

class HomeArticle extends Component
{
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    public $active_menu_id;
    protected $paginationTheme = 'custom';   

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
        $this->active_menu_id = $active_menu_id;
        // -------------start for activing menu in sidebar ----------------------
    }
    public $search = [
            'identity' => null,
        ];

        public function applyKeywordFilter($keywordId)
        {
            $this->keyword_id = $keywordId;
            $this->resetPage(); 
        }
    public function render()
    {
        $submissions = Submission::with('authors','files')
            ->when(!empty($this->search['identity']), function ($query) {
                $searchTerm = $this->search['identity'];
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('title_fa', 'like', "%{$searchTerm}%")
                    ->orWhere('title_en', 'like', "%{$searchTerm}%");
                });
            })
            ->where('status','published')
            ->orderBy('id', 'desc')
            ->get();
        foreach ($submissions as $submission) {
                $submission->files = $submission->files->where('round', $submission->round)->values();
            }
        return view('livewire.front.home.home-article',compact('submissions'));
    }
}
