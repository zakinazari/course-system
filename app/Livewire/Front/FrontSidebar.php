<?php

namespace App\Livewire\Front;

use Livewire\Component;
use App\Models\Website\Post;
use App\Models\Website\Index;
use App\Models\Website\LeadershipBoard;
use App\Models\Submissions\Keyword;
class FrontSidebar extends Component
{
   
    public function render()
    {
        $posts = Post::where('status','published')->orderBy('published_at','desc')->limit(5)->get();
        $indexes = Index::all();
        $boards = LeadershipBoard::all();

        $keywords = Keyword::withCount(['submissions' => function ($q) {
            $q->where('status', 'published');
        }])
        ->whereHas('submissions', function ($q) {
            $q->where('status', 'published');
        })
        ->where('keyword_en', null)
        ->orderBy('submissions_count', 'desc')
        ->limit(50)
        ->get();
        
        return view('livewire.front.front-sidebar',compact('posts','indexes','boards','keywords'));
    }
}
