<?php

namespace App\Livewire\Front\Articles;
use Livewire\WithPagination;
use App\Models\Submissions\Submission;
use App\Models\Submissions\SubmissionFile;
use Livewire\Component;
use Storage;
class ArticleList extends Component
{
     // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    public $active_menu_id;
    public $keyword_id = null; 
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

    public function mount($active_menu_id = null,$keyword)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->active_menu_id = $active_menu_id;
        // -------------start for activing menu in sidebar ----------------------
        $this->keyword_id  = $keyword;
    }
    public $search = [
            'identity' => null,
            'type'=>'title',
        ];


    public function render()
    {
        $submissions = Submission::with('authors','files')
            ->when(!empty($this->search['identity']), function ($query) {

                if($this->search['type']==='title'){
                    $searchTerm = $this->search['identity'];
                     $query->where(function ($q) use ($searchTerm) {
                        $q->where('title_fa', 'like', "%{$searchTerm}%")
                        ->orWhere('title_en', 'like', "%{$searchTerm}%");
                    });
                }else{
                    $query->whereHas('authors', function ($q) {
                        $searchTerm = $this->search['identity'];
                        $q->where('given_name_fa', 'like', "%{$searchTerm}%")
                        ->orWhere('given_name_en', 'like', "%{$searchTerm}%")
                        ->orWhere('family_name_fa', 'like', "%{$searchTerm}%")
                        ->orWhere('family_name_en', 'like', "%{$searchTerm}%");
                    });
                }
            })
            ->when($this->keyword_id, function ($query) {
                $query->whereHas('keywords', function ($q) {
                    $q->where('keywords.id', $this->keyword_id);
                });
            })
            ->where('status','published')
            ->latest()
            ->paginate($this->perPage);
        foreach ($submissions as $submission) {
                $submission->files = $submission->files->where('round', $submission->round)->values();
            }

        return view('livewire.front.articles.article-list',compact('submissions'));
    }


    public function downloadFile($file_id)
    {
        $file = SubmissionFile::findOrFail($file_id);

        $path = $file->file_path;

        if (!Storage::disk('local')->exists($path)) {
            $this->dispatch('alert', 
                type: 'error',
                message: 'File not found!'
            );
            return;
        }

        return Storage::disk('local')->download($path, $file->original_name);
    }
}
