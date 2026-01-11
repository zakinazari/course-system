<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    @include('front-layouts.front-header')
    <title>@yield('title') </title>
</head>

<body>
    
    <!-- first header -->
        <div class="preloader-area">
            <div class="spinner">
                <div class="inner">
                    <div class="disc"></div>
                    <div class="disc"></div>
                    <div class="disc"></div>
                </div>
            </div>
        </div>
        <div class="top-header-area container my-page-banner-area bg-2">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-lg-12 col-md-12">
                        <div class="header-right-content">
                            <div class="page-banner-content"  style="margin-top:-50px;margin-right:10px;">
                            <ul>
                                @if(App::getLocale() ==='fa')
                                <li><a href="{{ route('locale.frontend', 'pa') }}">
                                     <i class="fi fi-us fis rounded-circle fs-4 me-1"></i>
                                    {{ __('label.pa') }}
                                </a></li>
                                @else
                                    <li><a href="{{ route('locale.frontend', 'fa') }}">
                                    <i class="fi fi-af fis rounded-circle fs-4 me-1"></i>
                                    {{ __('label.fa') }}
                                </a></li>
                                @endif
                                <li><a href="{{ route('registration-form') }}">{{ __('label.register') }}</a></li>
                                @auth
                                    <li><a href="{{ route('dashboard') }}">{{ __('label.dashboard') }}</a></li>
                                @else
                                    <li><a href="{{ route('login-form') }}">{{ __('label.login') }}</a></li>
                                @endauth
                            </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12" style="text-align:center;">
                        <!-- <div class="row">
                            <div class="col-lg-2">
                                <img src="{{ asset('front-assets/images/imarat.png') }}" alt="Image" style="width:140px;margin-top:-20px;">
                            </div>
                            <div class="col-lg-8">
                                <p class="header-p" style="font-weight:bold;margin-top:-20px;">د امیرالمؤمنین حفظه الله تعالی و رعاه د فرمانونو داخلي کنفرانس</p>
                                <p class="header-p " style="font-weight:bold">کنفرانس داخلي فرامین امیرالمؤمنین حفظه الله تعالی و رعاه</p>
                            </div>
                            <div class="col-lg-2">
                                <img src="{{ asset('front-assets/images/logo_mis.png') }}" alt="Image" style="width:145px;margin-top:-25px;">
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
        <!-- end first header -->
    <!-- end first header -->
     
    <!-- navbar start -->
      <div class="navbar-area nav-bg-1 container">
        <div class="mobile-responsive-nav">
            <div class="container">
                <div class="mobile-responsive-menu">
                    <div class="logo">
                        <a href="{{ url('/') }}">
                            <img src="{{ asset('favicon.png') }}" style="width:27%;" class="main-logo" alt="logo">
                            <img src="{{ asset('favicon.png') }}" style="width:27%;" class="white-logo" alt="logo">
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @livewire('front.front-navbar')

    <!-- END NAVBAR-->

    @hasSection('content-wrapper')

    @yield('content-wrapper')
    
    @else
        <div class="graduate-admission pt-100 pb-70" style="padding-top: 27px !important;">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8" style="box-shadow:1px 1px 20px 3px rgba(0, 0, 0, 0.05)">
                        <div class="how-to-apply" style="padding:35px !important;">
                            @yield('content')
                        </div>
                    </div>
                    <div class="col-lg-4" @if(App::getLocale() ==='en') style="padding-right:0px;" @else style="padding-left:0px;" @endif>
                        @livewire('front.front-sidebar')
                    </div>
                </div>
            </div>
        </div>
    @endif
    @include('front-layouts.front-footer')
{!! NoCaptcha::renderJs() !!}
</body>
</html>