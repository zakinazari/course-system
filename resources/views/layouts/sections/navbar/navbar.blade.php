@php
$containerNav = $containerNav ?? 'container-fluid';
@endphp
<?php $lang = App::getLocale();?>
<!-- Navbar -->
<nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
  <div class="{{$containerNav}}">

    <!--  Brand demo (display only for navbar-full and hide on below xl) -->
    @if(isset($navbarFull))
    <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
      <a href="{{url('/')}}" class="app-brand-link gap-2">
        <span class="app-brand-logo demo">
         <img src="" alt="Avatar" class="rounded-circle me-3" width="54">
        </span>
        <span class="app-brand-text demo menu-text fw-bold">{{ __('label.app_name') }}</span>
      </a>

      @if(isset($menuHorizontal))
      <!-- Display menu close icon only for horizontal-menu with navbar-full -->
      <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
        <i class="bx bx-x bx-sm align-middle"></i>
      </a>
      @endif
    </div>
    @endif

    <!-- ! Not required for layout-without-menu -->
    @if(!isset($navbarHideToggle))
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0{{ isset($menuHorizontal) ? ' d-xl-none ' : '' }} {{ isset($contentNavbar) ?' d-xl-none ' : '' }}">
      <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
        <i class="bx bx-menu bx-sm"></i>
      </a>
    </div>
    @endif

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

      @if(!isset($menuHorizontal))
      <!-- Search -->
      <!-- <div class="navbar-nav align-items-center">
        <div class="nav-item navbar-search-wrapper mb-0">
          <a class="nav-item nav-link search-toggler px-0" href="javascript:void(0);">
            <i class="bx bx-search-alt bx-sm"></i>
            <span class="d-none d-md-inline-block text-muted">{{ __('label.search') }} (Ctrl+/)</span>
          </a>
        </div>
      </div> -->
      <!-- /Search -->
      @endif

      <ul class="navbar-nav flex-row align-items-center ms-auto">
            <!-- Language -->
            <li class="nav-item dropdown-language dropdown me-2 me-xl-0">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <i class="bx bx-globe bx-sm"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <a class="dropdown-item @if(App::getLocale()==='fa') active @endif" href="{{ route('locale.admin', 'fa') }}">
                    <!-- <i class="fi fi-af fis rounded-circle fs-4 me-1"></i> -->
                    <span class="align-middle">{{ __('label.fa') }}</span>
                </a>
            </li>
            <li>
                <!-- <a class="dropdown-item @if(App::getLocale()==='pa') active @endif" href="{{ route('locale.admin', 'pa') }}">
                    <span class="align-middle">{{ __('label.pa') }}</span>
                </a> -->
            </li>
            <li>
                <a class="dropdown-item @if(App::getLocale()==='en') active @endif" href="{{ route('locale.admin', 'en') }}">
                    <!-- <i class="fi fi-us fis rounded-circle fs-4 me-1"></i> -->
                    <span class="align-middle">{{ __('label.en') }}</span>
                </a>
            </li>
        </ul>
    </li>
        <!--/ Language -->
        <li class="nav-item dropdown-style-switcher dropdown me-2 me-xl-0">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
            @if(session('myStyle')==='dark')
            <i class="bx bx-moon me-2"></i>
            @else
            <i class="bx bx-sun me-2"></i>
            @endif
            </a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
              <li>
                  <a class="dropdown-item"  href="{{ route('set-theme', 'light') }}" data-theme="light">
                  <span class="align-middle"><i class="bx bx-sun me-2"></i>{{ __('label.light') }}</span>
                  </a>
              </li>
              <li>
                  <a class="dropdown-item"  href="{{ route('set-theme', 'dark') }}" data-theme="dark">
                  <span class="align-middle"><i class="bx bx-moon me-2"></i>{{ __('label.dark') }}</span>
                  </a>
              </li>
            </ul>
        </li>
        @if(isset($menuHorizontal))
        <!-- Search -->
        <li class="nav-item navbar-search-wrapper me-2 me-xl-0">
          <a class="nav-item nav-link search-toggler" href="javascript:void(0);">
            <i class="bx bx-search bx-sm"></i>
          </a>
        </li>
        <!-- /Search -->
        @endif


        <!-- Style Switcher -->
        <li class="nav-item me-2 me-xl-0">
          <a class="nav-link style-switcher-toggle hide-arrow" href="javascript:void(0);">
            <i class='bx bx-sm'></i>
          </a>
        </li>
        <!--/ Style Switcher -->

        <!-- Quick links  -->
        <!-- <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown me-2 me-xl-0">
          <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
            <i class='bx bx-grid-alt bx-sm'></i>
          </a>
          <div class="dropdown-menu dropdown-menu-end py-0">
            <div class="dropdown-menu-header border-bottom">
              <div class="dropdown-header d-flex align-items-center py-3">
                <h5 class="text-body mb-0 me-auto">Shortcuts</h5>
                <a href="javascript:void(0)" class="dropdown-shortcuts-add text-body" data-bs-toggle="tooltip" data-bs-placement="top" title="Add shortcuts"><i class="bx bx-sm bx-plus-circle"></i></a>
              </div>
            </div>
            <div class="dropdown-shortcuts-list scrollable-container">
              <div class="row row-bordered overflow-visible g-0">
                <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                    <i class="bx bx-calendar fs-4"></i>
                  </span>
                  <a href="{{url('app/calendar')}}" class="stretched-link">Calendar</a>
                  <small class="text-muted mb-0">Appointments</small>
                </div>
                <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                    <i class="bx bx-food-menu fs-4"></i>
                  </span>
                  <a href="{{url('app/invoice/list')}}" class="stretched-link">Invoice App</a>
                  <small class="text-muted mb-0">Manage Accounts</small>
                </div>
              </div>
              <div class="row row-bordered overflow-visible g-0">
                <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                    <i class="bx bx-user fs-4"></i>
                  </span>
                  <a href="{{url('app/user/list')}}" class="stretched-link">User App</a>
                  <small class="text-muted mb-0">Manage Users</small>
                </div>
                <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                    <i class="bx bx-check-shield fs-4"></i>
                  </span>
                  <a href="{{url('app/access-roles')}}" class="stretched-link">Role Management</a>
                  <small class="text-muted mb-0">Permission</small>
                </div>
              </div>
              <div class="row row-bordered overflow-visible g-0">
                <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                    <i class="bx bx-pie-chart-alt-2 fs-4"></i>
                  </span>
                  <a href="{{url('/')}}" class="stretched-link">Dashboard</a>
                  <small class="text-muted mb-0">User Profile</small>
                </div>
                <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                    <i class="bx bx-cog fs-4"></i>
                  </span>
                  <a href="{{url('pages/account-settings-account')}}" class="stretched-link">Setting</a>
                  <small class="text-muted mb-0">Account Settings</small>
                </div>
              </div>
              <div class="row row-bordered overflow-visible g-0">
                <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                    <i class="bx bx-help-circle fs-4"></i>
                  </span>
                  <a href="{{url('pages/help-center-landing')}}" class="stretched-link">Help Center</a>
                  <small class="text-muted mb-0">FAQs & Articles</small>
                </div>
                <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                    <i class="bx bx-window-open fs-4"></i>
                  </span>
                  <a href="{{url('modal-examples')}}" class="stretched-link">Modals</a>
                  <small class="text-muted mb-0">Useful Popups</small>
                </div>
              </div>
            </div>
          </div>
        </li> -->
        <!-- Quick links -->

        <!-- Notification -->
      <livewire:notifications-dropdown />
        <!--/ Notification -->

        <!-- User -->
        <li class="nav-item navbar-dropdown dropdown-user dropdown">
          <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
            <div class="avatar avatar-online">
              <img src="{{ asset('storage/'.Auth::user()->profile_photo) }}" alt class="rounded-circle">
            </div>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <a class="dropdown-item" href="{{ route('my-account') }}">
                <div class="d-flex">
                  <div class="flex-shrink-0 me-3">
                    <div class="avatar avatar-online">
                      <img src="{{ asset('storage/'.Auth::user()->profile_photo) }}" alt class="rounded-circle">
                    </div>
                  </div>
                  <div class="flex-grow-1">
                    <span class="fw-semibold d-block">
                      @if (Auth::check())
                      {{ Auth::user()->name }}
                      @else
                      John Doe
                      @endif
                    </span>
                    <small class="text-muted"></small>
                  </div>
                </div>
              </a>
            </li>
            <li>
              <div class="dropdown-divider"></div>
            </li>
            <!-- <li>
              <a class="dropdown-item"  href="{{ route('my-account') }}">
                <i class="bx bx-user me-2"></i>
                <span class="align-middle">{{__('label.my_account')}}</span>
              </a>
            </li> -->
            
            @if (Auth::check())
            <li>
              <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class='bx bx-power-off me-2'></i>
                <span class="align-middle"> {{ __('logout') }}</span>
              </a>
            </li>
            <form method="POST" id="logout-form" action="{{ route('logout') }}">
              @csrf
            </form>
            @else
            <li>
              <a class="dropdown-item" href="{{ Route::has('login') ? route('login') : 'javascript:void(0)' }}">
                <i class='bx bx-log-in me-2'></i>
                <span class="align-middle">Login</span>
              </a>
            </li>
            @endif
          </ul>
        </li>
        <!--/ User -->
      </ul>
    </div>

    <!-- Search Small Screens -->
    <div class="navbar-search-wrapper search-input-wrapper {{ isset($menuHorizontal) ? $containerNav : '' }} d-none">
      <input type="text" class="form-control search-input {{ isset($menuHorizontal) ? '' : $containerNav }} border-0" placeholder="Search..." aria-label="Search...">
      <i class="bx bx-x bx-sm search-toggler cursor-pointer"></i>
    </div>
  </div>
</nav>
<!-- / Navbar -->