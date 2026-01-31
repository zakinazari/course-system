<?php

namespace App\Livewire\Assignments\Reviewer;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use App\Models\Submissions\Review;
use App\Models\Submissions\ReviewDecision;
use App\Models\Submissions\ReviewFile;
use App\Models\Submissions\Submission;
use Storage;
use DB;
class ReviewerAssignmentReview extends Component
{
    use WithFileUploads;
    public $review_id;
    public $review;
    public $comments_fa;
    public $comments_en;
    public $recommendation;
    public $file=[];
    public $uploaded_files=[];
    public $table_name='review_decisions';
    public $editMode = false;
    public $decision;
    public $submission;
    protected $listeners = ['modalClosed' => 'closeModal','globalDelete' => 'handleGlobalDelete'];
    public function mount($review_id = null)
    {
        $this->review = Review::findOrFail($review_id);
        $this->submission = Submission::findOrFail($this->review?->submission_id);
        $this->review_id = $review_id;
        $this->decision = ReviewDecision::where('review_id',$this->review_id)->first();
        $this->loadUploadedFiles();
    }


    public function loadUploadedFiles()
    {
        $this->uploaded_files = ReviewFile::where('type','reviewed')->where('review_id', $this->review_id)
                                ->latest()
                                ->get();
    }
    public function resetInputFields(){
        $this->resetExcept([
            'review_id',
            'file',
            'modalId',
            'search',
            'submission',
            'review',
        ]);
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'file') {

        
            $this->resetErrorBag('file.*');

            foreach ($this->file as $i => $f) {
                try {
                    $this->validate([
                        "file.$i" => 'file|max:10240|mimes:pdf,doc,docx',
                    ]);
                } catch (\Illuminate\Validation\ValidationException $e) {

                    
                    unset($this->file[$i]);

                   
                    if (file_exists($f->getRealPath())) {
                        @unlink($f->getRealPath());
                    }

                 
                    $this->dispatch('alert', type: 'error', message: 'This file type is not allowed');
                }
            }

          
            $this->file = array_values($this->file);

        } else {

           
            if (array_key_exists($propertyName, $this->rules())) {

               
                $this->resetErrorBag($propertyName);

                $this->validateOnly($propertyName);
            }
        }
    }

    protected function rules()
    {
        $rules = [
            'review_id' => 'required',
            'recommendation' => 'required',
        ];

        return $rules;
    }

    public function storeDecision()
    {
        $this->validate();
        DB::beginTransaction();
        try {

            ReviewDecision::updateOrCreate(
                [
                    'review_id'=> $this->review_id,
                ],
                [
                    'review_id'=> $this->review_id,
                    'comments_fa' => $this->comments_fa,
                    'comments_en' => $this->comments_en,
                    'recommendation' => $this->recommendation,
                ],
            );

            $review = Review::findOrFail($this->review_id);
            $review->update([
                'status'=>'completed',
            ]);
            DB::commit();
            $decision = ReviewDecision::where('review_id',$this->review_id)->first();
            $this->comments_fa = $decision?->comments_fa;
            $this->comments_en = $decision?->comments_en;
            $this->recommendation = $decision?->recommendation;
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));

        } catch (\Exception $e) {
             DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.store_error').' : ' . $e->getMessage());
        }
    }

    public function uploadFile()
    {
    
       $this->validate([
            'file'   => 'required|array',
            'file.*' => 'file|max:10240|mimes:pdf,doc,docx',
        ], [
            'file.required' => __('label.file_required'),
            'file.*.file' => __('label.file_invalid'),
            'file.*.max' => __('label.file_max'),
            'file.*.mimes' => __('label.file_mimes'),
        ]);

        try {
            foreach ($this->file as $f) {
                $originalName = $f->getClientOriginalName();
                $mime = $f->getClientMimeType();
                $size = $f->getSize();
                $storedName = Str::random(40) . '.' . $f->getClientOriginalExtension();
                
                $path = $f->storeAs('reviewer-files/' . $this->review_id, $storedName, 'local');
                ReviewFile::create([
                    'review_id' => $this->review_id,
                    'original_name' => $originalName,
                    'file_path' => $path,
                    'size' => $size,
                    'mime' => $mime,
                    'type' => 'reviewed',
                ]);

                 $f->delete();
            }

            $this->file = [];
            $this->loadUploadedFiles();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.store_error') . $e->getMessage());
        }
    }

    public function deleteFile($id)
    {
        $file = ReviewFile::findOrFail($id);

        if (Storage::disk('local')->exists($file->file_path)) {
            Storage::disk('local')->delete($file->file_path);
        }

        $file->delete();

        $this->loadUploadedFiles();
        $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
    }

    public function downloadFile($file_id)
    {
        $file = ReviewFile::findOrFail($file_id);

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



    public function handleGlobalDelete($payload)
    {

        if (!isset($payload['table']) || $payload['table'] !== $this->table_name) {
            return;
        }

        $this->delete($payload['id']);
    }

    public function delete($id)
    {
         DB::beginTransaction();
        try {

            $review = Review::findOrFail($this->review_id);
            $review->update([
                'status'=>'pending',
            ]);

            ReviewDecision::findOrFail($id)->delete();

            $files = ReviewFile::where('type','reviewed')->where('review_id', $this->review_id)->get();

            foreach ($files as $file) {
                if (Storage::disk('local')->exists($file->file_path)) {
                    Storage::disk('local')->delete($file->file_path);
                }
                $file->delete();
            }

            DB::commit();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
            $this->dispatch('refreshReviewerAssignmentViewComponent');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }


    public function render()
    {
       
        return view('livewire.assignments.reviewer.reviewer-assignment-review');
    }
}
