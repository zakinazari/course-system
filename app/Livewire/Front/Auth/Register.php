<?php

namespace App\Livewire\Front\Auth;
use Livewire\Component;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Models\Settings\AccessRole;
use App\Models\Settings\Country;
use App\Models\Settings\EducationDegree;
use App\Models\Settings\AcademicRank;
use App\Models\Settings\Province;
use App\Models\Website\WebMenu;
class Register extends Component
{
    public string $name = '';
    public string $name_fa = '';
    public string $name_en = '';
    public string $family_name_fa = '';
    public string $family_name_en = '';
    public string $phone_no = '';
    public string $affiliation_fa = '';
    public string $affiliation_en = '';
    public ?int $country_id = 0;
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public $countries;
    public $education_degrees;
    public $academic_ranks;
    public $provinces;
    public $province_id;
    public $education_degree_id;
    public $academic_rank_id;
    public $city_fa;
    public $city_en;
    public $department_fa;
    public $department_en;
    public $preferred_research_area_fa;
    public $preferred_research_area_en;
    public $active_menu_id;
    public $active_menu;

    public $g_recaptcha_response;
    protected $listeners = ['setRecaptcha'];

    public function setRecaptcha($token)
    {
        $this->g_recaptcha_response = $token;
        $this->resetErrorBag('g_recaptcha_response'); 
    }

    public function mount($active_menu_id = null){

        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = WebMenu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->countries = Country::all();
        $this->education_degrees = EducationDegree::all();
        $this->academic_ranks = AcademicRank::all();
        $this->provinces = Province::all();
    }

    // Hook for real time error message

    public function updatedPassword()
    {
        $this->resetErrorBag('password');
        $this->resetErrorBag('password_confirmation');
    }

