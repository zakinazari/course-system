<?php

namespace App\Livewire\Settings\Users;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\AccessRole;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\User;
use Auth;
class UserList extends Component
{
     // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'user-list-addEditModal';
    public $table_name='users';
    
    public $access_roles= [];
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
        
        $this->access_roles = AccessRole::all();
    }

    public $name, $user_id,$email, $role_ids=[],$check_all = false;
    public $password, $confirm_password;
    public $show_password = false;
    public $show_confirm_password = false;


    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'access_roles',
        ]);
    }
    public $search = [
            'identity' => null,
            'role' => null,
        ];

    public function render()
    {
         $users = User::with('roles')
            ->when(!empty($this->search['identity']), function ($query) {
                $query->where('name', 'like', '%' . $this->search['identity'] . '%')
                ->orWhere('email',$this->search['identity']);
            })
            ->when(!empty($this->search['role']), function ($query) {
                $query->whereHas('roles', function ($q) {
                        $searchTerm = $this->search['role'];
                        $q->where('role_name',$searchTerm);
                    });
            })
            
            ->paginate($this->perPage);
        return view('livewire.settings.users.user-list',compact('users'));
    }

    public function togglePassword()
    {
        $this->show_password = !$this->show_password;
    }

    public function toggleConfirmPassword()
    {
        $this->show_confirm_password = !$this->show_confirm_password;
    }
    
    protected function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:users,name,' . $this->user_id,

            'email' => 'required|email|unique:users,email,' . $this->user_id,

            'role_ids' => 'required|array',

            // Password Rules
            'password' => [
                $this->editMode ? 'nullable' : 'required',
                'string',
                'min:8',                      
                'regex:/[A-Z]/',             
                'regex:/[0-9]/',              
                'regex:/[@$!%*#?&]/',       
            ],

            'confirm_password' => 'same:password',
        ];
    }

    // Localized messages
    protected function messages()
    {
        return [

            // NAME
            'name.required' => __('label.name_required'),
            'name.string'   => __('label.name_string'),
            'name.max'      => __('label.name_max'),
            'name.unique'   => __('label.name_unique'),

            // EMAIL
            'email.required' => __('label.email_required'),
            'email.email'    => __('label.email_invalid'),
            'email.unique'   => __('label.email_unique'),

            // ROLES
            'role_ids.required' => __('label.roles_required'),
            'role_ids.array'    => __('label.roles_array'),

            // PASSWORD
            'password.required' => __('label.password_required'),
            'password.string'   => __('label.password_string'),
            'password.min'      => __('label.password_min'),
            'password.regex'    => __('label.password_regex'),

            // CONFIRM PASSWORD
            'confirm_password.same' => __('label.password_confirm_same'),
        ];
    }


    public function store()
    {
    
        $this->validate();
        try{
            $user = User::create([
                'name'     => $this->name,
                'email'    => $this->email,
                'password' => bcrypt($this->password),
            ]);

            if(!empty($this->role_ids)){
                $user->roles()->sync($this->role_ids);
            }

            $this->closeModal();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));
        }catch (\Exception $e) {
        
            $this->dispatch('alert', type: 'error', message: __('label.store_error').' : '. $e->getMessage());
        }
    }

    public function edit($id)
    {
        
        $user = User::with('roles')->findOrFail($id);

        $this->user_id = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;

        $this->role_ids = $user->roles?->pluck('id')->toArray();
        $this->check_all = count($this->role_ids) === $this->access_roles->count();
        $this->editMode = true;
        $this->dispatch('open-modal', id: $this->modalId);
    }

    public function update()
    {
        $this->validate();

        try {
            $user = User::findOrFail($this->user_id);

            $data = [
                'name'  => $this->name,
                'email' => $this->email,
            ];

            if (!empty($this->password)) {
                $data['password'] = bcrypt($this->password);
            }

            $user->update($data);
            $user->roles()->sync($this->role_ids);

            $this->closeModal();
            $this->resetInputFields();
            $this->editMode = false;

            $this->dispatch('alert', type: 'success', message: __('label.successfully_updated'));

        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.update_error').' : '. $e->getMessage());
        }
    }


    public function toggleSelectAll()
    {
        if ($this->check_all) {

            $this->role_ids = $this->access_roles->pluck('id')->toArray();
        } else {
            $this->role_ids = [];
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
                User::findOrFail($id)->delete();
                SystemLog::create([
                    'user_id' => Auth::user()->id,
                    'section' => 'این کاربر توسط این کاربر حذف شده است. (ID: '.$id.')',
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
