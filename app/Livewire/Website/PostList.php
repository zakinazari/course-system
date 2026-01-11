<?php

namespace App\Livewire\Website;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\Website\Post;

use Storage;
use Auth;
use Carbon\Carbon;
class PostList extends Component
{
    
    // -------start generals--------------------
    use WithPagination;
    use WithFileUploads;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'post-list-addEditModal';
    public $table_name='posts';
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
         $this->dispatch('loadEditors'); 
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

    public $post_id,$title_fa,$title_en,$content_fa,$content_en,$image,$published_at,$existing_image,$show_contet_fa,$show_content_en,$status='draft';

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
        $posts = Post::query()
            ->when(!empty($this->search['identity']), function ($query) {
                $searchTerm = $this->search['identity'];
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('title_fa', 'like', "%{$searchTerm}%")
                    ->orWhere('title_en', 'like', "%{$searchTerm}%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.website.post-list',compact('posts'));
    }

    protected function rules()
    {
        return [
            'title_fa' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
    
    protected function messages()
    {
        return [
            'title_fa.required' => __('label.title.required'),
        ];
    }

    public function store()
    {
        if (add(Auth::user()->role_ids, $this->active_menu_id)) {

            $this->validate();

            try {
                $post = Post::create([
                    'title_fa' => $this->title_fa,
                    'title_en' => $this->title_en,
                    'content_en' => $this->content_en,
                    'content_fa' => $this->content_fa,
                    'status' => $this->status,
                    'user_id' => Auth::id(),
                    'published_at' => $this->published_at
                    ? Carbon::parse($this->published_at)->setTimeFromTimeString(now()->toTimeString())
                    : null,
                ]);

                if ($this->image) {

                    $file = $this->image;

                    $storedName = Str::random(40) . '.' . $file->getClientOriginalExtension();

                    $path = $file->storeAs(
                        'website/posts/' . $post->id,
                        $storedName,
                        'public' 
                    );

                    $post->image = $path;
                    $post->save();
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

    public function showContent($id)
    {
       
        $post = Post::findOrFail($id);
        $this->show_contet_fa = $post->content_fa;
        $this->show_contet_en = $post->content_en;
        $this->dispatch('open-modal', id: 'modalShowContent');
    }


    public function edit($id)
    {
        $this->resetValidation();
        $post = Post::findOrFail($id);

        $this->post_id = $id;
        $this->title_fa = $post->title_fa;
        $this->title_en = $post->title_en;
        $this->content_fa = $post->content_fa;
        $this->content_en = $post->content_en;
        $this->status = $post->status;
        $this->published_at = Carbon::parse($post->published_at)->format('Y-m-d');
        $this->existing_image = $post->image;
        $this->editMode = true;

        $this->dispatch('open-modal', id: $this->modalId);
         $this->dispatch('loadEditors'); 
    }

   
    public function update()
    {
        if(edit(Auth::user()->role_ids,$this->active_menu_id)){
            $this->validate();
            try {
                $post = Post::findOrFail($this->post_id);
                $post->update([
                    'title_fa' => $this->title_fa,
                    'title_en' => $this->title_en,
                    'content_en' => $this->content_en,
                    'content_fa' => $this->content_fa,
                    'status' => $this->status,
                    'auth' =>Auth::id(),
                    'published_at' => $this->published_at
                    ? Carbon::parse($this->published_at)->setTimeFromTimeString(now()->toTimeString())
                    : null,
                ]);

                if ($this->image) {

        
                    if ($post->image && \Storage::disk('public')->exists($post->image)) {
                        \Storage::disk('public')->delete($post->image);
                    }

                    $file = $this->image;
                    $storedName = Str::random(40) . '.' . $file->getClientOriginalExtension();

                    $path = $file->storeAs(
                        'website/posts/' . $post->id,
                        $storedName,
                        'public' 
                    );

                    $post->image = $path;
                    $post->save();
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
                $post = Post::findOrFail($id);
                if ($post->image && \Storage::disk('public')->exists($post->image)) {
                    \Storage::disk('public')->delete($post->image);
                }

                $post->delete();
                SystemLog::create([
                    'user_id' => Auth::user()->id,
                    'section' => 'این اطلاعیه توسط این کاربر حذف شده است. (ID: '.$id.')',
                    'type_id' => 4,
                ]);
                $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
            } catch (\Exception $e) {
                $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
            }
        }else{
            $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }
    }
}
