<?php

namespace App\Livewire\Submissions;

use Livewire\Component;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\Submissions\Submission;
use Livewire\WithPagination;
use Auth;
use Illuminate\Support\Facades\Storage;
use DB;
class SubmissionList extends Component
{
   
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $submission_id;
    public $modalId = 'issue-list-addEditModal';
    public $table_name='issues';
    protected $listeners = ['modalClosed' => 'closeModal','globalDelete' => 'handleGlobalDelete'];
  
    public function closeModal(){
        $this->resetInputFields();
        $this->resetValidation();
        $this->dispatch('close-modal');
    }

    public function openModal(){
        $this->resetInputFields();
        $this->resetValidation();
    }
    // Hook for real time error message
    // public function updated($propertyName)
    // {
    //     if (array_key_exists($propertyName, $this->rules())) {
    //         $this->validateOnly($propertyName);
    //     }
    // }

    public function updatingPerPage()
    {
        $this->resetPage();
    }
    public function applySearch()
    {
        $this->resetPage();
    }
    
    // ---------------------------------end generals-------------

    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------
       
    }

    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'search',
            'modalId',
            'table_name',
            'submissions',
        ]);
    }

    public $search = [
            'identity' => null,
            'status' => null,
        ];

    public function render()
    {
        $query = Submission::query()
            ->when(!empty($this->search['identity']), function ($q) {
                $searchTerm = $this->search['identity'];
                $q->where(function ($q) use ($searchTerm) {
                    $q->where('title_fa', 'like', "%{$searchTerm}%")
                    ->orWhere('title_en', 'like', "%{$searchTerm}%");
                });
            })
            ->when(!empty($this->search['status']), function ($q) {
                 $status = $this->search['status'];
                 $q->where(function ($q) use ($status) {
                    $q->where('status', $status);
                });
            });

            if (!auth()->user()->roles->contains('role_name', 'Admin')) {

                $query->where('submitter_id', auth()->id());
            }

        $submissions = $query->orderBy('id', 'desc')
                            ->paginate($this->perPage);

        return view('livewire.submissions.submission-list', compact('submissions'));
    }

    public function viewSubmission($submission_id)
    {
        return redirect()->route('submission-view-page', [
            'menu_id' => $this->active_menu_id,
            'submission_id' => $submission_id,
        ]);
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
        if (delete(Auth::user()->role_ids, $this->active_menu_id)) {
            try {
                DB::transaction(function () use ($id) {

                    $submission = Submission::with('files')->findOrFail($id);

                    foreach ($submission->files as $file) {

                        if ($file->file_path && Storage::disk('local')->exists($file->file_path)) {
                            Storage::disk('local')->delete($file->file_path);
                        }

                        $file->delete();
                    }
                    $submission->delete();

                    SystemLog::create([
                        'user_id' => Auth::user()->id,
                        'section' => 'این مقاله توسط این کاربر حذف شده است. (ID: '.$id.')',
                        'type_id' => 4,
                    ]);
                });

                $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));

            } catch (\Exception $e) {
                $this->dispatch('alert', type: 'error', message: __('label.delete_error') . ' : ' . $e->getMessage());
            }
        } else {
            $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }
    }

    public function publish(Submission $submission)
    {
        if ($submission->status !== 'accepted') {
            return back()->with('error', 'Submission is not ready for publishing.');
        }

        $submission->status = 'published';
        $submission->published_at = now(); 
        $submission->save();

        return back()->with('success', 'Submission published successfully.');
    }

    public function unpublish(Submission $submission)
    {
        if ($submission->status !== 'published') {
            return back()->with('error', 'Submission is not currently published.');
        }

        $submission->status = 'accepted';
        $submission->published_at = null;
        $submission->save();

        return back()->with('success', 'Submission unpublished successfully.');
    }
}