    public function updatedPasswordConfirmation()
    {
        $this->resetErrorBag('password');
        $this->resetErrorBag('password_confirmation');
    }
    public function updated($propertyName)
    {
        if (array_key_exists($propertyName, $this->rules())) {
            $this->validateOnly($propertyName);
        }
    }

    
    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:users,name'],
            'name_fa' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'family_name_fa' => ['nullable', 'string', 'max:255'],
            'family_name_en' => ['nullable', 'string', 'max:255'],
            'phone_no' => ['required', 'string', 'max:16', 'unique:users,phone_no'],
            'affiliation_fa' => ['nullable', 'string', 'max:255'],
            'affiliation_en' => ['nullable', 'string', 'max:255'],
            'country_id' => ['required', 'integer', 'min:1'],
            'education_degree_id' => ['required', 'integer'],
            'academic_rank_id' => ['required', 'integer'],
            'province_id' => ['required', 'integer'],
            'city_fa' => ['nullable', 'string', 'max:255'],
            'city_en' => ['nullable', 'string', 'max:255'],
            'department_fa' => ['required', 'string', 'max:255'],
            'department_en' => ['nullable', 'string', 'max:255'],
            'preferred_research_area_fa' => ['required', 'string', 'max:255'],
            'preferred_research_area_en' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::defaults(), 'min:8',                      
                'regex:/^(?=.*[A-Z])(?=.*[0-9])(?=.*[@$!%*#?&]).+$/',],
            // captcha--------------------
            'g_recaptcha_response' => 'required|captcha',
        ];
    }

    protected function messages()
    {
        return [
            // ------------------ Name ------------------
            'name.required' => __('validation.name_required'), // فارسی، پشتو، انگلیسی در فایل lang
            'name.string' => __('validation.name_string'),
            'name.max' => __('validation.name_max'),
            'name.unique' => __('validation.name_unique'),

            'name_fa.required' => __('validation.name_fa_required'),
            'name_fa.string' => __('validation.name_fa_string'),
            'name_fa.max' => __('validation.name_fa_max'),

            'name_en.string' => __('validation.name_en_string'),
            'name_en.max' => __('validation.name_en_max'),

            // ------------------ Family Name ------------------
            'family_name_fa.string' => __('validation.family_name_fa_string'),
            'family_name_fa.max' => __('validation.family_name_fa_max'),

            'family_name_en.string' => __('validation.family_name_en_string'),
            'family_name_en.max' => __('validation.family_name_en_max'),

            // ------------------ Phone ------------------
            'phone_no.required' => __('validation.phone_required'),
            'phone_no.string' => __('validation.phone_string'),
            'phone_no.max' => __('validation.phone_max'),
            'phone_no.unique' => __('validation.phone_unique'),

            // ------------------ Affiliation ------------------
            'affiliation_fa.string' => __('validation.affiliation_fa_string'),
            'affiliation_fa.max' => __('validation.affiliation_fa_max'),

            'affiliation_en.string' => __('validation.affiliation_en_string'),
            'affiliation_en.max' => __('validation.affiliation_en_max'),

            // ------------------ Country ------------------
            'country_id.required' => __('validation.country_required'),
            'country_id.integer' => __('validation.country_integer'),
            'country_id.min' => __('validation.country_min'),

            // ------------------ Education Degree ------------------
            'education_degree_id.required' => __('validation.education_degree_required'),
            'education_degree_id.integer' => __('validation.education_degree_integer'),

            // ------------------ Academic Rank ------------------
            'academic_rank_id.required' => __('validation.academic_rank_required'),
            'academic_rank_id.integer' => __('validation.academic_rank_integer'),

            // ------------------ Province ------------------
            'province_id.required' => __('validation.province_required'),
            'province_id.integer' => __('validation.province_integer'),

            // ------------------ City ------------------
            'city_fa.string' => __('validation.city_fa_string'),
            'city_fa.max' => __('validation.city_fa_max'),

            'city_en.string' => __('validation.city_en_string'),
            'city_en.max' => __('validation.city_en_max'),

            // ------------------ Department ------------------
            'department_fa.required' => __('validation.department_fa_required'),
            'department_fa.string' => __('validation.department_fa_string'),
            'department_fa.max' => __('validation.department_fa_max'),

            'department_en.string' => __('validation.department_en_string'),
            'department_en.max' => __('validation.department_en_max'),

            // ------------------ Preferred Research Area ------------------
            'preferred_research_area_fa.required' => __('validation.research_area_fa_required'),
            'preferred_research_area_fa.string' => __('validation.research_area_fa_string'),
            'preferred_research_area_fa.max' => __('validation.research_area_fa_max'),

            'preferred_research_area_en.string' => __('validation.research_area_en_string'),
            'preferred_research_area_en.max' => __('validation.research_area_en_max'),

            // ------------------ Email ------------------
            'email.required' => __('validation.email_required'),
            'email.string' => __('validation.email_string'),
            'email.email' => __('validation.email_email'),
            'email.max' => __('validation.email_max'),
            'email.unique' => __('validation.email_unique'),

            // ------------------ Password ------------------
            'password.required'  => __('validation.password_required'),
            'password.string'    => __('validation.password_string'),
            'password.confirmed' => __('validation.password_confirmed'),
            'password.min'       => __('validation.password_min'),
            'password.required'  => __('validation.password_required'),
            'password.regex'     => __('validation.password_regex'),

            'g_recaptcha_response.required' => __('validation.recaptcha'),
            'g_recaptcha_response.captcha'  => __('validation.recaptcha'),
        ];
    }



   public function register()
    {
       
            $role_id = AccessRole::where('role_name', 'Author')->value('id');
            if (!$role_id) {
                session()->flash('error', 'نقش Author موجود نیست، ثبت نام انجام نشد.');
                return;
            }
            $validated = $this->validate();
            
            $validated['password'] = Hash::make($validated['password']);

        try {
            $user = User::create($validated);

            event(new Registered($user));

            Auth::login($user);

            Session::regenerate();

            $user->roles()->sync($role_id);

            return redirect()->intended(route('dashboard'));

        } catch (\Illuminate\Validation\ValidationException $e) {
            $messages = collect($e->errors())->flatten()->join(', ');
            session()->flash('error', $messages);
            return;
        } catch (\Exception $e) {
            session()->flash('error', 'خطا: ' . $e->getMessage());
            return;
        }
    }


    public function render()
    {
        return view('livewire.front.auth.register');
    }
}
