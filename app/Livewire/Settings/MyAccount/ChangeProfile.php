<?php

namespace App\Livewire\Settings\MyAccount;

use Livewire\Component;
use App\Models\Settings\Menu;
use App\Models\User;
use Auth;
use Hash;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Validator;

class ChangeProfile extends Component{

    use WithFileUploads;
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;

    public $name = '';
    public $phone_no = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';

    public $profile_photo;
    public $profile_photo_exists;
    // Hook for real time error message

    public $current_password;
    public $new_password;
    public $new_password_confirmation;
   public function updated($propertyName)
    {
        // validate فیلدهای معمولی
        if (array_key_exists($propertyName, $this->rules())) {
            $this->validateOnly($propertyName);
        }

        // validate فیلدهای password
            if (in_array($propertyName, ['current_password','new_password','new_password_confirmation'])) {
                
                if ($propertyName === 'new_password' && empty($this->new_password_confirmation)) {
                    $this->validateOnly(
                        $propertyName,
                        [
                            'new_password' => ['required', 'string', 'min:8', 'regex:/^(?=.*[A-Z])(?=.*[0-9])(?=.*[@$!%*#?&]).+$/']
                        ],
                        [
                            'new_password.required' => __('label.new_password') . ' ' . __('label.is_required'),
                            'new_password.min'      => __('label.password_min'),
                            'new_password.regex'    => __('validation.password_regex'),
                        ]
                    );
                } else {
                    // بقیه فیلدها یا وقتی تایید پر شده است
                    $this->validateOnly(
                        $propertyName,
                        $this->passwordRules(),
                        $this->passwordMessages()
                    );
                }
            }

    }

    public function mount($active_menu_id = null){
    
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

       $this->edit();
    }


    public function render()
    {
        return view('livewire.settings.my-account.change-profile');
    }

    protected function rules()
    {
        return [
            'phone_no' => 'nullable|numeric|unique:users,phone_no,' . Auth::id(),
            'profile_photo' => 'nullable|image|mimes:jpeg,jpg,png|max:10240',
        ];
    }

    // Localized messages
    protected function messages()
    {
        return [
            'phone_no.unique' => __('label.phone_no.unique'),
            'phone_no.max'    => __('label.phone_no.max'),
            'phone_no.numeric'    => __('label.phone_no.numeric'),
            'profile_photo.image' => __('label.profile_photo_image'),
            'profile_photo.max'   => __('label.profile_photo_max'),
        ];
    }

    public function edit(){
        $this->name = Auth::user()->name;
        $this->phone_no = Auth::user()->phone_no;
        $this->profile_photo_exists = Auth::user()->profile_photo;
    }

    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'profile_photo_exists',
        ]);
    }

    public function update()
    {
        $this->validate();

        try {
            $user = User::findOrFail(Auth::id());
            $user->update([
                'name' => $this->name,
                'phone_no' => $this->phone_no,
            ]);
            // dd($this->profile_photo);
            if ($this->profile_photo) {

                if ($user->profile_photo && \Storage::disk('public')->exists($user->profile_photo)) {
                    \Storage::disk('public')->delete($user->profile_photo);
                }

                $file = $this->profile_photo;
                $storedName = Str::random(40) . '.' . $file->getClientOriginalExtension();

                $path = $file->storeAs(
                    'profile_photo/' . $user->id,
                    $storedName,
                    'public' 
                );

                $user->profile_photo = $path;
                $user->save();
                $this->profile_photo_exists = $path;
            }

            $this->dispatch('alert', type: 'success', message: __('label.successfully_updated'));
           
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.update_error').' : '. $e->getMessage());
        }
    }
 
   protected function passwordRules()
    {
        return [
            'current_password' => ['required'],
            'new_password' => [
                'required',
                'string',
                'confirmed',
                'min:8',
                'regex:/^(?=.*[A-Z])(?=.*[0-9])(?=.*[@$!%*#?&]).+$/'
            ],
            'new_password_confirmation' => ['required'],
        ];
    }

    protected function passwordMessages()
    {
        return [
            'current_password.required'    => __('label.current_password') . ' ' . __('label.is_required'),
            'new_password.required'        => __('label.new_password') . ' ' . __('label.is_required'),
            'new_password_confirmation.required'        => __('label.confirm_password') . ' ' . __('label.is_required'),
            'new_password.confirmed'       => __('label.confirm_password') . ' ' . __('label.not_match'),
            'new_password.min' =>  __('label.password_min'),
            'new_password.regex' =>  __('validation.password_regex'),
        ];
    }
    public function updatePassword()
    {
        $this->validate($this->passwordRules(), $this->passwordMessages());
        
        try {

            $user = Auth::user();

            if (!Hash::check($this->current_password, $user->password)) {
                $this->addError('current_password', __('label.current_password').' اشتباه است');
                return;
            }

            $user->password = Hash::make($this->new_password);
            $user->save();

            $this->dispatch('alert', type: 'success', message: __('label.successfully_updated'));

            $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.update_error').' : '. $e->getMessage());
        }
    }

}