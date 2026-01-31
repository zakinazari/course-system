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
            <h3>{{ __('label.official_journals') }}</h3>

            <div class="form-group">
                <div class="input-group category-content">
            
                    <select class="form-select form-select-sm flex-grow-0" style="width: 120px;" wire:model.live="search.type">
                        <option value="title">{{ __('label.title') }}</option>
                        <option value="ruling_number">{{ __('label.gazette_number') }}</option>
                    </select>


                    <input type="text" class="form-control form-control-sm"
                    placeholder="{{ $search['type'] === 'title' ? __('label.title') : __('label.gazette_number') }}"
                    wire:model.live="search.identity">

                    <button type="submit" class="btn  btn" style="background-color:#00b2f2;color:white;">
                        <i class="flaticon-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <ul class="list-group list-group-flush" id="gazetteSidebar">
        @foreach($gazettes as $gazette)
            @php $collapseId = 'gazette-'.$gazette->id; @endphp

            <li class="list-group-item bg-transparent border-0 px-0">
                <a class="d-flex align-items-center text-decoration-none fw-bold fs-5"
                   data-bs-toggle="collapse"
                   href="#{{ $collapseId }}"
                   role="button"
                   aria-expanded="false"
                   aria-controls="{{ $collapseId }}">
                    📁<span class="ms-2"> 
                        @if(App::getLocale()=='fa') {{ $gazette->title_fa }}
                        @else {{ $gazette->title_en }} @endif 
                        </span>
                </a>
                <ul class="collapse mt-2 ps-4 list-unstyled"
                    id="{{ $collapseId }}"
                    data-bs-parent="#gazetteSidebar">
                    @foreach($gazette?->ruling as $i=> $ruling)
                        @if(!empty($ruling?->files))
                        <li class="mb-1">
                            <a href="{{ route('gazette.ruling.file.view', $ruling->id) }}"
                               target="_blank"
                               class="text-decoration-none d-flex align-items-center">
                                <i class="flaticon-pdf-file flaticon-pdf-file fs-4 text-danger" ></i><span class="ms-2"> {{ $i + 1 }}. @if(App::getLocale()=='fa') {{ $ruling->title_fa }} @else {{ $ruling->title_en }} @endif</span>
                            </a>
                        </li>
                        @endif
                    @endforeach
                </ul>
            </li>
        @endforeach
    </ul>
    <div class="mt-4 justify-content-end px-3">
        {{ $gazettes->links() }}
    </div>
</div>
