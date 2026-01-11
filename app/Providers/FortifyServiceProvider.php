<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class FortifyServiceProvider extends ServiceProvider
{
    
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // start made by Zaki
    
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;
            return Limit::perMinute(5)->by($email.$request->ip());
        });

        Fortify::authenticateUsing(function (Request $request) {
            $login = $request->input('user_name');
            $password = $request->input('password');

            $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_no';
            $user = User::where($field, $login)->first();

            if ($user && Hash::check($password, $user->password)) {
                return $user;
            }

            return null;
        });
        // end made by Zaki

        Fortify::twoFactorChallengeView(fn () => view('livewire.auth.two-factor-challenge'));
        Fortify::confirmPasswordView(fn () => view('livewire.auth.confirm-password'));

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

    }

}
