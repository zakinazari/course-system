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
     <h4>
        @if(App::getLocale()=='fa')
            {{ $menu?->page?->title_fa }}
        @else
            {{ $menu?->page?->title_en }}
        @endif
      </h4>
        <p>
            @if(App::getLocale()=='fa')
                {!! $menu?->page?->content_fa !!}
            @else
                {!! $menu?->page?->content_en !!}
            @endif
        </p>

    <h4>{{ __('label.latest_articles') }}</h4>
    <hr>
    <div class="row align-items-center">
        <div class="col-lg-12 col-md-12">
            <!-- --------submissions----------- -->
            @livewire('front.home.home-article',['active_menu_id' =>$active_menu_id])
        </div>
    </div>
</div>