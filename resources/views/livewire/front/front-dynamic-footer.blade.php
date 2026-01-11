<div>
    <div class="footer-area pt-100 pb-70 ">
        <div class="container">
            <div class="row">
                <div class="col-lg-5 col-sm-6">
                    <div class="footer-logo-area">
                        <a href="#"><img src="{{ asset('front-assets/images/logo_mis.png') }}" style="width:70px;" alt="Image"></a>
                        <p>{{ __('label.wardak_university.name') }}</p>
                        <div class="contact-list">
                            <ul>
                                <li><a href="#"> {{ __('label.wardak_university.email') }}</a></li>
                                <li><a href="#"> {{ __('label.wardak_university.website') }}</a></li>
                                <li><a href="#">{{ __('label.wardak_university.facebook') }}</a></li>
                                <li><a href="#"> {{ __('label.wardak_university.contact') }}</a></li>
                                <li><a href="#"> {{ __('label.wardak_university.address') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 col-sm-6">
                    <div class="footer-logo-area">
                        <a href="#"><img src="{{ asset('favicon.png') }}" style="width:70px;" alt="Image"></a>
                        <p>{{ __('label.national_conference.name') }}</p>
                        <div class="contact-list">
                            <ul>
                                <li><a href="#">{{ __('label.national_conference.email') }}</a></li>
                                <li><a href="#">{{ __('label.national_conference.website') }}</a></li>
                                <li><a href="#"> {{ __('label.national_conference.facebook') }}</a></li>
                                <li><a href="#">{{ __('label.national_conference.contact') }}</a></li>
                                <li><a href="#"> {{ __('label.national_conference.address') }} </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
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
