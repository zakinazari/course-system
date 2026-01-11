<?php

namespace App\Livewire\Submissions;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Models\Submissions\Submission;
use App\Models\Submissions\SubmissionFile;
use Livewire\Attributes\Validate;
use Auth;
use DB;
use Storage;
class SubmissionFiles extends Component
{
    use WithFileUploads;
    public $submission_id;

    public $file=[];
    public $uploaded_files = [];

    public function mount($submission_id=null){

        $this->submission_id = $submission_id;

        $this->loadUploadedFiles();
    }
   
    public function loadUploadedFiles()
    {
        $this->uploaded_files = SubmissionFile::where('submission_id', $this->submission_id)
                                ->latest()
                                ->get();
    }

 
   public function updatedFile()
    {
       
        $this->resetErrorBag('file');

        foreach ($this->file as $i => $f) {
            if (!in_array($f->getClientOriginalExtension(), ['pdf','doc','docx','xls','xlsx','csv'])) {
                $this->addError(
                    "file.$i",
                    __('label.file_mimes', ['value' => 'pdf,doc,docx,xls,xlsx,csv'])
                );
            }
        }
    }

    public function uploadFile()
    {
    
       $this->validate([
            'file' => 'required|array',
            'file.*' => 'file|max:2048|mimes:pdf,doc,docx,xls,xlsx,csv',
        ], [
            'file.required' => __('label.file_required'),
            'file.*.file' => __('label.file_invalid'),
            'file.*.max' => __('label.file_max',['value'=>2]),
            'file.*.mimes' => __('label.file_mimes',['value'=>'pdf,doc,docx,xls,xlsx,csv']),
        ]);

        DB::beginTransaction();
        try {

            $submission = Submission::findOrFail($this->submission_id);
            if ($submission->status === 'revision_required') {
                $submission->update([
                    'round' => $submission->round + 1,
                    'status' => 'submitted', 
                ]);
            }

            $current_round = $submission->round;

            foreach ($this->file as $f) {
                $originalName = $f->getClientOriginalName();
                $mime = $f->getClientMimeType();
                $size = $f->getSize();
                $storedName = Str::random(40) . '.' . $f->getClientOriginalExtension();
                
                $path = $f->storeAs('submissions/' . $this->submission_id, $storedName, 'local');
                
                SubmissionFile::create([
                    'submission_id' => $this->submission_id,
                    'file_type' => 'manuscript',
                    'original_name' => $originalName,
                    'file_path' => $path,
                    'uploaded_by' => auth()->id(),
                    'size' => $size,
                    'mime' => $mime,
                    'round' => $current_round,
                    'uploaded_at' => now(),
                ]);

                 $f->delete();
            }
            DB::commit();
            $this->file = [];
            $this->loadUploadedFiles();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));
            $this->dispatch('refreshSubmissionViewComponent');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.store_error') . $e->getMessage());
        }
    }

    public function deleteFile($id)
    {
        $file = SubmissionFile::findOrFail($id);

        if (Storage::disk('local')->exists($file->file_path)) {
            Storage::disk('local')->delete($file->file_path);
        }

        $file->delete();
        $this->file=[];
        $this->loadUploadedFiles();
        $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
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

    public function render()
    {
        
        return view('livewire.submissions.submission-files');
    }


    protected $listeners = ['validate-step' => 'validateStep'];
    public function validateStep($step)
    {
        if ($step != 2) return;
        $uploaded_files = SubmissionFile::where('submission_id', $this->submission_id)->count();
        if ($uploaded_files > 0) {
            $this->dispatch('stepValidated', is_valid: true);
        } else {
            $this->dispatch('stepValidated', is_valid: false);
        }
    }
}
