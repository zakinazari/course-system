<?php

namespace App\Livewire\Submissions;

use Livewire\Component;
use App\Models\Submissions\Submission;
use App\Models\Submissions\EditorialDecision;
use DB;
class EditorDecision extends Component
{
    public $modalId = 'editor-decision-addEditModal';
    public $table_name='editorial_decisions';
    public $editMode = false;
    public $submission_id;
    public $submission;
    public $comments_fa;
    public $comments_en;
    public $round;
    public $decision_id;
    protected $listeners = ['modalClosed' => 'closeModal','globalDelete' => 'handleGlobalDelete','refreshEditorDecisionComponent'=>'refreshMe'];
    public function refreshMe()
    {
        $this->dispatch('$refresh'); 
    }
    public function closeModal(){
        $this->resetInputFields();
        // $this->resetValidation();
        $this->dispatch('close-modal', id: $this->modalId);

    }
    public function openModal(){
        $this->resetInputFields();
        // $this->resetValidation();
        $this->dispatch('open-modal', id: $this->modalId);
    }
     // Hook for real time error message
    public function updated($propertyName)
    {
        if (array_key_exists($propertyName, $this->rules())) {
            $this->validateOnly($propertyName);
        }
    }
    protected function rules()
    {
        return [
            'submission_id' => 'required',
        ];
    }

     public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'submission_id',
            'submission',
            'round',
        ]);
    }
   
    public function editDecision($id)
    {
        
        $decision = EditorialDecision::findOrFail($id);
        $this->comments_fa = $decision->comments_fa;
        $this->comments_en = $decision->comments_en;
        $this->decision_id = $id;
        $this->dispatch('open-modal', id: $this->modalId);
        $this->editMode = true;
    }

    public function updateDecision()
    {
        try {

            $decision = EditorialDecision::findOrFail($this->decision_id);
            $decision->comments_fa = $this->comments_fa;
            $decision->comments_en = $this->comments_en;
            $decision->save();

            $decision->comments_fa = $this->comments_fa;
            $decision->comments_en = $this->comments_en;
            $this->closeModal();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_updated'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.update_error').' : ' . $e->getMessage());
        }
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

            $submission = Submission::findOrFail($this->submission_id);
            $submission->update([
                'status'=>'screening',
            ]);

            EditorialDecision::findOrFail($id)->delete();
            DB::commit();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
            $this->dispatch('refreshSubmissionViewComponent');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }

    public function mount($submission_id = null,$round)
    {
        $this->submission_id = $submission_id;
        $this->round = $round;
    }

    public function updateRound($round)
    {
        $this->round = $round;
    }

    public function render()
    {
        $editor_decision = EditorialDecision::with('editor:id,name')
        ->where('submission_id',$this->submission_id)
        ->where('round',$this->round)->get();
        $this->submission = Submission::findOrFail($this->submission_id);
        return view('livewire.submissions.editor-decision',compact('editor_decision'));
    }
}
