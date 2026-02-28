<?php

namespace App\Livewire\Settings\Permissions;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\MenuType;
use App\Models\Settings\Action;
use App\Models\Settings\AccessRole;
use App\Models\Settings\Permission;
use DB;
use Auth;
class PermissionList extends Component
{
    // -------start generals--------------------
    public $active_menu_id;
    public $active_menu;

    // ---------------------------------end generals-------------

    public $name,$name_en,$parent,$grand_parent,$menu_type,$access_role;

    public $show_table=false;
    public $parents = [];
    public $show_parent = false;
    public $main_menus = [];
    public $sub_menus = [];
    public $menu_types=[];
    public $access_roles =[];
    public $actions =[];
    public $menus = [];

    public $permissions = [];
    public $check_all = false;
    public function resetInputFields(){
        $this->resetExcept([
            'active_menu',
            'search',
            'main_menus',
            'sub_menus',
            'menu_types',
            'access_roles',
            'actions',
            'permissions',
        ]);
    }
    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        $this->active_menu_id= $active_menu_id;
        // -------------start for activing menu in sidebar ----------------------
        
        $this->main_menus = Menu::where('type_id', 1)
            ->orderBy('order', 'asc')
            ->get();
        $this->menu_types = MenuType::all();
        $this->access_roles = AccessRole::where('is_system',false)->get();
        $this->actions = Action::all();
        
    }

    public $search = [
            'identity' => null,
            'main_menu' => null,
            'sub_menu' => null,
            'access_role' => null,
        ];

    
    public function render()
    {
        return view('livewire.settings.permissions.permission-list');
    }

    public function applySearch()
    {
        $role_id = $this->search['access_role'];

        if (empty($role_id)) {
            $this->dispatch('alert', type: 'error', message: 'Please select an access role first!');
            return;
        }

        $this->show_table = true;

        $this->menus = Menu::with([
            'type','section','parent','grandParent',
            'permission' => function ($query) use ($role_id) {
                $query->where('role_id', $role_id);
            }
        ])
        ->when(!empty($this->search['identity']), function ($q) {
            $search = $this->search['identity'];
            $q->where(function ($qq) use ($search) {
                $qq->where('name_fa', 'like', "%{$search}%")
                ->orWhere('name_en', 'like', "%{$search}%")
                ->orWhere('name_pa', 'like', "%{$search}%")
                ->orWhere('url', 'like', "%{$search}%");
            });
        })
        ->when(empty($this->search['sub_menu']) && !empty($this->search['main_menu']), function ($query) {
            $query->where(function ($q) {
                $q->where('id', $this->search['main_menu'])
                ->orWhere('parent_id', $this->search['main_menu'])
                ->orWhere('grand_parent_id', $this->search['main_menu']);
            });
        })
        ->when(!empty($this->search['sub_menu']), function ($query) {
            $query->where(function ($q) {
                $q->where('id', $this->search['sub_menu'])
                ->orWhere('parent_id', $this->search['sub_menu'])
                ->orWhere('grand_parent_id', $this->search['sub_menu']);
            });
        })
        ->orderBy('type_id')
        ->orderBy('parent_id')
        ->orderBy('order')
        ->get();

        $this->permissions = [];
        $this->check_all = false;
        $this->reset('permissions');
        foreach ($this->menus as $menu) {
            foreach ($this->actions as $action) {
                $this->permissions[$menu->id][$action->id] =
                    optional($menu->permission)->contains('action_id', $action->id);
            }
        }
    }


    public function checkAll()
    {
        foreach ($this->menus as $menu) {
            foreach ($this->actions as $action) {
                $this->permissions[$menu->id][$action->id] = $this->check_all;
            }
        }
    }

    public function updatedSearchAccessRole($value)
    {
        $this->check_all = false;
    }
 
    public function savePermissions()
    {

        if(read(Auth::user()->role_ids,$this->active_menu_id)){
            $role_id = $this->search['access_role'];

            if (empty($role_id)) {
                $this->dispatch('alert', type: 'error', message: 'Please select an access role first!');
                return;
            }

            $permissions_data = $this->permissions ?? [];

            DB::transaction(function () use ($role_id, $permissions_data) {

                foreach ($permissions_data as $menu_id => $actions) {
                    foreach ($actions as $action_id => $checked) {
                        if ($checked) {
                            Permission::updateOrCreate(
                                [
                                    'role_id'   => $role_id,
                                    'menu_id'   => $menu_id,
                                    'action_id' => $action_id,
                                ],
                                [
                                    'role_id'   => $role_id,
                                    'menu_id'   => $menu_id,
                                    'action_id' => $action_id,
                                ]
                            );
                        } else {
                            Permission::where('role_id', $role_id)
                                ->where('menu_id', $menu_id)
                                ->where('action_id', $action_id)
                                ->delete();
                        }
                    }
                }
            });
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));
            $this->applySearch();
        }else{
            $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }
    }



    public function updatedParent($value)
    {
        if ($value) {
            $parent = Menu::find($value);
            $this->grand_parent = $parent?->parent_id;
        } else {
            $this->grand_parent = null;
        }
    }

    public function loadParents($menu_type_id)
    {

        $menu_type = ($menu_type_id == 2) ? 1 : 2;

        $this->parents = Menu::where('type_id', $menu_type)
            ->where('id','<>',$this->menu_id)
            ->select('id', 'name_en')->orderBy('order','ASC')
            ->get();
    }
    
    public function loadSubMenus($main_menu_id)
    {
        $this->sub_menus = Menu::where('type_id', 2)
            ->where('parent_id', $main_menu_id)
            ->orderBy('order', 'asc')
            ->get();
    }
    
}
