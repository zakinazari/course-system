<?php

namespace App\Livewire\Website\AboutUs;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\Website\AboutUs;
use Storage;
use Auth;
use Carbon\Carbon;
class AboutUsList extends Component
{
     // -------start generals--------------------
    use WithPagination;
    use WithFileUploads;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'about-us-list-addEditModal';
    public $table_name='about_us';

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

    public $about_us_id,$title_fa,$title_en,$phone,$email,$facebook,$website,$logo,$existing_logo,$address_fa,$address_en;

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
        $about_us = AboutUs::query()
            ->when(!empty($this->search['identity']), function ($query) {
                $searchTerm = $this->search['identity'];
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('title_fa', 'like', "%{$searchTerm}%")
                    ->orWhere('title_en', 'like', "%{$searchTerm}%");
                });
            })
            ->paginate($this->perPage);
        return view('livewire.website.about-us.about-us-list',compact('about_us'));
    }

    protected function rules()
    {
        return [
            'title_fa' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
        ];
    }
    
    protected function messages()
    {
        return [

            'title_fa.required' => __('label.title.required'),
            'title_en.required'   => __('label.title.required'),
            'logo.*.max' => __('label.file_max',['value'=>1]),
            
        ];
    }

    public function store()
    {
        if (add(Auth::user()->role_ids, $this->active_menu_id)) {

            $this->validate();

            try {
                $about_us = AboutUs::create([
                    'title_fa' => $this->title_fa,
                    'title_en' => $this->title_en,
                    'phone' => $this->phone,
                    'email' => $this->email,
                    'facebook' => $this->facebook,
                    'website' => $this->website,
                    'address_fa' => $this->address_fa,
                    'address_en' => $this->address_en,
                ]);

                if ($this->logo) {

                    $file = $this->logo;

                    $storedName = Str::random(40) . '.' . $file->getClientOriginalExtension();

                    $path = $file->storeAs(
                        'website/about-us/' . $about_us->id,
                        $storedName,
                        'public' 
                    );

                    $about_us->logo = $path;
                    $about_us->save();
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
        $about_us = AboutUs::findOrFail($id);

        $this->about_us_id = $id;
        $this->title_fa = $about_us->title_fa;
        $this->title_en = $about_us->title_en;
        $this->phone = $about_us->phone;
        $this->email = $about_us->email;
        $this->facebook = $about_us->facebook;
        $this->website = $about_us->website;
        $this->address_fa = $about_us->address_fa;
        $this->address_en = $about_us->address_en;
        $this->existing_logo = $about_us->logo;
        $this->editMode = true;

        $this->dispatch('open-modal', id: $this->modalId);
         $this->dispatch('loadEditors'); 
    }

   
    public function update()
    {
        if(edit(Auth::user()->role_ids,$this->active_menu_id)){
            $this->validate();

            try {

                $about_us = AboutUs::findOrFail($this->about_us_id);
                $about_us->update([
                    'title_fa' => $this->title_fa,
                    'title_en' => $this->title_en,
                    'phone' => $this->phone,
                    'email' => $this->email,
                    'facebook' => $this->facebook,
                    'website' => $this->website,
                    'address_fa' => $this->address_fa,
                    'address_en' => $this->address_en,
                ]);

                if ($this->logo) {

        
                    if ($about_us->logo && \Storage::disk('public')->exists($about_us->logo)) {
                        \Storage::disk('public')->delete($about_us->logo);
                    }

                    $file = $this->logo;
                    $storedName = Str::random(40) . '.' . $file->getClientOriginalExtension();

                    $path = $file->storeAs(
                        'website/about-us/' . $about_us->id,
                        $storedName,
                        'public' 
                    );

                    $about_us->logo = $path;
                    $about_us->save();
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
                $about_us = AboutUs::findOrFail($id);
                if ($about_us->logo && \Storage::disk('public')->exists($about_us->logo)) {
                    \Storage::disk('public')->delete($about_us->logo);
                }
                $about_us->delete();
                $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
            } catch (\Exception $e) {
                $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
            }
        }else{
            $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }
    }
}
