<div>
    <style>
        #layout-menu {
            height: 100vh;
        }

        #layout-menu .menu-inner {
            height: calc(100vh - 64px);
            overflow: hidden; 
        }
    </style>
    <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme" >
    <div class="app-brand demo">
      <a href="{{ url('/') }}" class="app-brand-link">
        <img src="{{ asset('front-assets/images/imarat.png') }}" alt="Image" style="width:50px;">
        <span class="app-brand-text demo menu-text fw-bold ms-2" style="font-size:15px;">{{ __('label.app_name') }}</span>
        
      </a>
      <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
        <i class="bx menu-toggle-icon d-none d-xl-block fs-4 align-middle"></i>
        <i class="bx bx-x d-block d-xl-none bx-sm align-middle"></i>
      </a>
    </div>

    <div class="menu-divider mt-0"></div>

    <div class="menu-inner-shadow"></div>
    <ul class="menu-inner py-1">
        @foreach($menu_section as $section)
            @if($section->id !=1 && $section?->menu?->count() > 0)
            <li class="menu-header small text-uppercase">
              <span class="menu-header-text" data-i18n="Components"> {{ $section->section_name }} </span>
            </li>
            @endif
            @foreach($section->menu as $m)

               @php
  
                  $roleIds = Auth::user()->role_ids;

                  $sub_menu_count = $m->subMenu->filter(function($sub) use ($roleIds) {
                      return $sub->permission->contains(function($p) use ($roleIds) {
                          return in_array($p->role_id, $roleIds) && $p->action_id == 1;
                      });
                  })->count();

                  // محاسبه‌ی sub-sub menu
                  $sub_sub_menu_count = $m->subMenu->sum(function($sub) use ($roleIds) {
                      return $sub->subMenuSub->filter(function($subSub) use ($roleIds) {
                          return $subSub->permission->contains(function($p) use ($roleIds) {
                              return in_array($p->role_id, $roleIds) && $p->action_id == 1;
                          });
                      })->count();
                  });
                  $total_sub_count = $sub_menu_count + $sub_sub_menu_count;
              @endphp

                
                <li class="menu-item {{ $active_menu_id == $m->id ? 'active open' : '' }}">
                    <a href="{{ ($m->url && Route::has($m->url)) ? route($m->url, ['menu_id' => $m->id]) : 'javascript:void(0);' }}" 
                      class="menu-link @if(count($m?->subMenu) > 0) menu-toggle @endif">
                        <i class="{{ $m->icon }}"></i>
                        <div data-i18n="Dashboards"> {{ $m->name }}</div>
                        @if($active_menu_id == $m->id && $total_sub_count > 0)
                        <div class="badge bg-primary rounded-pill ms-auto"> {{ $total_sub_count }}</div>
                        @endif
                    </a>
                    @if($m->subMenu?->count()>0)
                    <ul class="menu-sub">
                        @foreach($m->subMenu as $sm)
                          
                            <li class="menu-item {{ $active_sub_menu_id == $sm->id ? 'active open' : '' }}">
                                <a href="{{ ($sm->url && Route::has($sm->url)) ? route($sm->url, ['menu_id' => $sm->id]) : 'javascript:void(0);' }}" 
                                  class="menu-link @if(count($sm?->subMenuSub) > 0) menu-toggle @endif">
                                    <div data-i18n="View">{{ $sm->name }}</div>
                                </a>
                              @if($sm->subMenuSub?->count()>0)
                                <ul class="menu-sub">
                                    @foreach($sm->subMenuSub as $sms)
                                      
                                        <li class="menu-item {{ $active_sub_menu_sub_id == $sms->id ? 'active' : '' }}">
                                            <a href="{{ ($sms->url && Route::has($sms->url)) ? route($sms->url, ['menu_id' => $sms->id]) : 'javascript:void(0);' }}" 
                                              class="menu-link">
                                                <div data-i18n="Account"> {{ $sms->name }}</div>
                                            </a>
                                        </li>
                                     
                                    @endforeach
                                </ul>
                                @endif
                            </li>
                   
                        @endforeach
                    </ul>
                    @endif
                </li>
            @endforeach
        
          @endforeach
    </ul>
  </aside>

</div>
@script
<script>
document.addEventListener('DOMContentLoaded', function () {
    const menuInner = document.querySelector('#layout-menu .menu-inner');
    if (!menuInner || typeof PerfectScrollbar === 'undefined') return;

    if (menuInner._ps) {
        menuInner._ps.destroy();
    }


    menuInner._ps = new PerfectScrollbar(menuInner, {
        wheelSpeed: 1.5,
        suppressScrollX: true,
        swipeEasing: true,
        wheelPropagation: false
    });
});
</script>
@endscript


