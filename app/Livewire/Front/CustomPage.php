<?php

namespace App\Livewire\Front;
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\Issues\Issue;
use App\Models\Website\WebMenu;
use App\Models\Website\WebPageFile;
use App\Models\Website\Post;
use App\Models\Website\Gazette;
use App\Models\Submissions\Submission;
use App\Models\Submissions\SubmissionFile;
use App\Models\Website\ScientificBoard;
use Storage;
class CustomPage extends Component
{
    use WithPagination;
    protected $paginationTheme = 'custom';   
    public function updatingPerPage()
    {
        $this->resetPage();
    }
    public function applySearch()
    {
        $this->resetPage();
    }
    public $active_menu_id;
    public $active_menu;
    public $keyword;
    public function mount($active_menu_id = null,$slug)
    {
        $this->keyword = request()->query('keyword');
        if($slug){
            $this->active_menu_id = WebMenu::where('slug',$slug)->value('id');
        }else{
            $this->active_menu_id = $active_menu_id;
        }
        
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $this->active_menu_id);
        $this->active_menu = WebMenu::with(['parent', 'grandParent', 'subMenu'])->find($this->active_menu_id);
        // -------------start for activing menu in sidebar ----------------------
    }
    
    public function render()
    {   
        
        $menu = WebMenu::with('page','page.files')->find($this->active_menu_id);
        
        if($menu->slug == 'home'){
            return view('livewire.front.home-page',compact('menu'));
        }elseif($menu->slug == 'post'){
         
            $posts = Post::with('user')->orderBy('created_at','desc')->paginate(10);
            return view('livewire.front.posts-page',compact('menu','posts'));

        }elseif($menu->slug == 'gazette'){

            return view('livewire.front.gazette.gazettes-page');

        }elseif($menu->slug == 'orders'){

            return view('livewire.front.ruling.orders-page');

        }elseif($menu->slug == 'decrees'){

            return view('livewire.front.ruling.decrees-page');

        }elseif($menu->slug == 'articles'){

            return view('livewire.front.articles.article-list-page');

        }elseif($menu->slug == 'accepted_abstracts'){

            return view('livewire.front.articles.accepted-abstracts-page');

        }elseif($menu->slug =='scientific_board'){
            $scientific_board = ScientificBoard::with('members')->get();
            return view('livewire.front.scientific-board-page',compact('scientific_board'));
            
        }else{

            return view('livewire.front.custom-page',compact('menu'));
        }
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

    public function downloadPageFile($file_id)
    {
        $file = WebPageFile::findOrFail($file_id);

        $path = $file->file_path;

        if (!Storage::disk('local')->exists($path)) {
            $this->dispatch('alert', 
                type: 'error',
                message: 'File not found!'
            );
            return;
        }

        return Storage::disk('local')->download($path, $file->file_name);
    }
}
