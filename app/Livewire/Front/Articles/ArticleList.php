<?php

namespace App\Livewire\Front\Articles;
use Livewire\WithPagination;
use App\Models\Submissions\Submission;
use App\Models\Submissions\SubmissionFile;
use App\Models\Submissions\MainAxis;
use App\Models\Submissions\SubAxis;
use App\Models\Website\WebMenu;
use Livewire\Component;
use Storage;
class ArticleList extends Component
{
     // -------start generals--------------------
    use WithPagination;
    public $perPage = 40;
    public $active_menu_id;
    public $active_menu;
    public $keyword_id = null; 
    protected $paginationTheme = 'custom';   

    public $main_axes = [];
    public $sub_axes = [];
    public $main_axis_id;
    public $sub_axis_id;

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
        $this->dispatch('setActiveMenuFromPage', $this->active_menu_id);
        $this->active_menu = WebMenu::with(['parent', 'grandParent', 'subMenu'])->find($this->active_menu_id);
        // -------------start for activing menu in sidebar ----------------------
        $this->keyword_id  = $keyword;
        $this->main_axes = MainAxis::all();
        $this->sub_axes = SubAxis::all();
    }
    public $search = [
            'identity' => null,
            'type'=>'title',
            'main_axis_id'=> null,
            'sub_axis_id'=> null,
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
            ->when(!empty($this->search['main_axis_id']), function ($query) {
                $query->where('main_axis_id',$this->search['main_axis_id']);
            })
            ->when(!empty($this->search['sub_axis_id']), function ($query) {
                $query->where('sub_axis_id',$this->search['sub_axis_id']);
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

    public function loadSubAxes($main_axis_id)
    {
        $this->sub_axes = SubAxis::where('main_axis_id', $main_axis_id)->get();
    }
}
