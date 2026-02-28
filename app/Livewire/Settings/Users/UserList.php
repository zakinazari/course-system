<?php

namespace App\Livewire\Settings\Users;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\AccessRole;
use App\Models\CenterSettings\Branch;
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
    public $branches= [];
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
        
        $this->access_roles = AccessRole::where('is_system',false)->get();
        $this->branches = Branch::all();
    }

    public $name, $user_id,$email, $role_id,$branch_id,$check_all = false,$check_all_branch = false;
    public $password, $confirm_password;
    public $show_password = false;
    public $show_confirm_password = false;
    public $is_active = 1;


    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'access_roles',
            'branches',
        ]);
    }
    public $search = [
            'identity' => null,
            'role' => null,
        ];

    public function render()
    {
         $users = User::with('role','branch')
           ->whereHas('role', function ($q) {
                $q->where('is_system',false);
            })

            ->when(!empty($this->search['identity']), function ($query) {
                $query->where('name', 'like', '%' . $this->search['identity'] . '%')
                ->orWhere('email',$this->search['identity']);
            })
            ->when(!empty($this->search['role']), function ($query) {
                $query->whereHas('role', function ($q) {
                        $searchTerm = $this->search['role'];
                        $q->where('id',$searchTerm);
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

            'role_id' => 'required',
            'is_active' => 'required|boolean',

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
                'role_id'    => $this->role_id,
                'branch_id' =>  Auth::user()->branch_id ?: $this->branch_id,
                'password' => bcrypt($this->password),
                'is_active' => $this->is_active,
            ]);

            $user = SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.user').'('.$user->name.' ID: '.$user->id.')',
                'type_id' => 2,
            ]);
        
            $this->closeModal();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));
        }catch (\Exception $e) {
        
            $this->dispatch('alert', type: 'error', message: __('label.store_error').' : '. $e->getMessage());
        }
    }

    public function edit($id)
    {
        
        $user = User::findOrFail($id);

        $this->user_id = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role_id = $user->role_id;
        $this->branch_id = $user->branch_id;
        $this->is_active =$user->is_active;
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
                'role_id' => $this->role_id,
                'branch_id' =>  Auth::user()->branch_id ?: $this->branch_id,
                'is_active' => $this->is_active,
            ];

            if (!empty($this->password)) {
                $data['password'] = bcrypt($this->password);
            }

            $user->update($data);   

            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.user').'('.$user->name.' ID: '.$user->id.')',
                'type_id' => 3,
            ]);

            $this->closeModal();
            $this->resetInputFields();
            $this->editMode = false;

            $this->dispatch('alert', type: 'success', message: __('label.successfully_updated'));

        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.update_error').' : '. $e->getMessage());
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
                $user = User::findOrFail($id);
                SystemLog::create([
                    'user_id' => Auth::user()->id,
                    'section' => __('label.user').'('.$user->name.' ID: '.$id.')',
                    'type_id' => 4,
                ]);
                $user->delete();
                $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
            } catch (\Exception $e) {
                $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
            }
        }else{
            $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }
    }

}
