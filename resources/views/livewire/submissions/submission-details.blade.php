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
    <form  wire:submit.prevent="updateDetails">
   <!-- Title -->
        <div class="row g-3">
            <div class="col-sm-12 fv-plugins-icon-container">
                <label class="form-label" for="">{{ __('label.title') }} <span class="required" style="color:red;">*</span></label>
                <input type="text" name="title" id="title" class="form-control @error('title_fa') is-invalid @enderror" placeholder="" wire:model.lazy="title_fa" dir="rtl">
                    @error('title_fa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
            </div>
        </div>
        <div class="row g-3">

            <div class="col-sm-12 fv-plugins-icon-container">
                <label class="form-label" for="">{{ __('label.title') }} ({{ __('label.en',locale:'en') }}) <span class="required" style="color:red;">*</span></label>
                <input type="text" name="title_en" id="title_en" class="form-control @error('title_en') is-invalid @enderror" placeholder="" wire:model.lazy="title_en" dir="ltr">
                    @error('title_en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
            </div>
        </div>

        <!-- Keywords -->
        <div class="row mt-3">
            <div class="col-sm-12 fv-plugins-icon-container">
                <label for="newKeyword">{{ __('label.keywords') }}</label>
                <div class="d-flex mb-2">
                    <input 
                        type="text"
                        id="keywordsInputFa"
                        class="form-control mb-2"
                        wire:model.defer="newKeyword_fa"
                        wire:keydown.enter.prevent="addKeywordFa"
                        wire:keydown.comma.prevent="addKeywordFa"
                        placeholder="{{__('label.keywords.placeholder',locale:'fa')}}"
                    >
                </div>
                @error('keywords_fa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                <div class="mb-3">
                    @foreach($keywords_fa as $i => $keyword)
                        <span class="btn btn-label-secondary btn-sm me-1">
                            {{ $keyword }}
                            <button type="button" class="btn-close btn-close-white btn-sm ms-1" wire:click="removeKeywordFa({{ $i }})"></button>
                        </span>
                    @endforeach
                </div>
            </div>
            
        </div>

        <!-- Keywords -->
        <div class="row mt-3">
            <div class="col-sm-12 fv-plugins-icon-container">
                <label for="newKeyword">{{ __('label.keywords') }} ({{ __('label.en',locale:'en') }})</label><br>
                <div class="d-flex mb-2">
                    <input 
                        type="text"
                        id="keywordsInputEn"
                        class="form-control mb-2"
                        wire:model.defer="newKeyword_en"
                        wire:keydown.enter.prevent="addKeywordEn"
                        wire:keydown.comma.prevent="addKeywordEn"
                        wire:keydown.space.prevent="addKeywordEn"
                        placeholder="{{__('label.keywords.placeholder',locale:'en')}}"
                    >
                    
                </div>
                @error('keywords_en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                <div class="mb-3">
                    @foreach($keywords_en as $i => $keyword)
                        <span class="btn btn-label-secondary btn-sm me-1">
                            {{ $keyword }}
                            <button type="button" class="btn-close btn-close-white btn-sm ms-1" wire:click="removeKeywordEn({{ $i }})"></button>
                        </span>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Abstract -->
         <div class="row mt-3">
            <div class="position-relative col-sm-12 fv-plugins-icon-container">
                <div wire:ignore>
                    <label class="form-label" for="">{{ __('label.abstract') }} <span class="required" style="color:red;">*</span></label>
                    <div id="editor_fa" class="bg-white border rounded  @error('abstract_fa') is-invalid @enderror" style="min-height:200px;direction:rtl">
                        {!! $abstract_fa !!}
                    </div>
                    
                </div>
            </div>
            @error('abstract_fa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            <input type="hidden" wire:model="abstract_fa" id="abstract_fa">
        </div>

        <div class="row mt-3">
            <div class="position-relative col-sm-12 fv-plugins-icon-container">
                <div wire:ignore>
                    <label class="form-label" for="">{{ __('label.abstract') }} ({{ __('label.en',locale:'en') }})<span class="required" style="color:red;">*</span></label>
                    <div id="editor_en" class="bg-white border rounded  @error('abstract_en') is-invalid @enderror" style="min-height:200px;">
                        {!! $abstract_en !!}
                </div>
                </div>
                 @error('abstract_en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                <input type="hidden" wire:model="abstract_en" id="abstract_en">
                <hr>
                <div class="col-12 d-flex justify-content-between">
                    <button class="btn btn-label-secondary btn-prev" disabled="">
                    <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                    <span class="d-sm-inline-block d-none">{{ __('label.prev') }}</span>
                    </button>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-success" wire:click="updateDetails('save')">{{ __('label.save') }}</button>
                        <button  wire:click="validateStep(1)" class="btn btn-primary btn-next">
                            <span class="d-sm-inline-block d-none me-sm-1">{{ __('label.next') }}</span>
                            <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                        </button>
                    </div>
                    
                </div>
            </div>
        </div>
    </form>
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

    initEditor('editor_fa', 'abstract_fa', 'left');

    initEditor('editor_en', 'abstract_en', 'right');
});
</script>

