<?php

namespace App\Livewire\Website;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\MenuType;
use App\Models\Settings\SystemLog;
use App\Models\Website\WebMenu;
use App\Models\Website\WebPage;
use Auth;
class WebMenuList extends Component
{
     // -------start generals--------------------
    use WithPagination;
    public $perPage = 5;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'web-menus-addEditModal';
    public $table_name='web-menus';
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
 

    public $name_fa,$name_en,$status = 1,$order,$parent,$grand_parent,$menu_type,$menu_id,$page_id;
    public $parents = [];
    public $show_parent = false;
    public $main_menus = [];
    public $sub_menus = [];
    public $menu_types=[];
    public $pages =[];
    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'main_menus',
            'sub_menus',
            'menu_types',
            'pages',
        ]);
    }
    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------
        
        $this->main_menus = WebMenu::where('type_id', 1)
            ->orderBy('order', 'asc')
            ->get();
        $this->menu_types = MenuType::all();
        $this->pages = WebPage::all();
    }

    public $search = [
            'identity' => null,
            'main_menu' => null,
            'sub_menu' => null,
        ];

    public function render()
    {
        $menus = WebMenu::with(['type','parent','grandParent'])
        ->when(!empty($this->search['identity']), function($q) {
            $search = $this->search['identity'];
            $q->where('name_fa', 'like', "%{$search}%")
            ->orWhere('name_en', 'like', "%{$search}%");
        })
        ->when($this->search['sub_menu'] == '' && $this->search['main_menu'] != '', function($query){
            $query->where(function($q) {
                $q->where('id', $this->search['main_menu'])
                ->orWhere('parent_id', $this->search['main_menu'])
                ->orWhere('grand_parent_id', $this->search['main_menu']);
            });
        })
        ->when($this->search['sub_menu'] != '', function($query){
            $query->where(function($q) {
                $q->where('id', $this->search['sub_menu'])
                ->orWhere('parent_id', $this->search['sub_menu'])
                ->orWhere('grand_parent_id', $this->search['sub_menu']);
            });
        })
        ->orderBy('type_id', 'asc')
        ->orderBy('parent_id', 'asc')
        ->orderBy('order', 'asc')
        ->paginate($this->perPage);

        return view('livewire.website.web-menu-list',compact('menus'));
    }

    protected function rules()
    {
        $rules = [
            'name_en' => 'required|string|max:255|unique:web_menus,name_en,' . $this->menu_id,
            'menu_type' => 'required',
            'status' => 'required',
            'order' => 'required',
        ];

        if (in_array($this->menu_type, [2, 3])) {
            $rules['parent'] = 'required';
        }

        return $rules;
    }
    // Localized messages
    protected function messages()
    {
        return [
            'name_en.required' => __('label.menu_name_en.required'),
            'name_en.unique'   => __('label.menu_name_en.unique'),
            'menu_type.required'   => __('label.menu_type.required'),
            'status.required'   => __('label.status.required'),
            'order.required'   => __('label.order.required'),
            'parent.required'   => __('label.parent.required'),
        ];
    }
    // Create role
    public function store()
    {
        if(add(Auth::user()->role_ids,$this->active_menu_id)){
            $this->validate();
            try{
                WebMenu::create([
                    'name_fa' => $this->name_fa,
                    'name_en' => $this->name_en,
                    'type_id' => $this->menu_type,
                    'page_id' => $this->page_id,
                    'order' => $this->order,
                    'parent_id' => $this->parent,
                    'grand_parent_id' => $this->grand_parent,
                    'status' => $this->status,
                ]);
                $this->closeModal();
                $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));
            }catch (\Exception $e) {
            
                $this->dispatch('alert', type: 'error', message: __('label.store_error').' : '. $e->getMessage());
            }
        }else{
            $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }
    }


    public function edit($id)
    {
        $this->resetValidation(); 
        $this->menu_id = $id;    
        $menu = WebMenu::find($id);
        $this->name_en = $menu->name_en;
        $this->name_fa = $menu->name_fa;
        $this->menu_type = $menu->type_id;
        $this->page_id = $menu->page_id;
        $this->order = $menu->order;
        $this->parent = $menu->parent_id;
        $this->status = $menu->status;
        
        $this->updatedMenuType($this->menu_type);
        $this->updatedParent($this->parent);
        $this->editMode = true;
        $this->dispatch('open-modal', id: $this->modalId);
    }
    // Update role
    public function update()
    {
        if(edit(Auth::user()->role_ids,$this->active_menu_id)){
            $this->validate();

            try {

                WebMenu::findOrFail($this->menu_id)->update([
                        'name_en' => $this->name_en,
                        'name_fa' => $this->name_fa,
                        'type_id' => $this->menu_type,
                        'page_id' => $this->page_id,
                        'order' => $this->order,
                        'parent_id' => $this->parent,
                        'grand_parent_id' => $this->grand_parent,
                        'status' => $this->status,
                    ]);

                $this->updateChildMenus($this->menu_id);
                $this->closeModal();
                $this->dispatch('alert', type: 'success', message: __('label.successfully_updated'));
            } catch (\Exception $e) {
            
                $this->dispatch('alert', type: 'error', message: __('label.update_error').' : '. $e->getMessage());
            }
        }else{
            $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }
    }

    protected function updateChildMenus($menu_id)
    {   
        $menu  = WebMenu::findOrFail($menu_id);
        if($menu->type_id==1){
            $sub_menus= WebMenu::where('parent_id',$menu->id)->get();
            foreach ($sub_menus as $sub) {
                $sub_menu=WebMenu::find($sub->id);
                $sub_menu->parent_id=$menu->id;
                $sub_menu->grand_parent_id=null;
                $sub_menu->type_id=2;
                $sub_menu->save();
                // sub_menu_sub
                WebMenu::where('parent_id',$sub->id)->update([
                    'grand_parent_id' => $menu->id,
                    'parent_id' => $sub->id,
                    'type_id' => 3,
                ]);
            }
        }elseif($menu->type_id==2){
            // sub_menu_sub
            WebMenu::where('parent_id',$menu->id)->update([
                'grand_parent_id' => $menu->parent_id,
                'parent_id' => $menu->id,
                'type_id' => 3,
            ]);
        }elseif($menu->type_id==3){
            // sub_menu_sub
            WebMenu::where('parent_id',$menu->id)->update([
                'parent_id' =>$menu->parent_id,
                'grand_parent_id' =>$menu->grand_parent_id,
                'type_id' => 3,
            ]);
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
                WebMenu::findOrFail($id)->delete();
                SystemLog::create([
                    'user_id' => Auth::user()->id,
                    'section' => 'این منوی ویبسایت توسط این کاربر حذف شده است. (ID: '.$id.')',
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

    // ------------get parrents --------------

    public function updatedMenuType($value)
    {
        if ($value == 1 || $value =='') {
            $this->show_parent = false;
            $this->parent = null;
            $this->grand_parent = null;
            $this->parents = [];
        }elseif($value == 3){
            $parent = WebMenu::find($this->menu_id);
            $this->grand_parent = $parent?->parent_id;
            $this->show_parent = true;
            $this->loadParents($value);
        } else {
            $this->grand_parent = null;
            $this->show_parent = true;
            $this->loadParents($value);
        }
    }

    public function updatedParent($value)
    {
        if ($value) {
            $parent = WebMenu::find($value);
            $this->grand_parent = $parent?->parent_id;
        } else {
            $this->grand_parent = null;
        }
    }

    public function loadParents($menu_type_id)
    {

        $menu_type = ($menu_type_id == 2) ? 1 : 2;

        $this->parents = WebMenu::where('type_id', $menu_type)
            ->where('id','<>',$this->menu_id)
            ->select('id', 'name_en','name_fa')->orderBy('order','ASC')
            ->get();
    }
    
   public function loadSubMenus($main_menu_id)
    {
        $this->sub_menus = WebMenu::where('type_id', 2)
            ->where('parent_id', $main_menu_id)
            ->orderBy('order', 'asc')
            ->get();
    }
}
