<?php

namespace App\Livewire\Issues;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Models\Settings\Menu;
use App\Models\Issues\Issue;
use Storage;
use Auth;
use Carbon\Carbon;
class IssueList extends Component
{

      // -------start generals--------------------
    use WithPagination;
    use WithFileUploads;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'issue-list-addEditModal';
    public $table_name='issues';
    protected $listeners = ['modalClosed' => 'closeModal','globalDelete' => 'handleGlobalDelete'];

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
     // Hook for real time error message
    public function updated($propertyName)
    {
        if (array_key_exists($propertyName, $this->rules())) {
            $this->validateOnly($propertyName);
        }
    }

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


    public $issue_id,$volume,$number,$title_fa,$title_en,$cover_image,$published_at,$existing_cover_image,$status='unpublished';

     public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
        ]);
    }
    public $search = [
            'identity' => null,
        ];

    public function render()
    {
        $issues = Issue::query()
            ->when(!empty($this->search['identity']), function ($query) {
                $searchTerm = $this->search['identity'];
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('volume', 'like', "%{$searchTerm}%")
                    ->orWhere('number', 'like', "%{$searchTerm}%")
                    ->orWhere('title_fa', 'like', "%{$searchTerm}%")
                    ->orWhere('title_en', 'like', "%{$searchTerm}%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);
        return view('livewire.issues.issue-list',compact('issues'));
    }

  
    protected function rules()
    {
        return [
            'volume' => 'required|string|max:20',
            'number' => 'required|string|max:10',
            'title_fa' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
    
    protected function messages()
    {
        return [
            'volume.required' => __('label.volume.required'),
            'number.required'   => __('label.number.required'),
        ];
    }

    public function store()
    {
        if (add(Auth::user()->role_ids, $this->active_menu_id)) {

            $this->validate();

            try {
                $issue = Issue::create([
                    'volume' => $this->volume,
                    'number' => $this->number,
                    'title_fa' => $this->title_fa,
                    'title_en' => $this->title_en,
                    'status' => $this->status,
                    'published_at' => $this->published_at ? Carbon::parse($this->published_at) : null,
                ]);

                if ($this->cover_image) {

                    $file = $this->cover_image;

                    $storedName = Str::random(40) . '.' . $file->getClientOriginalExtension();

                    $path = $file->storeAs(
                        'issues/' . $issue->id,
                        $storedName,
                        'public' 
                    );

                    $issue->cover_image = $path;
                    $issue->save();
                }

                $this->closeModal();
                $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));

            } catch (\Exception $e) {

                $this->dispatch('alert', type: 'error', message: __('label.store_error').' : '. $e->getMessage());
            }

        } else {
            $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }
    }

    public function edit($id)
    {
        $this->resetValidation(); 
        $this->issue_id = $id;    
        $issue = Issue::find($id);
        $this->volume = $issue->volume;
        $this->number = $issue->number;
        $this->title_en = $issue->title_en;
        $this->title_fa = $issue->title_fa;
        $this->status = $issue->status;
        $this->published_at = $issue->published_at;
        $this->existing_cover_image = $issue->cover_image;
        $this->editMode = true;
        $this->dispatch('open-modal', id: $this->modalId);
    }
   
    public function update()
    {
        if(edit(Auth::user()->role_ids,$this->active_menu_id)){
            $this->validate();

            try {

                $issue = Issue::findOrFail($this->issue_id);
                $issue->update([
                    'volume' => $this->volume,
                    'number' => $this->number,
                    'title_fa' => $this->title_fa,
                    'title_en' => $this->title_en,
                    'status' => $this->status,
                    'published_at' => $this->published_at ? Carbon::parse($this->published_at) : null,
                ]);

                if ($this->cover_image) {

        
                    if ($issue->cover_image && \Storage::disk('public')->exists($issue->cover_image)) {
                        \Storage::disk('public')->delete($issue->cover_image);
                    }

                    $file = $this->cover_image;
                    $storedName = Str::random(40) . '.' . $file->getClientOriginalExtension();

                    $path = $file->storeAs(
                        'issues/' . $issue->id,
                        $storedName,
                        'public' 
                    );

                    $issue->cover_image = $path;
                    $issue->save();
                }
                $this->closeModal();
                $this->dispatch('alert', type: 'success', message: __('label.successfully_updated'));
            } catch (\Exception $e) {
            
                $this->dispatch('alert', type: 'error', message: __('label.update_error').' : '. $e->getMessage());
            }
        }else{
            $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
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
        if(delete(Auth::user()->role_ids,$this->active_menu_id)){
            try {
                $issue = Issue::findOrFail($id);
                if ($issue->cover_image && \Storage::disk('public')->exists($issue->cover_image)) {
                    \Storage::disk('public')->delete($issue->cover_image);
                }

                $issue->delete();
                $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
            } catch (\Exception $e) {
                $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
            }
        }else{
            $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }
    }

}
