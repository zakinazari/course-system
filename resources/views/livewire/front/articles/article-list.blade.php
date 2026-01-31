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
    <div class="search-content">
    <form wire:submit.prevent="applySearch" class="row g-3 align-items-end">
        <h3>{{ __('label.published_articles') }}</h3>

        <div class="form-group">
            <div class="row">
                <div class="category-content col-md-6">
                    <h6>{{ __('label.main_axes') }}</h6>
                    <select class="form-select form-control" aria-label="Default select example"
                    wire:model.live="search.main_axis_id"
                    wire:change="loadSubAxes($event.target.value)">
                        <option value="">{{ __('label.all') }}</option>
                        @foreach($main_axes as $main)
                            <option value="{{ $main->id }}"> 
                                @if(App::getLocale()=='fa')
                                    {{ substr($main->title_fa, 0, 200) }}{{ strlen($main->title_en) > 200 ? '…' : '' }}
                                @else
                                    {{ substr($main->title_en, 0, 200) }}{{ strlen($main->title_en) > 200 ? '…' : '' }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="category-content col-md-6">
                    <h6>{{ __('label.sub_axes') }}</h6>
                    <select class="form-select form-control" aria-label="Default select example"
                     wire:model.live="search.sub_axis_id">
                        <option value="">{{ __('label.all') }}</option>
                        @foreach($sub_axes as $sub)
                            <option value="{{ $sub->id }}"> 
                                @if(App::getLocale()=='fa')
                                    {{ substr($sub->title_fa, 0, 200) }}{{ strlen($sub->title_en) > 200 ? '…' : '' }}
                                @else
                                    {{ substr($sub->title_en, 0, 200) }}{{ strlen($sub->title_en) > 200 ? '…' : '' }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="row">
                <div class="input-group category-content">
                    <select class="form-select form-select-sm flex-grow-0" style="width: 120px;" wire:model.live="search.type">
                        <option value="title">{{ __('label.title') }}</option>
                        <option value="author">{{ __('label.author') }}</option>
                    </select>

                
                    <input type="text" class="form-control form-control-sm"
                    placeholder="{{ $search['type'] === 'title' ? __('label.search_by_title') : __('label.search_by_author') }}"
                    wire:model.live="search.identity">

                    <button type="submit" class="btn  btn" style="background-color:#00b2f2;color:white;">
                        <i class="flaticon-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>


    <!-- <div class="serch-content">
        <form wire:submit.prevent="applySearch" class="row g-3 align-items-end">
            <h3>{{ __('label.search') }}</h3>
            <div class="form-group">
            <input type="text" class="form-control" placeholder="{{ __('label.title') }}" wire:model.live="search.identity">
            <button type="submit" class="src-btn">
                <i class="flaticon-search"></i>
            </button>
            </div>
        </form>
    </div> -->
    
    @foreach($submissions as $submission)
    <h6 style="padding:25px 10px;" class="mt-3">
        <a href="{{ route('article',['menu_id' =>$active_menu_id,'submission_id'=>$submission->id]) }}">
            @if(App::getLocale()==='en') {{ $submission->title_en }} @else {{ $submission->title_fa }}  @endif
        </a>
    </h6> 
    
    <p style="font-size:13px;"><i class="flaticon-user"></i>
        @if($submission?->authors?->count() > 0)
            @foreach($submission->authors as $i => $author)
                @if(App::getLocale()==='en')
                    {{ $author->given_name_en }} {{ $author->family_name_en }}<sup>({{ $i+1 }})</sup>
                    @if(!$loop->last)،@endif
                @else
                    {{ $author->given_name_fa }} {{ $author->family_name_fa }}<sup>({{ $i+1 }})</sup>
                @endif
            @endforeach
            <br>

            @foreach($submission->authors as $i => $author)
                @if(App::getLocale()==='en')
                    ({{ $i+1 }}) {{ $author->affiliation_en }} @if($author?->type_id==1) ( {{ $author?->type?->type_name_en }} ) @endif
                @else
                    ({{ $i+1 }}) {{ $author->affiliation_fa }} @if($author?->type_id==1) ( {{ $author?->type?->type_name_fa }} ) @endif
                @endif
                @if(!$loop->last) ,<br> @endif
            @endforeach
        @endif
    </p>
    </p>
    <div class="row">
        <div class="col-lg-4 col-md-4 pb-1">
            @if($submission?->files?->count() > 0)
                @foreach($submission->files as $file)
                <a wire:click.prevent="downloadFile({{ $file->id }})"class="default-btn btn" style="padding:5px 10px;border-radius: 10px;"><i class="flaticon-pdf-file" style="font-size:20px;padding-right:0px;"></i></a>
                @endforeach
            @endif
        </div>
        <div class="col-lg-4 col-md-4 ">
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
         {{ __('label.number_of_read') }}  : {{ $submission->views }}
        </div>
    </div>
    @endforeach

    <div class="mt-4 justify-content-end px-3">
        {{ $submissions->links() }}
    </div>
</div>

