<?php

namespace App\Livewire\Website\Gazettes;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\Website\Gazette;
use App\Models\Website\GazetteFile;
use Storage;
use Auth;
use Carbon\Carbon;
use DB;
class GazetteList extends Component
{
    // -------start generals--------------------
    use WithPagination;
    use WithFileUploads;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'gazette-list-addEditModal';
    public $table_name='gazettes';
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

     
    public $gazette_id;
    public $title_fa;
    public $title_en;
    public $gazette_number;
    public $publish_date;
    public $status = true;

    public $files = [];
    public $existing_files;

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
         $gazettes = Gazette::query()
            ->when(!empty($this->search['identity']), function ($query) {
                $searchTerm = $this->search['identity'];
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('title_fa', 'like', "%{$searchTerm}%")
                    ->orWhere('title_en', 'like', "%{$searchTerm}%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.website.gazettes.gazette-list',compact('gazettes'));
    }

    protected function rules()
    {
        return [
            'title_fa' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'gazette_number' => [
                'required',
                \Illuminate\Validation\Rule::unique('gazettes', 'gazette_number')
                    ->ignore($this->gazette_id ?? null),
            ],
            'publish_date' => [
                'required',
                'string',
                'size:10',
                'regex:/^\d{4}\/\d{2}\/\d{2}$/',
            ],
            'status' => 'boolean',

            'files'   => 'nullable|array',
            'files.*' => 'file|mimes:pdf|max:2048',
        ];
    }
    
    protected function messages()
    {
        return [
            'title_fa.required' => __('label.title.required'),

            'gazette_number.required' => __('label.gazette_number.required'),
            'gazette_number.unique' => __('label.gazette_number.unique'),

            'publish_date.required' => __('label.gazette_date.required'),
            'publish_date.size'     => __('label.gazette_date.size'),
            'publish_date.regex'    => __('label.gazette_date.regex'),

            'files.required' => __('label.file_required'),
            'files.*.file' => __('label.file_invalid'),
            'files.*.max' => __('label.file_max',['value'=>2]),
            'files.*.mimes' => __('label.file_mimes',['value'=>'pdf']),
        ];
    }


    public function store()
    {
        
        if (add(Auth::user()->role_ids, $this->active_menu_id)) {
            
            $this->validate();
            DB::beginTransaction();
            try {
                $gazette = Gazette::create([
                    'title_fa' => $this->title_fa,
                    'title_en' => $this->title_en,
                    'gazette_number' => $this->gazette_number,
                    'publish_date' => $this->publish_date,
                    'status' => $this->status,
                ]);

                if ($this->files) {
                    foreach ($this->files as $file) {
    
                        $storedName = Str::random(40) . '.' . $file->getClientOriginalExtension();

                        $path = $file->storeAs(
                            'website/gazette-files/' . $gazette->id,
                            $storedName,
                            'local'
                        );

                        if (!Storage::disk('local')->exists($path)) {
                            throw new \Exception("File $storedName could not be stored!");
                        }

                        $file_size = Storage::disk('local')->size($path);

                        GazetteFile::create([
                            'gazette_id' => $gazette->id,
                            'file_path'  => $path,
                            'file_name'  => $file->getClientOriginalName(),
                            'file_type'  => $file->getClientOriginalExtension(),
                            'file_size'  => $file_size,
                        ]);
                    }

                }
                DB::commit();
                $this->closeModal();
                $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));

            } catch (\Exception $e) {
                 DB::rollBack();
                $this->dispatch('alert', type: 'error', message: __('label.store_error').' : '. $e->getMessage());
            }

        } else {
            $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }
    }

    public function showFiles($id)
    {
       
        $this->existing_files =  GazetteFile::where('gazette_id',$id)->get();

        $this->dispatch('open-modal', id: 'modalShowFiles');
    }

    public function edit($id)
    {
        $this->resetValidation();
        $gazette = Gazette::findOrFail($id);

        $this->gazette_id = $id;
        $this->title_fa = $gazette->title_fa;
        $this->title_en = $gazette->title_en;
        $this->gazette_number = $gazette->gazette_number;
        $this->publish_date = $gazette->publish_date;
        $this->status = $gazette->status;
        $this->editMode = true;

        $this->dispatch('open-modal', id: $this->modalId);
         $this->dispatch('loadEditors'); 
    }

   
    public function update()
    {
        if(!edit(Auth::user()->role_ids, $this->active_menu_id)){
            $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
            return;
        }

        $this->validate();

        DB::beginTransaction();
        try {
            $gazette = Gazette::findOrFail($this->gazette_id);

            $gazette->update([
                'title_fa' => $this->title_fa,
                'title_en' => $this->title_en,
                'gazette_number' => $this->gazette_number,
                'publish_date' => $this->publish_date,
                'status' => $this->status,
            ]);

            if ($this->files) {
                foreach ($this->files as $file) {

                    $fileName = $file->getClientOriginalName();

                    $existingFile = GazetteFile::where('gazette_id', $gazette->id)
                                            ->where('file_name', $fileName)
                                            ->first();

                    $storedName = Str::random(40) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs(
                        'website/gazette-files/' . $gazette->id,
                        $storedName,
                        'local'
                    );

                    if (!Storage::disk('local')->exists($path)) {
                        throw new \Exception("File $storedName could not be stored!");
                    }

                    $fileData = [
                        'gazette_id' => $gazette->id,
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
                        GazetteFile::create($fileData);
                    }
                }
            }

            DB::commit();
            $this->closeModal();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_updated'));

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.update_error').' : '. $e->getMessage());
        }
    }



    
    public function handleGlobalDelete($payload)
    {

        if (!isset($payload['table']) || ($payload['table'] != $this->table_name && $payload['table'] != 'gazette_files')) {
            return;
        }

        if($payload['table']==='gazette_files'){
            $this->deleteFile($payload['id']);
        }else{
            $this->delete($payload['id']);
        }
    }

   public function delete($id)
    {
        if(delete(Auth::user()->role_ids, $this->active_menu_id)){
            try {
                $gazette = Gazette::with('files')->findOrFail($id);

                foreach($gazette->files as $file){
                    if(\Storage::disk('local')->exists($file->file_path)){
                        \Storage::disk('local')->delete($file->file_path);
                    }
                    $file->delete();
                }

                $gazette->delete();
                SystemLog::create([
                    'user_id' => Auth::user()->id,
                    'section' => 'این جریده توسط این کاربر حذف شده است. (ID: '.$id.')',
                    'type_id' => 4,
                ]);
                $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
            } catch (\Exception $e) {
                $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
            }
        } else {
            $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }
    }

    public function deleteFile($id)
    {
    
        $file = GazetteFile::findOrFail($id);
        $gazette_id = $file->gazette_id;
        if (Storage::disk('local')->exists($file->file_path)) {
            Storage::disk('local')->delete($file->file_path);
        }

        $file->delete();

        SystemLog::create([
            'user_id' => Auth::user()->id,
            'section' => 'این فایل جریده توسط این کاربر حذف شده است. (ID: '.$id.')',
            'type_id' => 4,
        ]);

        $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));

        $this->showFiles($gazette_id);
    }

    public function downloadFile($file_id)
    {
        $file = GazetteFile::findOrFail($file_id);

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

}
