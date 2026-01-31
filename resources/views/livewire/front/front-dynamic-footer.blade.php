<div>
    <div class="footer-area pt-100 pb-70">
        <div class="container">
            <div class="row">
                @foreach($about_us as $about)
                <div class="col-lg-5 col-sm-6">
                    <div class="footer-logo-area">
                        <a href="#"><img src="{{ asset('storage/' . $about->logo) }}" style="width:70px;" alt="Image"></a>
                        <p> {{ App::getLocale()=='fa'? $about->title_fa: $about->title_en }} </p>
                        <div class="contact-list">
                            <ul>
                                <li><a href="#"> {{ __('label.email') }}: {{ $about->email }}</a></li>
                                <li><a href="#"> {{ __('label.website') }}: {{ $about->website }}</a></li>
                                <li><a href="#">{{ __('label.facebook') }}: {{ $about->facebook }}</a></li>
                                <li><a href="#"> {{ __('label.phone') }}: {{ $about->phone }}</a></li>
                                <li><a href="#"> {{ __('label.address') }}: {!! App::getLocale()=='fa'? $about->address_fa: $about->address_en !!}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                @endforeach

                <div class="col-lg-2 col-sm-6">
                    <div class="footer-widjet">
                        @foreach($menus as $m)
                        <h3>
                            @if(App::getLocale() =='fa')
                                {{ $m->name_fa }}
                            @else
                                {{ $m->name_en }}
                            @endif
                        </h3>
                        <div class="list">
                            <ul>
                                @foreach($m->subMenu as $sm)
                                <li><a href="{{ $sm->subMenuSub?->count() > 0 ? '#' : route('page', ['menu_id' => $sm->id]) }}">
                                    @if(App::getLocale()==='fa')
                                        {{ $sm->name_fa }}
                                    @else
                                        {{ $sm->name_en }}
                                    @endif
                                </a></li>
                                @endforeach
                            </ul>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="shape">
                <img src="{{ asset('front-assets/images/shape-1.png') }}" alt="Image">
            </div>
        </div>
    </div>

    <div class="copyright-area">
        <div class="container">
            <div class="copyright">
                <div class="row">
                    <div class="col-lg-6 col-md-4">
                        <div class="social-content">
                            <ul>
                                <li><span>{{ __('label.follow_us_on') }}</span></li>
                                <li>
                                    <a href="https://www.facebook.com" target="_blank"><i class="ri-facebook-fill"></i></a>
                                </li>
                                <li>
                                    <a href="https://www.twitter.com" target="_blank"><i class="ri-twitter-fill"></i></a>
                                </li>
                                <li>
                                    <a href="https://instagram.com/?lang=en" target="_blank"><i class="ri-instagram-line"></i></a>
                                </li>
                                <li>
                                    <a href="https://linkedin.com/?lang=en" target="_blank"><i class="ri-linkedin-fill"></i></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-8">
                        <div class="copy">
                           <p>{{__('label.all_rights_reserved')}} © <script> 
                                    document.write(new Date().getFullYear())

                            </script></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
