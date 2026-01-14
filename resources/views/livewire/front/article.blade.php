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
   <div class="row" style="">
        <div class="col-lg-12">
            <div class="how-to-apply">
                <div class="enroll-courses" style="padding:4px;">
                    <a href="#"> @if(App::getLocale()==='en') {{ $active_menu->name_en }} @elseif(App::getLocale()==='fa') {{ $active_menu->name_fa }} @endif  <span style="color:#ddd;">/</span> 
                    <span style="color:#ddd;">{{ __('label.submission') }}</span></a>
                </div>
                <div class="how-to-apply">
                    <h4>{{ __('label.title') }}</h4>
                    <p>
                        @if(App::getLocale()==='en')
                            {{ $submission->title_en }}
                        @else
                            {{ $submission->title_fa }}
                        @endif
                    </p>
                    <div class="row">
                        @if($submission?->authors?->count() > 0)
                            @foreach($submission->authors as $author)
                                <div class="col-lg-6 col-md-6">
                                    <i class="flaticon-user" ></i>  @if(App::getLocale()==='en') {{ $author->given_name_en }} {{ $author->family_name_fa }}
                                        @else {{ $author->given_name_fa }} {{ $author->family_name_fa }} @endif 

                                    <p  style="font-size:13px;"> @if(App::getLocale()==='en') {{ $author->affiliation_en }} @if($author?->type_id==1) ( {{ $author?->type?->type_name_en }} ) @endif
                                    @else {{ $author->affiliation_fa }} @if($author?->type_id==1) ( {{ $author?->type?->type_name_fa }} ) @endif @endif</p> 
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <hr>
                    <h5>{{ __('label.abstract') }}</h5>
                    <p>@if(App::getLocale()==='en')
                            {{ $submission->abstract_en }}
                        @else
                            {!! $submission->abstract_fa !!}
                        @endif</p>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-4 pb-1">
                        @if($submission?->files?->count() > 0)
                            @foreach($submission->files as $file)
                            <a wire:click.prevent="downloadFile({{ $file->id }})"class="default-btn btn" style="padding:5px 10px;border-radius: 10px;"><i class="flaticon-pdf-file" style="font-size:20px;padding-right:0px;"></i></a>
                            @endforeach
                        @endif
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <i class="flaticon-clock" style="color:#00b2f2"></i> {{ __('label.date_published') }}: {{ $submission->published_at? verta($submission->published_at)->format('Y/m/d H:i:a'): '' }}
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 128 128">
                            <path fill="#64d465" d="M87.598 49.397h23.598v61.799H87.598z"/>
                            <path fill="#f4dd45" d="M16.803 65.694h23.598v45.502H16.803z"/>
                            <path fill="#f87c68" d="M40.402 33.1H64v78.096H40.402z"/>
                            <path fill="#7fb3fa" d="M64 16.803h23.598v94.393H64z"/>
                            <path d="M121.693 123.443H6.307a1.749 1.749 0 0 1-1.75-1.75V6.307a1.75 1.75 0 0 1 3.5 0v113.636h113.636a1.75 1.75 0 0 1 0 3.5z" fill="#477b9e"/>
                        </svg>
                        {{ __('label.number_of_read') }}: {{ $submission->views }}
                    </div>
                </div>
                <h4 class="mt-5">{{ __('label.keywords') }}</h4>
                <p class="card-text">
                    @if(App::getLocale() == 'en')
                        @foreach($keywords_en as $i => $keyword)
                           <a href="#" class="default-btn btn" style="padding:1px 10px;border-radius: 10px;background-color:gray;border-color:gray;">{{ $keyword }}</a>
                        @endforeach
                    @else
                        @foreach($keywords_fa as $i => $keyword)
                        <a href="#" class="default-btn btn" style="padding:1px 10px;border-radius: 10px;background-color:gray;border-color:gray;">{{ $keyword }}</a>
                        @endforeach
                    @endif
                </p>
            </div>
        </div>
    </div>

    <button type="button" class="default-btn btn mt-5">
        <a href="{{ route('page',['menu_id' =>1, 'slug' =>'articles'])}}" style="color:white;">
            {{ __('label.all_articles') }}<i class="flaticon-next"></i>
        </a>
    </button>
    <br>
    @if(count($similar_articles) > 0)
    <div style="" class="mt-5">
        <h4>{{ __('label.similar_articles') }}</h4>
        <div class="madical-care-content">
            <div class="row align-items-center">
                <div class="medical-care">
                    <div class="list">
                        <ul style="padding:2px 10px;">
                            @foreach($similar_articles as $similar)
                            <li>
                                <a href="{{ route('article',['menu_id' =>$active_menu_id,'submission_id'=>$similar->id]) }}"><strong> @if(App::getLocale()=='en') {{ $similar->title_en }} @else {{ $similar->title_fa }} @endif</strong></a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
