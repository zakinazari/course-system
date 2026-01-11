<?php

namespace App\Livewire\Website;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\Website\WebPage;
use App\Models\Website\WebPageFile;
use Storage;
use Auth;
use Carbon\Carbon;
class WebPageList extends Component
{
     // -------start generals--------------------
    use WithPagination;
    use WithFileUploads;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'web-page-list-addEditModal';
    public $table_name='web_pages';

    public $files = [];
    public $existing_files;
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

    public $web_page_id,$name_fa,$name_en,$title_fa,$title_en,$content_fa,$content_en,$cover_image,$published_at,$existing_cover_image,$page,$show_contet_fa,$show_content_en;

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
        $web_pages = WebPage::query()
            ->when(!empty($this->search['identity']), function ($query) {
                $searchTerm = $this->search['identity'];
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name_fa', 'like', "%{$searchTerm}%")
                    ->orWhere('name_en', 'like', "%{$searchTerm}%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.website.web-page-list',compact('web_pages'));
    }

    protected function rules()
    {
        return [
            'name_fa' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'title_fa' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
            'files.*' => 'required|file|mimes:pdf,mp4,mov,avi,webm|max:20480',
        ];
    }
    
    protected function messages()
    {
        return [
            'name_fa.required' => __('label.name.required'),
            'name_en.required' => __('label.name.required'),
            'title_fa.required' => __('label.title.required'),
            'title_en.required'   => __('label.title.required'),

            'file.required' => __('label.file_required'),
            'file.*.file' => __('label.file_invalid'),
            'file.*.max' => __('label.file_max',['value'=>20]),
            'file.*.mimes' => __('label.file_mimes',['value'=>'pdf,mp4,mov,avi,webm']),
            
        ];
    }

    public function store()
    {
        if (add(Auth::user()->role_ids, $this->active_menu_id)) {

            $this->validate();

            try {
                $web_pages = WebPage::create([
                    'name_fa' => $this->name_fa,
                    'name_en' => $this->name_en,
                    'title_fa' => $this->title_fa,
                    'title_en' => $this->title_en,
                    'content_en' => $this->content_en,
                    'content_fa' => $this->content_fa,
                ]);

                if ($this->cover_image) {

                    $file = $this->cover_image;

                    $storedName = Str::random(40) . '.' . $file->getClientOriginalExtension();

                    $path = $file->storeAs(
                        'website/pages/' . $web_pages->id,
                        $storedName,
                        'public' 
                    );

                    $web_pages->cover_image = $path;
                    $web_pages->save();
                }

                if ($this->files) {

                    foreach ($this->files as $file) {
    
                        $storedName = Str::random(40) . '.' . $file->getClientOriginalExtension();

                        $path = $file->storeAs(
                            'website/pages/files' . $web_pages->id,
                            $storedName,
                            'local'
                        );

                        if (!Storage::disk('local')->exists($path)) {
                            throw new \Exception("File $storedName could not be stored!");
                        }

                        $file_size = Storage::disk('local')->size($path);

                        WebPageFile::create([
                            'page_id' => $web_pages->id,
                            'file_path'  => $path,
                            'file_name'  => $file->getClientOriginalName(),
                            'file_type'  => $file->getClientOriginalExtension(),
                            'file_size'  => $file_size,
                        ]);
                    }

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
       
        $page = WebPage::findOrFail($id);
        $this->show_contet_fa = $page->content_fa;
        $this->show_contet_en = $page->content_en;
        $this->dispatch('open-modal', id: 'modalShowContent');
    }

    public function showFiles($id)
    {
       
        $this->existing_files =  WebPageFile::where('page_id',$id)->get();

        $this->dispatch('open-modal', id: 'modalShowFiles');
    }

    public function deleteFile($id)
    {
    
        $file = WebPageFile::findOrFail($id);
        $page_id = $file->page_id;
        if (Storage::disk('local')->exists($file->file_path)) {
            Storage::disk('local')->delete($file->file_path);
        }

        $file->delete();
        SystemLog::create([
            'user_id' => Auth::user()->id,
            'section' => 'این صفحه توسط این کاربر حذف شده است. (ID: '.$id.')',
            'type_id' => 4,
        ]);
        $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));

        $this->showFiles($page_id);
    }

    public function downloadFile($file_id)
    {
        $file = WebPageFile::findOrFail($file_id);

        $path = $file->file_path;

        if (!Storage::disk('local')->exists($path)) {
            $this->dispatch('alert', 
                type: 'error',
                message: 'File not found!'
            );
            return;
        }

        return Storage::disk('local')->download($path, $file->file_name);
    }

    public function edit($id)
    {
        $this->resetValidation();
        $page = WebPage::findOrFail($id);

        $this->web_page_id = $id;
        $this->name_fa = $page->name_fa;
        $this->name_en = $page->name_en;
        $this->title_fa = $page->title_fa;
        $this->title_en = $page->title_en;
        $this->content_fa = $page->content_fa;
        $this->content_en = $page->content_en;
        $this->existing_cover_image = $page->cover_image;
        $this->editMode = true;

        $this->dispatch('open-modal', id: $this->modalId);
         $this->dispatch('loadEditors'); 
    }

   
    public function update()
    {
        if(edit(Auth::user()->role_ids,$this->active_menu_id)){
            $this->validate();

            try {

                $page = WebPage::findOrFail($this->web_page_id);
                $page->update([
                    'name_fa' => $this->name_fa,
                    'name_en' => $this->name_en,
                    'title_fa' => $this->title_fa,
                    'title_en' => $this->title_en,
                    'content_en' => $this->content_en,
                    'content_fa' => $this->content_fa,
                ]);

                if ($this->cover_image) {

        
                    if ($page->cover_image && \Storage::disk('public')->exists($page->cover_image)) {
                        \Storage::disk('public')->delete($page->cover_image);
                    }

                    $file = $this->cover_image;
                    $storedName = Str::random(40) . '.' . $file->getClientOriginalExtension();

                    $path = $file->storeAs(
                        'website/pages/' . $page->id,
                        $storedName,
                        'public' 
                    );

                    $page->cover_image = $path;
                    $page->save();
                }

                if ($this->files) {
                    foreach ($this->files as $file) {

                        $fileName = $file->getClientOriginalName();

                        $existingFile = WebPageFile::where('page_id', $page->id)
                                                ->where('file_name', $fileName)
                                                ->first();

                        $storedName = Str::random(40) . '.' . $file->getClientOriginalExtension();
                        $path = $file->storeAs(
                            'website/pages/files' . $page->id,
                            $storedName,
                            'local'
                        );

                        if (!Storage::disk('local')->exists($path)) {
                            throw new \Exception("File $storedName could not be stored!");
                        }

                        $fileData = [
                            'page_id' => $page->id,
                            'file_path'  => $path,
                            'file_name'  => $fileName,
                            'file_type'  => $file->getClientOriginalExtension(),
                            'file_size'  => Storage::disk('local')->size($path),
                        ];

                        if ($existingFile) {
                            if (Storage::disk('local')->exists($existingFile->file_path)) {
                                Storage::disk('local')->delete($existingFile->file_path);
                            }
                            $existingFile->update($fileData);
                        } else {
                            WebPageFile::create($fileData);
                        }
                    }
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

        if (!isset($payload['table']) || ($payload['table'] != $this->table_name && $payload['table'] != 'web_page_files')) {
            return;
        }

        if($payload['table']==='web_page_files'){
            $this->deleteFile($payload['id']);
        }else{
            $this->delete($payload['id']);
        }
    }

    public function delete($id)
    {
        if(delete(Auth::user()->role_ids,$this->active_menu_id)){
            try {
                $page = WebPage::with('files')->findOrFail($id);
                if ($page->cover_image && \Storage::disk('public')->exists($page->cover_image)) {
                    \Storage::disk('public')->delete($page->cover_image);
                }

                foreach($page->files as $file){
                    if(\Storage::disk('local')->exists($file->file_path)){
                        \Storage::disk('local')->delete($file->file_path);
                    }
                    $file->delete();
                }

                $page->delete();
                $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
            } catch (\Exception $e) {
                $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
            }
        }else{
            $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }
    }
}
