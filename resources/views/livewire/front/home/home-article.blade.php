<div>
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
        <div class="col-lg-4 col-md-4">
            <i class="flaticon-clock" style="color:#00b2f2"></i> {{ __('label.date_published') }}: {{ $submission->published_at? verta($submission->published_at)->format('Y/m/d H:i:a'): '' }}
        </div>
        <div class="col-lg-4 col-md-4">
           <span class="submission-views" style="display:flex; align-items:center; gap:4px;">
    
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
        <button type="button" class="default-btn btn mt-5">
        <a href="{{ route('page',['menu_id' =>1, 'slug' =>'articles'])}}" style="color:white;">
            {{ __('label.all_articles') }}
            <i class="flaticon-next"></i>
        </a>
        </button>
    </div>
</div>
