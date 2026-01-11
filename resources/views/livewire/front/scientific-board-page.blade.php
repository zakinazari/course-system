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
   <div class="faq-left-content pl-20">
        <div class="faq-title mb-3">
            <h3>{{ __('label.scientific_board_members') }}</h3>
        </div>

        <div class="accordion" id="accordionExample">
            @foreach($scientific_board as $i=> $board)
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading{{ $board->id }}">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $board->id }}" aria-expanded="false" aria-controls="collapseOne">
                        {{ $i+1 }}.
                        @if(App::getLocale()==='fa')
                            {{ $board->name_fa }}
                        @else
                            {{ $board->name_en }}
                        @endif
                    </button>
                </h2>
                <div id="collapse{{ $board->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $board->id }}" data-bs-parent="#accordionExample" style="">
                        @foreach($board?->members as $b=> $member)
                            <div class="accordion-body">
                                {{ $b+1 }}.
                                @if(App::getLocale()==='fa')
                                    {{ $member->name_fa }}
                                @else
                                    {{ $member->name_en }}
                                @endif
                            </div>
                        @endforeach
                </div>
            </div>
             @endforeach
        </div>
    </div>
</div>