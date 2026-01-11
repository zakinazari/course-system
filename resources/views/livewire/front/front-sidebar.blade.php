<div>
 <style>
    .keyword-item {
        display: inline-block;
        margin: 0 5px 5px 0;
        transition: transform 0.2s, color 0.2s;
    }

    .keyword-item:hover {
        transform: scale(1.2);
        text-decoration: underline;
    }
</style>
    <div class="enroll-courses">

        <div class="enroll-img" style="text-align: center;">
            <img src="{{ asset('storage/website/sidebar/wardak-door.jpg') }}"
                alt="Journal Logo"
                style="max-width:90%; margin-bottom:10px;">

            <h5>{{ __('label.leadership_board') }}</h5>
        </div>
        <div class="list">
            <ul>
                @foreach($boards as $board)
                    @if(App::getLocale()=='fa')
                    <li><span>{{ $board->title_fa }}</span> {{ $board->content_fa }} </li>
                    @else
                    <li><span>{{ $board->title_en }}</span> {{ $board->content_en }} </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>



    <div class="related-post-area">
        <h3>{{ __('label.last_news') }}</h3>
        @foreach($posts as $post)
        <div class="related-post-box">
            <div class="related-post-content">

                <a href="{{ route('post', ['slug' =>'post', 'post_id' =>$post->id]) }}">
                    <img src="{{ asset('storage/' . $post->image) }}" 
                        alt="Cover Image" 
                        class="img-fluid border  shadow-sm"
                        style="width:80px; height:80px; object-fit:cover;">
                </a>

                <h4>
                    <a  href="{{ route('post', ['slug' =>'post', 'post_id' =>$post->id]) }}" >@if(App::getLocale()==='en') {{ $post->title_en }} @else {{ $post->title_fa }} @endif</a>
                </h4>

                <p>
                    <i class="flaticon-clock"></i> {{ __('label.date_published') }}
                    @if(App::getLocale()==='en') 
                        {{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->format('Y/m/d H:i:a') : '' }}
                    @else
                        {{ verta($post->published_at)->format('Y/m/d H:i:a') }} 
                    @endif
                </p>

            </div>
        </div>
        @endforeach
    </div>

    <div class="related-post-area">
        <h3>{{ __('label.indexes') }}</h3>

        <div class="row g-3">
            @foreach($indexes as $index)
            <div class="col-12">
                <div class="d-flex align-items-center p-2 rounded shadow-sm bg-white">

                    <a href="#" class="me-3">
                        <div style="
                            width: 100%;
                            /* width: 100px;
                            height: 100px; */
                            /* border: 1px solid #ddd; */
                            border-radius: 6px;
                            padding: 4px;
                            background: #fff;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            ">
                            <img src="{{ asset('storage/' . $index->image) }}"
                                alt="Image"
                                style="max-width:100%; max-height:100%; object-fit: contain;">
                        </div>
                    </a>

                    <!-- <h4 class="mb-0">
                        <a href="#" class="text-dark text-decoration-none">
                            {{ $index->name }}
                        </a>
                    </h4> -->

                </div>
            </div>
            @endforeach
        </div>
    </div>

   <div class="related-post-area">
        <h3>{{ __('label.keywords') }}</h3>

        <div class="d-flex flex-wrap gap-2">
            @php
                $minFont = 12; 
                $maxFont = 32;
                $maxCount = $keywords->max('submissions_count');
                $minCount = $keywords->min('submissions_count');
                $colors = ['#e6194B','#3cb44b','#ffe119','#4363d8','#f58231','#911eb4','#46f0f0','#f032e6','#bcf60c','#fabebe'];
            @endphp

            @foreach($keywords as $keyword)
                @php
                    $count = $keyword->submissions_count;
                    $size = ($maxCount != $minCount)
                        ? $minFont + ($count - $minCount) * ($maxFont - $minFont) / ($maxCount - $minCount)
                        : ($minFont + $maxFont) / 2;
                    $color = $colors[$keyword->id % count($colors)];
                    $angle = rand(-20,20);
                @endphp

                <a href="{{ route('page', ['menu_id' =>1, 'slug' =>'articles']) }}?keyword={{ $keyword->id }}"
                class="keyword-item text-decoration-none"
                style="font-size: {{ round($size) }}px; color: {{ $color }}; display:inline-block; transform: rotate({{ $angle }}deg); cursor:pointer;"
                onmouseover="this.style.transform='scale(1.3) rotate({{ $angle }}deg)';"
                onmouseout="this.style.transform='rotate({{ $angle }}deg)';">
                    @if(App::getLocale()==='en')
                        {{ $keyword->keyword_en }}
                    @else
                        {{ $keyword->keyword_fa }}
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>
