<div>
   
        <div class="desktop-nav">
            <div class="container-fluid">
                <nav class="navbar navbar-expand-md navbar-light">
                    <!-- <a class="navbar-brand" href="{{ url('/') }}" style="color:white;"> تحقیقات علمی
                        <img src="{{ asset('front-assets/images/favicon.png') }}" alt="logo">
                    </a> -->
                    <div class="collapse navbar-collapse mean-menu" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto">
                            @foreach($menus as $m)
                            <li class="nav-item {{ $active_menu_id == $m->id ? 'active' : '' }}">
                                <a href="{{ $m->subMenu?->count() > 0 ? '#' : route('page', ['menu_id' => $m->id]) }}" class="nav-link @if($m->subMenu?->count()>0) dropdown-toggle @endif"> 
                                    @if(App::getLocale()==='fa') {{ $m->name_fa }}  @else {{ $m->name_en }}  @endif
                                </a>
                                @if($m->subMenu?->count()>0)
                                <ul class="dropdown-menu">
                                    @foreach($m->subMenu as $sm)
                                    <li class="nav-item">
                                        <a href="{{ $sm->subMenuSub?->count() > 0 ? '#' : route('page', ['menu_id' => $sm->id]) }}" class="nav-link {{ $sm->subMenuSub?->count() > 0 ? 'dropdown-toggle' : '' }}  {{ $active_sub_menu_id == $sm->id ? 'active' : '' }}">
                                            @if(App::getLocale()==='fa') {{ $sm->name_fa }}  @else {{ $sm->name_en }}  @endif
                                        </a>
                                        @if($sm->subMenuSub?->count()>0)
                                        <ul class="dropdown-menu">
                                            @foreach($sm->subMenuSub as $sms)
                                            <li class="nav-item">
                                                <a href="{{ route('page', ['menu_id' => $sms->id]) }}" class="nav-link {{ $active_sub_menu_sub_id == $sms->id ? 'active' : '' }}">
                                                    @if(App::getLocale()==='fa') {{ $sms->name_fa }}  @else {{ $sms->name_en }}  @endif
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
                        </ul>
                        <!-- <div class="others-options">
                            <div class="icon">
                                <i class="ri-menu-3-fill" data-bs-toggle="modal" data-bs-target="#sidebarModal"></i>
                            </div>
                        </div> -->
                    </div>
                </nav>
            </div>
        </div>
        <!-- <div class="others-option-for-responsive">
            <div class="container">
                <div class="dot-menu">
                    <div class="inner">
                        <div class="icon">
                            <i class="ri-menu-3-fill" data-bs-toggle="modal" data-bs-target="#sidebarModal"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
    </div>
</div>
            
            