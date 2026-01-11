<?php

namespace App\Livewire\Submissions;

use Livewire\Component;
use App\Models\Submissions\Submission;
class SubmissionComment extends Component
{
    public $comments_to_editor_fa;
    public $comments_to_editor_en;
    public $submission_id;
    public $active_menu_id;
    public $current_url;
    protected $listeners = ['validate-step' => 'validateStep'];
    
    public function mount($menu_id = null,$submission_id = null,$current_url=null)
    {
        // dd($this->active_menu_id);
        // -------------start for activing menu in sidebar ----------------------
        // $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $menu_id;
        // $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------
        $this->submission_id = $submission_id;
        $this->current_url = $current_url;
        $submission = Submission::findOrFail($submission_id);
        $this->comments_to_editor_fa = $submission->comments_to_editor_fa;
        $this->comments_to_editor_en = $submission->comments_to_editor_en;

    }

    public function storeComment()
    {
        
        try {
            $submission = Submission::findOrFail($this->submission_id);
            $submission->update([
                'comments_to_editor_fa' => $this->comments_to_editor_fa,
                'comments_to_editor_en' => $this->comments_to_editor_en,
            ]);
            
            $this->comments_to_editor_fa = $submission->comments_to_editor_fa;
            $this->comments_to_editor_en = $submission->comments_to_editor_en;
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));

            return redirect($this->current_url);

        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.store_error').' : ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.submissions.submission-comment');
    }
    public function validateStep($step)
    {
        if ($step != 4) return;
        
        $this->dispatch('stepValidated', is_valid: true);
    }
}
