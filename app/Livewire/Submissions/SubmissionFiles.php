<?php

namespace App\Livewire\Submissions;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Models\Submissions\Submission;
use App\Models\Submissions\SubmissionFile;
use Livewire\Attributes\Validate;
use \App\Jobs\UploadSubmissionFiles;
use Auth;
use DB;
use Storage;
class SubmissionFiles extends Component
{
    use WithFileUploads;
    public $submission_id;

    public $file=[];
    public $uploaded_files = [];
    public $uploading = false;
    public $uploadStatus = null;
    public $table_name='submission_files';
    protected $listeners = ['modalClosed' => 'closeModal','globalDelete' => 'handleGlobalDelete','validate-step' => 'validateStep'];

    public function mount($submission_id=null){

        $this->submission_id = $submission_id;
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
            'file.*' => 'file|max:5120|mimes:pdf,doc,docx',
        ], [
            'file.required' => __('label.file_required'),
            'file.*.file' => __('label.file_invalid'),
            'file.*.max' => __('label.file_max',['value'=>5]),
            'file.*.mimes' => __('label.file_mimes',['value'=>'pdf,doc,docx']),
        ]);


        $tempFiles = [];
        foreach ($this->file as $f) {
                $tempFiles[] = [
                'path' => $f->store('temp/submissions'),
                'original_name' => $f->getClientOriginalName(), 
            ];
        }

        $this->uploading = true;
        Submission::where('id', $this->submission_id)
        ->update(['upload_status' => 'processing']);

        UploadSubmissionFiles::dispatch(
            $this->submission_id,
            auth()->id(),
            $tempFiles
        );

        $this->file = [];

        // $this->dispatch('alert', type: 'info', message: __('label.file_sent_for_processing'));

    }

    public function checkUploadStatus()
    {
        $status = Submission::find($this->submission_id)?->upload_status;

        if ($status === 'done') {
            $this->uploading = false;
            $this->loadUploadedFiles();

            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));
        }

        if ($status === 'failed') {
            $this->uploading = false;

            $this->dispatch('alert', type: 'error', message: __('label.upload_failed'));
        }
    }

     public function handleGlobalDelete($payload)
    {

        if (!isset($payload['table']) || ($payload['table'] != $this->table_name && $payload['table'] != 'submissoin_files')) {
            return;
        }

         $this->deleteFile($payload['id']);
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
         $this->loadUploadedFiles();
        return view('livewire.submissions.submission-files');
    }


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
