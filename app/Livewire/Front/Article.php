<?php

namespace App\Livewire\Front;

use Livewire\Component;
use App\Models\Website\WebMenu;
use App\Models\Submissions\Submission;
use App\Models\Submissions\SubmissionFile;
use Storage;
class Article extends Component
{

    public $active_menu_id;
    public $active_menu;
    public $submission_id;
    public $keywords_fa;
    public $keywords_en;
    public $similar_articles;
    public $submission;
    public function mount($active_menu_id = null,$submission_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->submission_id = $submission_id;
        $this->active_menu = WebMenu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->submission = Submission::with('files','authors','authors.type','issue','keywords')->findOrFail($this->submission_id);
        $this->submission->files = $this->submission->files->where('round', $this->submission->round)->values();

        $this->keywords_fa = $this->submission->keywords
                    ->whereNotNull('keyword_fa')
                    ->pluck('keyword_fa')
                    ->toArray();
        $this->keywords_en = $this->submission->keywords
            ->whereNotNull('keyword_en')
            ->pluck('keyword_en')
            ->toArray();
        $keyword_ids = $this->submission?->keywords?->pluck('id')
                    ->toArray();
        $this->similar_articles = Submission::whereHas('keywords', function ($query) use ($keyword_ids) {
            $query->whereIn('keywords.id', $keyword_ids);
        })
        ->where('status','published')
        ->where('id', '!=', $this->submission_id)
        ->latest()
        ->limit(10)
        ->get();

        // if (!session()->has('viewed_submission_' . $this->submission->id)) {
        //     $this->submission->increment('views');
        //     session()->put('viewed_submission_' . $this->submission->id, true);
        // }

        // ----------start submission views ----------------------------
        $submissionId = $this->submission->id;
        $sessionKey  = "viewed_submission_{$submissionId}";
        $cookieKey   = "viewed_submission_{$submissionId}";
        $ip          = request()->ip();
        $userAgent  = request()->userAgent();

        // bot filter
        foreach (['bot','crawl','spider','slurp'] as $bot) {
            if (str_contains(strtolower($userAgent), $bot)) {
                return;
            }
        }

        // session
        if (session()->has($sessionKey)) return;

        // cookie
        if (request()->hasCookie($cookieKey)) {
            session()->put($sessionKey, true);
            return;
        }

        // ip + time
        $alreadyViewed = \DB::table('submission_views')
            ->where('submission_id', $submissionId)
            ->where('ip', $ip)
            ->where('created_at', '>=', now()->subDay())
            ->exists();

        if ($alreadyViewed) {
            session()->put($sessionKey, true);
            return;
        }

        // ✅ real view
        $this->submission->increment('views');

        \DB::table('submission_views')->insert([
            'submission_id' => $submissionId,
            'ip' => $ip,
            'user_agent' => $userAgent,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        session()->put($sessionKey, true);
        cookie()->queue(cookie($cookieKey, true, 60 * 24));

        // ----------end submission views ------------------------------
    }

    public function render()
    {
       
        return view('livewire.front.article');
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
