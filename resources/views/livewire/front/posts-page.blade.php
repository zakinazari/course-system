<div class="row">
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
    @foreach($posts as $post)
    <div class="col-lg-6 col-md-6">
        <div class="single-news-card">
            <div class="news-img">
                <a href="{{ route('post', ['slug' =>'post', 'post_id' =>$post->id]) }}"><img src="{{ asset('storage/' . $post->image) }}" alt="Image"></a>
            </div>
            <div class="news-content">
                <div class="list">
                    <ul>
                        <li><i class="flaticon-user"></i> <a href="{{ route('post', ['slug' =>'post', 'post_id' =>$post->id]) }}">
                            @if(App::getLocale()=='en') 
                                {{ $post->user?->name_en }} {{ $post->user?->family_name_en }}
                            @else 
                                {{ $post->user?->name_fa }} {{ $post->user?->family_name_fa }} 
                            @endif
                        </a></li>
                        <li><i class="flaticon-clock"></i> {{ __('label.date_published') }} 
                            @if(App::getLocale()==='en') 
                                {{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->format('Y/m/d H:i') : '' }}
                            @else
                                {{ verta($post->published_at)->format('Y/m/d H:i') }} 
                            @endif
                        </li>
                    </ul>
                </div>
                <a href="{{ route('post', ['slug' =>'post', 'post_id' =>$post->id]) }}" ><h3>@if(App::getLocale()=='fa') {{ $post->title_fa }} @else {{ $post->title_en }} @endif</h3></a>
                <a href="{{ route('post', ['slug' =>'post', 'post_id' =>$post->id]) }}" class="read-more-btn">ادامه خواندن<i class="flaticon-next"></i></a>
            </div>
        </div>
    </div>
    @endforeach
    {{ $posts->links() }}
</div>
