<!DOCTYPE html>

<html lang="{{ App::getLocale() }}" class="{{ $configData['style'] }}-style {{ $navbarFixed ?? '' }} {{ $menuFixed ?? '' }} {{ $menuCollapsed ?? '' }} {{ $footerFixed ?? '' }} {{ $customizerHidden ?? '' }}" dir="<?php if(App::getLocale()=='en'){ echo 'ltr'; }else{ echo 'rtl';} ?>" data-theme="{{ (($configData['theme'] === 'theme-semi-dark') ? (($configData['layout'] !== 'horizontal') ? $configData['theme'] : 'theme-default') :  $configData['theme']) }}" data-assets-path="{{ asset('/assets') . '/' }}" data-base-url="{{url('/')}}" data-framework="laravel" data-template="{{ $configData['layout'] . '-menu-' . $configData['theme'] . '-' . $configData['style'] }}">
  
  <head>
    <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>@yield('title') 
    {{ config('variables.templateName') ? config('variables.templateName') : '' }}
    {{ config('variables.templateSuffix') ? config('variables.templateSuffix') : '' }}</title>
    <meta name="description" content="{{ config('variables.templateDescription') ? config('variables.templateDescription') : '' }}" />
  <meta name="keywords" content="{{ config('variables.templateKeyword') ? config('variables.templateKeyword') : '' }}">
  <!-- laravel CRUD token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Canonical SEO -->
  <link rel="canonical" href="{{ config('variables.productPage') ? config('variables.productPage') : '' }}">
  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="{{ asset('favicon.png') }}" />
  @livewireStyles

  <!-- <script defer  src="{{ asset('assets/js/alpine.js')}}"></script> -->
  @yield('style-before-livewire')
  
  
  <!-- Include Styles -->
  @include('layouts/sections/styles')
  
  <!-- Include Scripts for customizer, helper, analytics, config -->

  @include('layouts/sections/scriptsIncludes')
</head>

</style>
<body>
  
  <!-- Layout Content -->
  @yield('layoutContent')
  <!--/ Layout Content -->

  

  <!-- Include Scripts -->
  @include('layouts/sections/scripts')

  @yield('javascript')

   @livewireScripts
  @yield('scripts-after-livewire')

<script>
    // پیام‌های عمومی
    const messages = {
        delete_title: "{{ __('label.delete_title') }}",
        delete_text:  "{{ __('label.delete_text') }}",
        confirm_text: "{{ __('label.confirm_text') }}",
        cancel_text:  "{{ __('label.cancel_text') }}"
    };

    // تابع حذف عمومی
    function confirmDelete(id, table) {
        Swal.fire({
            title: messages.delete_title,
            text: messages.delete_text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: messages.confirm_text,
            cancelButtonText: messages.cancel_text
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.dispatch('globalDelete', [ { id: id, table: table } ]);
            }
        });
    }

    // هنگام لود Livewire
    document.addEventListener('livewire:load', function () {
       
        document.querySelectorAll('.modal').forEach(modalEl => {
            modalEl.addEventListener('hidden.bs.modal', function () {
                const id = modalEl.id; 
                Livewire.dispatch('modalClosed', { id }); 
            });
        });
    });

        // باز کردن modal با id دینامیک
        window.addEventListener('open-modal', event => {
        const id = event.detail.id;
        if (id) {
            const modalElement = document.getElementById(id);
            if (!modalElement) return;

            // اگر مودال از قبل instance دارد، همان را استفاده کن
            let modal = bootstrap.Modal.getInstance(modalElement);
            if (!modal) {
                modal = new bootstrap.Modal(modalElement, {
                    backdrop: 'static', // جلوگیری از تکرار بک‌دراپ
                    keyboard: false
                });
            }

            modal.show();
        }
    });

    window.addEventListener('close-modal', event => {
        const id = event.detail?.id;
        if (id) {
            const modalElement = document.getElementById(id);
            if (!modalElement) return;

            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
        }

        // پاک کردن بک‌دراپ باقی‌مانده در هر حال
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(b => b.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('padding-right');
    });

    // alert عمومی
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('alert', (event) => {
            const { type, message } = event;
            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: 'toast-top-right',
                timeOut: 3000
            };
            toastr[type ?? 'info'](message ?? 'No message provided!');
        });
    });
</script>

</body>

</html>
