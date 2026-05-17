<?php

namespace App\Livewire\Financial\Accounts;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\CenterSettings\Branch;
use App\Models\Financial\Account;
use Auth;
class AccountList extends Component
{
    
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'account-list-addEditModal';
    public $table_name='accounts';
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
    public $branches;
    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->branches =  Branch::all();
    }

     public $account_id,$name,$branch_id;
     public $type='branch';
     public $category = 'treasury';
     public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'branches',
        ]);
    }
    public $search = [
            'name' => null,
            'branch_id' => null,
        ];


    public function render()
    {
         $accounts = Account::with('branch')
        ->when(!empty($this->search['name']), function ($query) {
            $query->where('name', 'like', '%' . $this->search['name'] . '%');
        })
        ->when(!empty($this->search['branch_id']), function ($query) {
            $query->where('branch_id',$this->search['branch_id']);
        })
        ->orderBy('id','desc')
        ->paginate($this->perPage);

        return view('livewire.financial.accounts.account-list',compact('accounts'));
    }

    protected function rules()
    {
        $branch_id = Auth::user()->branch_id ?: $this->branch_id;

        $rules = [

            'name' => 'required|string|max:255|unique:accounts,name,' .
                ($this->editMode ? $this->account_id : 'NULL') . ',id',

            'type' => 'required|in:central,branch',

            'category' => 'required|in:treasury,cash,bank,other',
        ];

        // فقط برای حساب branch
        if ($this->type != 'central' && !Auth::user()->branch_id) {
            $rules['branch_id'] = 'required';
        }

        return $rules;
    }


    // Localized messages
    protected function messages()
    {
        return [

            'name.required' => __('label.name.required'),

            'branch_id.required' => __('label.branch.required'),

            'type.required' => __('label.type.required'),

            'category.required' => __('label.category.required'),
        ];
    }


    // Create Account
    public function store()
    {
        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {

            return $this->dispatch(
                'alert',
                type: 'error',
                message: __('label.permission_message')
            );
        }

        $this->validate();

        try {

        if ($this->category === 'treasury') {

            $branch_id = Auth::user()->branch_id ?: $this->branch_id;

                $exists = Account::query()

                    ->when(
                        $this->type === 'central',

                        fn($q) => $q->where('type', 'central'),

                        fn($q) => $q->where('branch_id', $branch_id)
                    )

                    ->where('category', 'treasury')

                    ->when($this->editMode, function ($q) {
                        $q->where('id', '!=', $this->account_id);
                    })

                    ->exists();

                if ($exists) {

                    return $this->dispatch(
                        'alert',
                        type: 'error',
                        message: 'Treasury account already exists'
                    );
                }
            }

            $account = Account::create([

                'name' => $this->name,

                'type' => $this->type,

                'category' => $this->category,

                'is_default' => $this->category == 'treasury',

                'balance' => 0,

                'branch_id' => $this->type === 'central'
                    ? null
                    : (Auth::user()->branch_id ?: $this->branch_id),
            ]);

            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.account') .
                    ' (' . $account->name . ' ID:' . $account->id . ')',
                'type_id' => 2,
            ]);
            // ---end system log-------------

            $this->closeModal();

            $this->dispatch(
                'alert',
                type: 'success',
                message: __('label.successfully_done')
            );

        } catch (\Exception $e) {

            $this->dispatch(
                'alert',
                type: 'error',
                message: __('label.store_error') . ': ' . $e->getMessage()
            );
        }
    }


    public function edit($id)
    {
        $this->resetValidation(); 
        $this->account_id = $id;    
        $account = Account::find($id);
        $this->name = $account->name;
        $this->branch_id = $account->branch_id;
        $this->type = $account->type;
        $this->editMode = true;
        $this->dispatch('open-modal', id: $this->modalId);
    }
    // Update role
    public function update()
    {
        if(!edit(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        $this->validate();
        try {
            
            $branch_id = Auth::user()->branch_id ?: $this->branch_id;

            $exists = Account::query()

                ->when(
                    $this->type === 'central',

                    fn($q) => $q->where('type', 'central'),

                    fn($q) => $q->where('branch_id', $branch_id)
                )

                ->where('category', 'treasury')

                ->when($this->editMode, function ($q) {
                    $q->where('id', '!=', $this->account_id);
                })

                ->exists();

            if ($exists) {

                return $this->dispatch(
                    'alert',
                    type: 'error',
                    message: 'Treasury account already exists'
                );
            }

            $account = Account::findOrFail($this->account_id);
            $account->update([
                'name' => $this->name,
                'type' => $this->type,
                'branch_id' => $this->type === 'central'
                    ? null
                    : (Auth::user()->branch_id ?: $this->branch_id),
            ]);
            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.account').' ('.$account->name.' ID:'.$account->id.')',
                'type_id' => 3,
            ]);
            // ---end system log-------------
            $this->closeModal();
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
        if(!delete(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        try {
            $account = Account::findOrFail($id);
             // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.account').' ('.$account->name.' ID:'.$account->id.')',
                'type_id' => 4,
            ]);
            // ---end system log-------------
            $account->delete();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }
}
