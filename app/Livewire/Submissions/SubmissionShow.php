<?php

namespace App\Livewire\Submissions;
use App\Models\Submissions\Submission;
use App\Models\Submissions\SubmissionFile;
use App\Models\Submissions\SubmissionAuthor;
use Livewire\Component;
use Storage;
class SubmissionShow extends Component
{
    public $submission;
    public $section;
    public $uploaded_files;
    public $authors;
    public $keywords_en;
    public $keywords_fa;
    public function mount($submission_id = null,$section = null){
        if($section=='files'){
            $this->uploaded_files = SubmissionFile::where('submission_id', $submission_id)
                                ->latest()
                                ->get();
           
        }elseif($section=='contributors'){
            $this->authors = SubmissionAuthor::with('country','Type','educationDegree','AcademicRank','province')->where('submission_id', $submission_id)
                                ->orderBy('type_id','ASC')
                                ->get();
        }else{
            $this->submission  = Submission::findOrFail($submission_id);

            $this->keywords_fa = $this->submission->keywords
                    ->whereNotNull('keyword_fa')
                    ->pluck('keyword_fa')
                    ->toArray();
            $this->keywords_en = $this->submission->keywords
                ->whereNotNull('keyword_en')
                ->pluck('keyword_en')
                ->toArray();
        }
        
        $this->section = $section;
    }
    public function render()
    {
        return view('livewire.submissions.submission-show');
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
