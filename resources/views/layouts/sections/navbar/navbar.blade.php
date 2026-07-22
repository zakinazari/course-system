@php
$containerNav = $containerNav ?? 'container-fluid';
@endphp
<?php $lang = App::getLocale();?>
<!-- Navbar -->
<nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme {{$navbarFixed}}" id="layout-navbar">
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

      <style>
        .navbar-search-box {
            width: clamp(170px, 40vw, 400px);
        }
      </style>
      <!-- Search -->
          <div class="navbar-search position-relative d-flex align-items-center">

              <div class="input-group navbar-search-box">
                  <span class="input-group-text">
                      <i class="bx bx-search"></i>
                  </span>

                  <input type="text"
                        id="navbar_student_search_input"
                        class="form-control"
                        placeholder="Search student...">
              </div>

              <div id="navbar_student_results"
                  class="list-group position-absolute w-100"
                  style="
                      z-index: 99999;
                      top: 100%;
                      left: 0;
                      display: none;
                      max-height: 300px;
                      overflow-y: auto;
                      background: #fff;
                      border: 1px solid #ddd;
                      box-shadow: 0 6px 20px rgba(0,0,0,0.15);
                  ">
              </div>

          </div>
      <!-- /Search -->
      @endif

      
      <ul class="navbar-nav flex-row align-items-center ms-auto">
      {{--
            <!-- Language -->
            <li class="nav-item dropdown-language dropdown me-2 me-xl-0">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
              <i class="bx bx-globe bx-sm"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <!-- <li>
                    <a class="dropdown-item @if(App::getLocale()==='fa') active @endif" href="{{ route('locale.admin', 'fa') }}">
                        <i class="fi fi-af fis rounded-circle fs-4 me-1"></i>
                        <span class="align-middle">{{ __('label.fa') }}</span>
                    </a>
                </li> -->
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
      --}}
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
                          <span class="fs-6 fw-bold">
                              {{ Auth::user()->name }}
                          </span>

                          <span class="d-block text-muted" style="font-size: 12px;">
                              {{ Auth::user()?->role?->role_name }}
                          </span>
                      @else
                          <span class="fs-6 fw-bold">John Doe</span>

                          <span class="d-block text-muted" style="font-size: 12px;">
                              Administrator
                          </span>
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
            <li>
              <a class="dropdown-item"  href="{{ route('my-account') }}">
                <i class="bx bx-user me-2"></i>
                <span class="align-middle">{{__('label.my_account')}}</span>
              </a>
            </li>
            
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


<script>
document.addEventListener('DOMContentLoaded', function () {

    const input = document.getElementById('navbar_student_search_input');
    const box = document.getElementById('navbar_student_results');

    let timeout = null;

    if (!input || !box) return;

    // hide dropdown
    function hideBox() {
        box.style.display = 'none';
        box.innerHTML = '';
    }

    // render results
    function render(data) {

        box.innerHTML = '';

        if (!data.length) {
            hideBox();
            return;
        }

        data.forEach(item => {

            let el = document.createElement('a');
            el.href = "#";
            el.className = "list-group-item list-group-item-action";
            el.textContent = item.text;

            el.addEventListener('click', function (e) {
            e.preventDefault();

            let menuId = 34;
            let studentId = item.id;

            window.location.href =
                `/students/${menuId}?student_id=${studentId}`;
        });

            box.appendChild(el);
        });

        box.style.display = 'block';
    }

    // search input
    input.addEventListener('keyup', function () {

        clearTimeout(timeout);

        let q = this.value.trim();

        if (q.length < 2) {
            hideBox();
            return;
        }

        timeout = setTimeout(() => {

            fetch(`/search-students?q=${encodeURIComponent(q)}`)
                .then(res => res.json())
                .then(data => render(data))
                .catch(err => {
                    console.error(err);
                    hideBox();
                });

        }, 300);
    });

    // click outside close
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.navbar-search')) {
            hideBox();
        }
    });

});

window.addEventListener('replace-url', function (event) {

    let menuId = event.detail.menuId;

    window.history.replaceState(
        {},
        '',
        `/students/${menuId}`
    );

});
</script>
