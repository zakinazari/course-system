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
   <div class="news-details">
        <div class="news-simple-card">
            <img src="{{ asset('storage/' . $single_post->image) }}" alt="Image">
            <div class="list">
            <ul>
                <li><i class="flaticon-user"></i> <a href="#" wire:click.prevent="viewPost({{ $single_post->id }})">
                    @if(App::getLocale()=='en') 
                        {{ $single_post->user?->name_en }} {{ $single_post->user?->family_name_en }}
                    @else 
                        {{ $single_post->user?->name_fa }} {{ $single_post->user?->family_name_fa }} 
                    @endif
                </a></li>
                <li><i class="flaticon-clock"></i> {{ __('label.date_published') }} 
                    @if(App::getLocale()==='en') 
                        {{ $single_post->published_at ? \Carbon\Carbon::parse($single_post->published_at)->format('Y/m/d H:i:a') : '' }}
                    @else
                        {{ verta($single_post->published_at)->format('Y/m/d H:i:a') }} 
                    @endif
                </li>
            </ul>
            </div>
            <h2>@if(App::getLocale()=='fa') {{ $single_post->title_fa }} @else {{ $single_post->title_en }} @endif</h2>
            <p>
                @if(App::getLocale()=='fa') {!! $single_post->content_fa !!} @else {!! $single_post->content_en !!} @endif
            </p>
        </div>
    </div>
     <a class="button default-btn btn mt-3" href="{{ route('page', ['menu_id' =>$active_menu_id, 'slug' => 'post']) }}" >
         {{ __('label.all_posts') }}
        <i class="flaticon-next"></i>
    </a>
</div>
