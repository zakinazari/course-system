<div >
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
      <h4>
        @if(App::getLocale()=='fa')
            {{ $menu?->page?->title_fa }}
        @else
            {{ $menu?->page?->title_en }}
        @endif
      </h4>
        @if(
            (App::getLocale() === 'fa' && trim(strip_tags($menu?->page?->content_fa)) !== '')
            ||
            (App::getLocale() !== 'fa' && trim(strip_tags($menu?->page?->content_en)) !== '')
        )
        <p>
            @if(App::getLocale()=='fa')
                {!! $menu?->page?->content_fa !!}
            @else
                {!! $menu?->page?->content_en !!}
            @endif
        </p>
        @endif
        
        @if($menu?->page?->files->count()>0)
            @if($menu?->page?->files->pluck('file_type')->first() !='pdf')
            <video width="100%" height="400" controls>
            <source src="{{ getImage($menu?->page?->files->pluck('file_path')->first()) }}" type="video/mp4">
            Your browser does not support the video tag.
            </video>
            @else
            <iframe src="{{ getImage($menu?->page?->files->pluck('file_path')->first()) }}" width="100%" height="700" style="border:none;">

            </iframe>
            @endif
        @endif
</div>
