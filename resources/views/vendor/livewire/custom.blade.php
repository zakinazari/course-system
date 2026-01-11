<style>
    .paginations ul li a {
        cursor: pointer;
    }
</style>

<div class="paginations" @if(App::getLocale()==='en') style="direction: ltr;" @else style="direction: rtl;" @endif>
    <ul>

        {{-- Next (RTL: first arrow) --}}
        @if ($paginator->onFirstPage())
            <li><a href="#" aria-disabled="true"><i class="flaticon-next"></i></a></li>
        @else
            <li>
                <a wire:click="previousPage" wire:loading.attr="disabled">
                    <i class="flaticon-next"></i>
                </a>
            </li>
        @endif

        {{-- Pagination Numbers --}}
        @foreach ($elements as $element)

            {{-- "..." Separator --}}
            @if (is_string($element))
                <li><a href="#">{{ $element }}</a></li>
            @endif

            {{-- Page Numbers --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li><a class="active">{{ sprintf("%02d", $page) }}</a></li>
                    @else
                        <li>
                            <a wire:click="gotoPage({{ $page }})">
                                {{ sprintf("%02d", $page) }}
                            </a>
                        </li>
                    @endif
                @endforeach
            @endif

        @endforeach

        {{-- Previous (RTL: last arrow) --}}
        @if ($paginator->hasMorePages())
            <li>
                <a wire:click="nextPage" wire:loading.attr="disabled">
                    <i class="flaticon-back"></i>
                </a>
            </li>
        @else
            <li><a href="#" aria-disabled="true"><i class="flaticon-back"></i></a></li>
        @endif

    </ul>
</div>
