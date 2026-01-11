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
        <a href="{{ url('/') }}">{{ __('label.main_page') }}<span style="color:#999 !important;"> / {{ __('label.login') }}</span></a>
    </div>
    <h3>{{ __('label.user_login') }}</h3>

      @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group mb-3">
            <input type="text" name="user_name" id="user_name" class="form-control" placeholder="{{ __('label.email_or_phone_no') }} * " required autofocus>
        </div>

        <div class="form-group mb-3">
            <input type="password" name="password" id="password" class="form-control" placeholder="{{ __('label.password') }} *" required>
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="remember" id="flexCheckDefault">
            <label class="form-check-label" for="flexCheckDefault">{{ __('label.remember_me') }}</label>
        </div>

        <button type="submit" class="default-btn btn">{{ __('label.login') }}</button>
        <button class="default-btn btn active"><a href="{{ route('registration-form') }}" class="">{{ __('label.register') }}</a></button>
        @if(Route::has('password.request'))
            <div class="mt-2">
                <!-- <a href="{{ route('password.request') }}">{{ __('label.forgot_password') }}</a> -->
            </div>
        @endif
    </form>
</div>

</div>
