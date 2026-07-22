<?php

namespace App\Livewire\Settings\NotificationCategoryRole;

use Livewire\Component;
use App\Models\Settings\Menu;
use App\Models\Settings\MenuType;
use App\Models\Settings\Action;
use App\Models\Settings\AccessRole;
use App\Models\Settings\Permission;
use App\Models\Settings\NotificationCategory;
use DB;
use Auth;
class NotificationCategoryRoleList extends Component
{
    
    // -------start generals--------------------
    public $active_menu_id;
    public $active_menu;

    // ---------------------------------end generals-------------

    public $access_role;

    public $show_table=false;

    public $roles =[];

    public $permissions = [];
    public $check_all = false;

    public array $notification_category_ids = [];
    public function resetInputFields(){
        $this->resetExcept([
            'active_menu',
            'search',
            'roles',
        ]);
    }

    
    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        $this->active_menu_id= $active_menu_id;
        // -------------start for activing menu in sidebar ----------------------

        $this->menu_types = MenuType::all();
        $this->access_roles = AccessRole::where('is_system',false)->get();
        $this->roles = AccessRole::where('is_system',false)->get();
        
    }

    public $search = [
            'identity' => null,
            'access_role' => null,
        ];

    
    public function render()
    {
        $notification_categories = NotificationCategory::orderBy('name')->get();
        return view('livewire.settings.notification-category-role.notification-category-role-list',compact('notification_categories'));
    }

    public function applySearch()
    {
        if (!$this->search['access_role']) {

            $this->notification_category_ids = [];

            return;
        }

        $role = AccessRole::find($this->search['access_role']);

        $this->notification_category_ids = $role
            ->notificationCategories()
            ->pluck('notification_categories.id')
            ->toArray();
    }

    public function updatedSearchAccessRole(){
        $this->applySearch();
    }


    public function savePermissions()
    {

        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }


        try {

            $role = AccessRole::find($this->search['access_role']);

            if (!$role) {

                $this->dispatch('alert', type: 'error', message: __('label.role.required'));
                return;

            }


            if (empty($this->notification_category_ids)) {

                $this->dispatch(
                    'alert',
                    type: 'error',
                    message: __('label.notification_category_required')
                );

                return;
            }
            
            $role->notificationCategories()
                ->sync($this->notification_category_ids);

            $this->dispatch(
                'alert',
                type: 'success',
                message: __('label.successfully_done')
            );
        

        } catch (\Exception $e) {

            $this->dispatch('alert', type: 'error', message: __('label.store_error') . ': ' . $e->getMessage());
        }
    }

}
