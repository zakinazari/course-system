<?php

namespace App\Livewire\Submissions;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Auth;
use DB;
use App\Models\User;
use App\Models\Submissions\Review;
use App\Models\Submissions\ReviewFile;
use App\Models\Submissions\Submission;
use Illuminate\Validation\Rule;
use App\Models\Settings\SystemLog;
use Storage;
class SubmissionReview extends Component
{
    use WithFileUploads;
    public $editMode = false;
    public $modalId = 'submission-review-addEditModal';
    public $table_name='reviews';
    public $submission_id;
    public $submission;
    public $users=[];
    public $reviewers=[];
    public $reviewer_id;
    public $review_id;
    public $round;
    public $editor_message_fa;
    public $editor_message_en;
    public $files = [];
    public $existing_files;
    protected $listeners = ['modalClosed' => 'closeModal','globalDelete' => 'handleGlobalDelete','refreshReviewComponent'=>'refreshMe'];
    public function refreshMe()
    {
        $this->dispatch('$refresh'); 
        $this->loadReviewers();
    }
    public function closeModal(){
        $this->resetInputFields();
        $this->resetValidation();
        $this->dispatch('close-modal', id: $this->modalId);

    }
    public function openModal(){
        $this->resetInputFields();
        $this->resetValidation();
        $this->dispatch('open-modal', id: $this->modalId);
    }
    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'modalId',
            'table_name',
            'search',
            'submission_id',
            'submission',
            'reviewers',
            'users',
            'round',
        ]);
    }

    public function updated($propertyName)
    {
        if (array_key_exists($propertyName, $this->rules())) {
            $this->validateOnly($propertyName);
        }
    }

    public function mount($active_menu_id = null,$submission_id = null,$round)
    {
       
        $this->submission_id = $submission_id;
        $this->users = User::all();
        $this->submission = Submission::findOrFail($this->submission_id);
        $this->round = $round;
        $this->loadReviewers();
    }

    protected function rules()
    {
        return [
            'files' => 'required|array',
            'files.*' => 'file|max:5120|mimes:pdf,doc,docx',
        ];
    }

    protected function messages()
    {
        return [

            'files.required' => __('label.file_required'),
            'files.*.files' => __('label.file_invalid'),
            'files.*.max' => __('label.file_max',['value'=>5]),
            'files.*.mimes' => __('label.file_mimes',['value'=>'pdf,doc,docx']),
        ];
    }

    public function edit($id)
    {
        $this->resetValidation();
        $review = Review::findOrFail($id);
        $this->review_id = $id;
        $this->editor_message_fa = $review->editor_message_fa;
        $this->editor_message_en = $review->editor_message_en;
        $this->editMode = true;
        $this->dispatch('open-modal', id: $this->modalId);

    }

    public function update()
    {
        $this->validate();
        DB::beginTransaction();
        try {
            $review = Review::findOrFail($this->review_id);
            $review->update([
                'editor_message_fa'=>$this->editor_message_fa,
                'editor_message_en'=>$this->editor_message_en,
            ]);

            foreach ($this->files as $f) {
                $originalName = $f->getClientOriginalName();
                $mime = $f->getClientMimeType();
                $size = $f->getSize();
                $storedName = Str::random(40) . '.' . $f->getClientOriginalExtension();
                
                $path = $f->storeAs('files-for-review/' . $review->id, $storedName, 'local');
                ReviewFile::create([
                    'review_id' => $review->id,
                    'original_name' => $originalName,
                    'file_path' => $path,
                    'type' => 'for_review',
                    'size' => $size,
                    'mime' => $mime,
                ]);

                $f->delete();
            }

            $this->files = [];

            DB::commit();
            $this->closeModal($this->modalId);
            
            $this->dispatch('alert', type: 'success', message: __('label.successfully_updated'));

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.update_error').' : ' . $e->getMessage());
        }
    }



    public function loadReviewers()
    {
        $this->reviewers = Review::with('reviewer')->where('submission_id', $this->submission_id)
                                ->where('round',$this->round)
                                ->latest()
                                ->get();
    }

    public function handleGlobalDelete($payload)
    {

        if (!isset($payload['table']) || ($payload['table'] != $this->table_name && $payload['table'] != 'review_files')) {
            return;
        }

        if($payload['table']==='review_files'){
            $this->deleteFile($payload['id']);
        }else{
            $this->delete($payload['id']);
        }
    }

    public function delete($id)
    {
        try {
            $review = Review::findOrFail($id);

            if (!$review) {
                $this->dispatch('alert', type: 'error', message: __('label.not_found'));
                return;
            }

            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => 'این داور توسط این کاربر حذف شده است. (ID: '.$id.')' 
                 .'reviewer_id:('.$review->reviewer_id.')' 
                 .'submission_id:('.$review->submission_id.')',
                'type_id' => 4,
            ]);

            $review->delete();

            $has_review = Review::where('submission_id',$this->submission_id)->exists();
            if(!$has_review){
                $submission = Submission::findOrFail($this->submission_id);
                $submission->update([
                    'status'=>'screening',
                ]);
            }
        
            $this->loadReviewers();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
            $this->dispatch('refreshSubmissionViewComponent');
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.submissions.submission-review');
    }

    public function showFiles($id)
    {
       
        $this->existing_files =  ReviewFile::where('type','for_review')->where('review_id',$id)->get();

        $this->dispatch('open-modal', id: 'modalShowFiles');
    }

    public function deleteFile($id)
    {
    
        $file = ReviewFile::findOrFail($id);
        $review_id = $file->review_id;
        if (Storage::disk('local')->exists($file->file_path)) {
            Storage::disk('local')->delete($file->file_path);
        }

        SystemLog::create([
            'user_id' => Auth::user()->id,
            'section' => 'این فایل مقاله برای داوری توسط این کاربر حذف شده است. (ID: '.$id.'نام فایل :'.$file->original_name.')',
            'type_id' => 4,
        ]);

        $file->delete();
        $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));

        $this->showFiles($review_id);
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
}
