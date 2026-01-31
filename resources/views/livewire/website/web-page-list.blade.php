@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')}}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/typeahead-js/typeahead.css')}}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/typography.css')}}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/katex.css')}}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/editor.css')}}" />
@endsection
@section('page-style')

@endsection
<div>
    
    <!-- title -->
   @section('title',
    (
        ($active_menu?->parent?->name ?? '') 
        ? $active_menu?->parent?->name . '-' 
        : ''
    ) 
    . $active_menu?->name
    . ' | ' . __('label.app_name')
    )

    <!-- end title -->
    <!-- start header -->
    <h4 class="py-3 breadcrumb-wrapper mb-4">
    @if(!empty($active_menu?->grandParent?->name))
        <span class="text-muted fw-light"> {{ $active_menu?->grandParent?->name }} /</span>
    @endif
    @if(!empty($active_menu?->parent?->name))
        <span class="text-muted fw-light"> {{ $active_menu?->parent?->name }} /</span>
    @endif
    {{ $active_menu?->name }}  
    </h4>
    <!-- end header -->


    <div class="card">
       
        <div class="card-header d-flex justify-content-between align-items-center">

            <h5 class="card-title mb-0">{{ $active_menu?->name }}</h5>

            <div class="d-flex align-items-center gap-2">
                
                @if(add(Auth::user()->role_ids,$active_menu_id))
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#{{$modalId}}" wire:click="openModal">
                            <i class="bi bi-plus-lg"></i> {{ __('label.add_new_record') }} 
                        </button>
                    </div>
                @endif
            </div>

        </div>
        <hr>
        <div class="table-responsive text-nowrap">
 
            <div class="mb-3 px-3">
                <form wire:submit.prevent="applySearch" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <input type="text" class="form-control" placeholder="{{ __('label.name') }}" wire:model="search.identity">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">
                            {{ __('label.search') }}
                        </button>
                    </div>
                </form>

                <!-- perPage -->
                <div class="d-flex align-items-center gap-1 mt-3 justify-content-end">
                    <span>{{ __('label.show') }}</span>
                    <select class="form-select w-auto" wire:model.live="perPage">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span>{{ __('label.entries') }}</span>
                </div>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead class="table-dark">
                        <tr>
                            <th>{{ __('label.NO') }}</th>
                            <th>{{ __('label.name') }}</th>
                            <th>{{ __('label.title') }}</th>
                            <th>{{ __('label.content') }}</th>
                            <!-- <th>{{ __('label.image') }}</th> -->
                            <th>{{ __('label.files') }}</th>
                            <th>{{ __('label.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($web_pages as $i => $page)
                        <tr>
                            <td>{{ ($web_pages->currentPage() - 1) * $web_pages->perPage() + $i + 1 }}</td>
                            <td>
                                 @if(App::getLocale()=='fa') {{ $page->name_fa }} @else {{ $page->name_en }} @endif 
                            </td>
                            <td>
                                 @if(App::getLocale()=='fa') {{ $page->title_fa }} @else {{ $page->title_en }} @endif 
                            </td>
                            <td>
                                <button type="button" class="btn btn-success btn-icon rounded-pill" wire:click="showContent({{ $page->id }})">
                                    <i class="bx bx-show"></i>
                                </button>
                           </td>
                        
                            <td>
                                <button type="button" class="btn btn-secondary btn-icon rounded-pill" wire:click="showFiles({{ $page->id }})">
                                    <i class=" bx bx-detail"></i>
                                </button>
                           </td>
                            {{-- 
                            <td class="text-center align-middle">
                                @if($page->cover_image && \Storage::disk('public')->exists($page->cover_image))
                                    <img src="{{ asset('storage/' . $page->cover_image) }}" 
                                        alt="Cover Image" 
                                        class="img-fluid border rounded shadow-sm"
                                        style="width:60px; height:60px; object-fit:cover;">
                                @else
                                    <img src="{{ asset('images/default.png') }}" 
                                        alt="No Cover" 
                                        class="img-fluid border rounded shadow-sm"
                                        style="width:60px; height:60px; object-fit:cover;">
                                @endif
                            </td>
                          --}}
                            <td>
                                <div class="dropdown position-static">
                                    <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if(edit(Auth::user()->role_ids,$active_menu_id))
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="edit({{ $page->id }})"
                                            ><i class="bx bx-edit-alt me-1 text-success"></i>{{ __('label.edit') }}</a>
                                        @endif
                                        @if(delete(Auth::user()->role_ids,$active_menu_id))
                                            <a class="dropdown-item " href="javascript:void(0);"  onclick="confirmDelete({{ $page->id }},'{{$table_name}}')"
                                            ><i class="bx bx-trash me-1 text-danger"></i>{{ __('label.delete') }}</a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 justify-content-end px-3">
                {{ $web_pages->links() }}
            </div>
        </div>
    </div>
    
    <!--/ Bootstrap Table with Header Dark -->
    <div class="modal fade" id="{{$modalId}}" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@if($editMode) {{ __('label.editing') }}  @else {{ __('label.adding') }} @endif {{ $active_menu?->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <form @if($editMode) wire:submit.prevent="update" @else wire:submit.prevent="store" @endif>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.name',locale:'fa') }}</label>
                                <input type="text" id="nameBasic" class="form-control @error('name_fa') is-invalid @enderror" wire:model.lazy="name_fa">
                                @error('name_fa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.name',locale:'pa') }}</label>
                                <input type="text" id="nameBasic" class="form-control @error('name_en') is-invalid @enderror" wire:model.lazy="name_en">
                                @error('name_en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.title',locale:'fa') }}</label>
                                <input type="text" id="nameBasic" class="form-control @error('title_fa') is-invalid @enderror" wire:model.lazy="title_fa">
                                @error('title_fa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.title',locale:'pa') }}</label>
                                <input type="text" id="nameBasic" class="form-control @error('title_en') is-invalid @enderror" wire:model.lazy="title_en">
                                @error('title_en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                           <div class="col-md-6 mb-3" wire:ignore>
                                <label class="form-label">{{ __('label.content',locale:'fa') }}</label>
                                <div id="editor_fa" class="bg-white border rounded" style="min-height:100px;"></div>
                                <input type="hidden" wire:model.lazy="content_fa" id="content_fa">
                            </div>

                            <div class="col-md-6 mb-3" wire:ignore>
                                <label class="form-label">{{ __('label.content', locale:'pa') }}</label>
                                <div id="editor_en" class="bg-white border rounded" style="min-height:100px;"></div>
                                <input type="hidden" wire:model.lazy="content_en" id="content_en">
                            </div>
                        </div>
                        
                       {{-- <div class="row" >
                            <div class="col-12">
                                @if($existing_cover_image)
                                    <div class="mb-2 text-center">
                                        <img src="{{ asset('storage/' . $existing_cover_image) }}" 
                                            alt="Cover Image" class="rounded" width="120" height="120">
                                    </div>
                                @endif
                                <label for="formFile" class="form-label">{{ __('label.image') }}</label>
                                <input class="form-control @error('cover_image') is-invalid @enderror" 
                                    type="file" id="formFile" wire:model.lazy="cover_image">
                                @error('cover_image') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                       
                         --}}
                       <div class="row" style="margin-top:100px;">
                            <div class="col-md-12 mb-3">
                                <label for="fileInput" class="form-label">{{ __('label.files') }}</label>
                                <div class="input-group mb-2">
                                    <input type="file" class="form-control @error('files.*') is-invalid @enderror" id="fileInput" multiple wire:model="files">
                                </div>
                                 @error('files.*')
                                    <span class="text-danger" id ="fileErrors">{{ $message }}</span>
                                @enderror
                                <div id="fileError" class="invalid-feedback d-block" style="display:none;"></div>
                                <small style="color: #2ab7b2;">{{ __('label.file_mimes',['value'=>'pdf,mp4,mov,avi,webm']) }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer" style="">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('label.close') }}</button>
                        <button type="submit" class="btn btn-primary">@if($editMode) {{ __('label.update') }} @else {{ __('label.save') }} @endif</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
   <div class="mt-3">
    <!-- Button trigger modal -->

    <!-- Modal -->
    <div class="modal fade" id="modalShowContent" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel1">{{ __('label.content') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                  @if(App::getLocale()=='fa') {!! $show_contet_fa !!} @else {!! $show_contet_fa !!} @endif 
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                {{ __('label.close') }}
            </button>
            </div>
        </div>
        </div>
    </div>
    </div>

    <div class="modal fade" id="modalShowFiles" tabindex="-1" style="display: none;" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel1">{{ __('label.files') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($existing_files)
                <div class="mt-3">
                    <ul class="list-group">
                        @foreach($existing_files as $f)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @php
                                    $ext = strtolower(pathinfo($f->file_name, PATHINFO_EXTENSION));
                                    switch ($ext) {
                                        case 'pdf':
                                            $icon = '📄'; 
                                            break;
                                        case 'doc':
                                        case 'docx':
                                            $icon = '📝'; 
                                            break;
                                        default:
                                            $icon = '📁'; 
                                            break;
                                    }
                                @endphp
                                <span>
                                    <span class="me-2">{{ $icon }}</span>
                                    <span>{{ $f->file_name }}</span>&nbsp;&nbsp;&nbsp;<small></small>
                                </span>
                                
                                <div class="d-flex gap-2">
                                    <a wire:click.prevent="downloadFile({{ $f->id }})" class="btn btn-xs btn-secondary text-white">
                                        <i class="bx bx-download me-1"></i> {{ __('label.download') }}
                                    </a>
                                    <button onclick="confirmDelete({{ $f->id }},'web_page_files')" type="button" class="btn btn-icon btn-label-danger">
                                    <i class="bx bx-trash-alt"></i>
                                    </button>
                                </div>

                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                {{ __('label.close') }}
            </button>
            </div>
        </div>
        </div>
    </div>
</div>
@section('vendor-script')
<!-- form-editor -->
<script src="{{ asset('assets/vendor/libs/quill/katex.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/quill/quill.js') }}"></script>
@endsection

@section('page-script')

<!-- file-uploads -->
<script src="{{ asset('assets/js/forms-file-upload.js') }}"></script>
<script src="{{ asset('assets/js/forms-editors.js') }}"></script>
@endsection
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toolbarOptions = [
        ['bold', 'italic', 'underline', 'strike'],
        ['blockquote', 'code-block'],
        ['link', 'image', 'formula'],
        ['link', 'formula'],
        [{ 'header': 1 }, { 'header': 2 }],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        [{ 'indent': '-1'}, { 'indent': '+1' }],
        [{ 'direction': 'rtl' }],
        [{ 'size': ['small', false, 'large', 'huge'] }],
        [{ 'align': [] }],
        ['clean']
    ];

    const editors = {};

    function initEditor(editorId, hiddenId, alignDir = 'right') {
        const editorEl = document.getElementById(editorId);
        const hidden = document.getElementById(hiddenId);
        if (!editorEl || !hidden) return;

        const quill = new Quill(editorEl, {
            modules: { toolbar: toolbarOptions },
            theme: 'snow'
        });

        quill.format('align', alignDir);

        // sync hidden input on change
        quill.on('text-change', function() {
            hidden.value = quill.root.innerHTML;
            hidden.dispatchEvent(new Event('input'));
        });

        // reset / load content when modal opens
        Livewire.on('loadEditors', () => {
            quill.root.innerHTML = hidden.value || '';
        });

        editors[editorId] = quill;
    }

    initEditor('editor_fa', 'content_fa', 'right');
    initEditor('editor_en', 'content_en', 'left');
});


// file upload code---------------
 const input = document.getElementById('fileInput');
    const errorDiv = document.getElementById('fileError');
    const maxLengthEnglish = 120;
    const maxLengthPersian = 60;

    input.addEventListener('change', function() {
        const file = this.files[0];
        if(file){
            const isPersian = /[\u0600-\u06FF]/.test(file.name); 
            const maxLength = isPersian ? maxLengthPersian : maxLengthEnglish;

            if(file.name.length > maxLength){
                errorDiv.textContent = "{{ __('label.file_name_too_long') }}".replace(':max', maxLength);
                errorDiv.style.display = 'block';
                this.value = ''; 
            } else {
                errorDiv.style.display = 'none';
            }
        }
    });

</script>
