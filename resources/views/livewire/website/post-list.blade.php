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
                        <input type="text" class="form-control" placeholder="{{ __('label.title') }}" wire:model="search.identity">
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
                            <th>{{ __('label.title') }}</th>
                            <th>{{ __('label.content') }}</th>
                            <th>{{ __('label.status') }}</th>
                            <th>{{ __('label.date_published') }}</th>
                            <th>{{ __('label.image') }}</th>
                            <th>{{ __('label.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($posts as $i => $post)
                        <tr>
                            <td>{{ ($posts->currentPage() - 1) * $posts->perPage() + $i + 1 }}</td>
                            <td>
                                 @if(App::getLocale()=='fa') {{ $post->title_fa }} @else {{ $post->title_en }} @endif 
                            </td>
                            <td>
                                <button type="button" class="btn btn-success btn-icon rounded-pill" wire:click="showContent({{ $post->id }})">
                                    <i class="bx bx-show"></i>
                                </button>
                           </td>
                            <td>
                                @if($post->status ==='published')
                                <span class="badge bg-label-success me-1">{{ __('label.published') }}</span>
                                @else
                                <span class="badge bg-label-danger me-1">{{ __('label.unpublished') }}</span>
                                @endif
                            </td>
                            <td>
                                @if($post->published_at!='')

                                {{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->format('Y/m/d') : '' }}
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                @if($post->image && \Storage::disk('public')->exists($post->image))
                                    <img src="{{ asset('storage/' . $post->image) }}" 
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
                          
                            <td>
                                <div class="dropdown position-static">
                                    <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if(edit(Auth::user()->role_ids,$active_menu_id))
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="edit({{ $post->id }})"
                                            ><i class="bx bx-edit-alt me-1 text-success"></i>{{ __('label.edit') }}</a>
                                        @endif
                                        @if(delete(Auth::user()->role_ids,$active_menu_id))
                                            <a class="dropdown-item " href="javascript:void(0);"  onclick="confirmDelete({{ $post->id }},'{{$table_name}}')"
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
                {{ $posts->links() }}
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
                        <div class="row" style="margin-top:100px;">
                           <div class="col-md-6 mb-3">
                                <label class="form-label d-block">{{ __('label.status') }}</label>
                                <div class="form-check form-check-inline">
                                    <input name="status" 
                                        class="form-check-input" 
                                        type="radio" 
                                        id="status-active" 
                                        value="published" 
                                        
                                        wire:model.lazy="status"  @checked($status == 'published' || is_null($status))>
                                    <label class="form-check-label" for="status-active">{{ __('label.publish') }}</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input name="status" 
                                        class="form-check-input" 
                                        type="radio" 
                                        id="status-inactive" 
                                        value="draft" 
                                        wire:model.lazy="status"  @checked($status == 'draft')>
                                    <label class="form-check-label" for="status-inactive">{{ __('label.unpublish') }}</label>
                                </div>
                                @error('status') 
                                    <div class="invalid-feedback d-block">{{ $message }}</div> 
                                @enderror
                            </div>
                            <div class="col mb-3">
                                <label class="form-label">{{ __('label.date_published') }}</label>
                                <input type="date" id="" class="form-control" placeholder="" wire:model.lazy="published_at">
                            </div>
                        </div>
                    
                       <div class="row" >
                            <div class="col-12">
                                @if($existing_image)
                                    <div class="mb-2 text-center">
                                        <img src="{{ asset('storage/' . $existing_image) }}" 
                                            alt="Cover Image" class="rounded" width="120" height="120">
                                    </div>
                                @endif
                                <label for="fileInput" class="form-label">{{ __('label.image') }}</label>
                                <input class="form-control @error('image') is-invalid @enderror" 
                                    type="file" id="fileInput" wire:model.lazy="image">
                                @error('image') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                <div id="fileError" class="invalid-feedback d-block" style="display:none;"></div>
                            </div>
                        </div>
                       
                    </div>

                    <div class="modal-footer" >
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
        // ['link', 'image', 'video', 'formula'],
        ['link', 'formula'],
        [{ 'header': 1 }, { 'header': 2 }],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        [{ 'indent': '-1'}, { 'indent': '+1' }],
        [{ 'size': ['small', false, 'large', 'huge'] }],
        [{ 'direction': 'rtl' }],
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

