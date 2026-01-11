<div>
    <!-- title -->
    @section('title',
    (
        ($active_menu?->parent?->{app()->getLocale() === 'fa' ? 'name' : 'name_en'} ?? '') 
        ? $active_menu?->parent?->{app()->getLocale() === 'fa' ? 'name' : 'name_en'} . '-' 
        : ''
    ) 
    . $active_menu?->{app()->getLocale() === 'fa' ? 'name_fa' : 'name_en'} 
    . ' | ' . __('label.app_name')
    )
    <!-- end title -->
    <div class="login" style="padding:35px !important;">
        <div class="enroll-courses" style="padding:6px;">
            <a href="{{ url('/') }}">{{ __('label.main_page') }} <span style="color:#999 !important;"> / {{ __('label.register') }}</span></a>
        </div>
        <h3>{{ __('label.profile') }}</h3>

        <form wire:submit.prevent="register">

            <div class="form-group mb-2">
                <label>{{ __('label.given_name') }} <span style="color:red;">*</span></label>
                <input type="text" wire:model.lazy="name_fa" class="form-control @error('name_fa') is-invalid @enderror" placeholder="{{ __('label.given_name') }}">
                @error('name_fa') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="form-group mb-2">
                <label>{{ __('label.family_name') }}</label>
                <input type="text" wire:model.lazy="family_name_fa" class="form-control @error('family_name_fa') is-invalid @enderror" placeholder="{{ __('label.family_name') }}">
                @error('family_name_fa') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group mb-3">
                <label>{{ __('label.education_degree') }} <span style="color:red;">*</span></label>
                <select class="form-control @error('education_degree_id') is-invalid @enderror" wire:model.lazy="education_degree_id">
                    <option value="">{{ __('label.select') }}</option>
                    @foreach($education_degrees as $degree)
                    <option value="{{ $degree->id }}">@if(App::getLocale() === 'en') {{ $degree->name_en }} @else {{ $degree->name_fa }} @endif</option>
                    @endforeach
                </select>
                @error('education_degree_id') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group mb-3">
                <label>{{ __('label.academic_rank') }} <span style="color:red;">*</span></label>
                <select class="form-control @error('academic_rank_id') is-invalid @enderror" wire:model.lazy="academic_rank_id">
                    <option value="">{{ __('label.select') }}</option>
                    @foreach($academic_ranks as $rank)
                    <option value="{{ $rank->id }}">@if(App::getLocale() === 'en') {{ $rank->name_en }} @else {{ $rank->name_fa }} @endif</option>
                    @endforeach
                </select>
                @error('academic_rank_id') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group mb-2">
                <label>{{ __('label.department') }}  <span style="color:red;">*</span></label>
                <input type="text" wire:model.lazy="department_fa" class="form-control @error('department_fa') is-invalid @enderror" placeholder="{{ __('label.department') }}">
                @error('department_fa') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group mb-2">
                <label>{{ __('label.preferred_research_area') }}  <span style="color:red;"></span></label>
                <input type="text" wire:model.lazy ="preferred_research_area_fa" class="form-control @error('preferred_research_area_fa') is-invalid @enderror" placeholder="{{ __('label.preferred_research_area') }}">
                @error('preferred_research_area_fa') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="form-group mb-2">
                <label>{{ __('label.affiliation') }}  <span style="color:red;">*</span></label>
                <input type="text" wire:model.lazy="affiliation_fa" class="form-control @error('affiliation_fa') is-invalid @enderror" placeholder="{{ __('label.affiliation') }}">
                @error('affiliation_fa') <span class="text-danger">{{ $message }}</span> @enderror
            </div>


            <div class="form-group mb-3">
                <label>{{ __('label.country') }} <span style="color:red;">*</span></label>
                <select class="form-control @error('country_id') is-invalid @enderror" id="countrySelect" wire:model.lazy="country_id">
                    <option value="">{{ __('label.select') }}</option>
                    @foreach($countries as $country)
                    <option value="{{ $country->id }}">@if(App::getLocale() === 'en') {{ $country->country_name_en }} @else {{ $country->country_name_fa }} @endif</option>
                    @endforeach
                </select>
                @error('country_id') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group mb-3">
                <label>{{ __('label.province') }} <span style="color:red;">*</span></label>
                <select class="form-control @error('province_id') is-invalid @enderror" wire:model.lazy="province_id">
                    <option value="">{{ __('label.select') }}</option>
                    @foreach($provinces as $province)
                    <option value="{{ $province->id }}">@if(App::getLocale() === 'en') {{ $province->name_en }} @else {{ $province->name_fa }} @endif</option>
                    @endforeach
                </select>
                @error('province_id') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="form-group mb-2">
                <label>{{ __('label.city') }}  <span style="color:red;"></span></label>
                <input type="text" wire:model.lazy="city_fa" class="form-control @error('city_fa') is-invalid @enderror" placeholder="{{ __('label.city') }}">
                @error('city_fa') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <h3>{{ __('label.login') }}</h3>

            <div class="form-group mb-2">
                <label>{{ __('label.email') }} <span style="color:red;">*</span></label>
                <input type="email" wire:model.lazy="email" class="form-control @error('email') is-invalid @enderror" placeholder="{{ __('label.email') }}">
                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group mb-2">
                <label>{{ __('label.phone') }} <span style="color:red;">*</span></label>
                <input type="text" wire:model.lazy="phone_no" class="form-control @error('phone_no') is-invalid @enderror" placeholder="{{ __('label.phone') }}">
                @error('phone_no') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group mb-2">
                <label>{{ __('label.user_name') }}<span style="color:red;">*</span></label>
                <input type="text" wire:model.lazy="name" class="form-control @error('name') is-invalid @enderror" placeholder="{{ __('label.user_name') }}">
                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="form-group mb-2">
                <label>{{ __('label.password') }}<span style="color:red;">*</span></label>
                <input type="password" wire:model.lazy="password" class="form-control @error('password') is-invalid @enderror">
                @error('password') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="form-group mb-2">
                <label>{{ __('label.confirm_password') }}<span style="color:red;">*</span></label>
                <input type="password" wire:model.lazy="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror">
                @error('password_confirmation') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <!-- کپچه -->
             <div class="form-group mb-2">
                <div class="form-group mb-3" wire:ignore>
                   {!! NoCaptcha::display(['data-callback' => 'recaptchaCallback']) !!}
                </div>

                <input type="hidden" wire:model="g_recaptcha_response" id="g-recaptcha-response">

                @error('g_recaptcha_response')
                    <span class="text-danger" wire:key="g_recaptcha_response_error">{{ $message }}</span>
                @enderror
               
            </div>
             <!-- کپچه -->
            <button type="submit" class="default-btn btn">{{ __('label.register') }}</button>
            <button class="default-btn btn active"><a href="{{ route('login-form') }}" class="">{{ __('label.login') }}</a></button>
            
        </form>
    </div>

</div>

<script>
function recaptchaCallback(token) {
    Livewire.dispatch('setRecaptcha', token); 
}
</script>
