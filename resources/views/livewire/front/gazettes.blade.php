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
        {{ __('label.official_journals') }}
      </h4>
      <hr>
    <ul class="list-group list-group-flush" id="gazetteSidebar">

        @foreach($gazettes as $gazette)
            @php $collapseId = 'gazette-'.$gazette->id; @endphp

            <li class="list-group-item bg-transparent border-0 px-0">
                <a class="d-flex align-items-center text-decoration-none fw-bold"
                   data-bs-toggle="collapse"
                   href="#{{ $collapseId }}"
                   role="button"
                   aria-expanded="false"
                   aria-controls="{{ $collapseId }}">
                    📁<span class="ms-2">{{ $gazette->title_fa }}</span>
                </a>
                <ul class="collapse mt-2 ps-4 list-unstyled"
                    id="{{ $collapseId }}"
                    data-bs-parent="#gazetteSidebar">
                    @foreach($gazette->files as $file)
                        <li class="mb-1">
                            <a href="{{ route('gazette.file.view', $file->id) }}"
                               target="_blank"
                               class="text-decoration-none d-flex align-items-center">
                                <i class="flaticon-pdf-file" style="color:red;"></i><span class="ms-2">{{ $file->file_name }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        @endforeach
    </ul>
</div>
