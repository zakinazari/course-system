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
        <div class="mb-3" wire:ignore>
            <label class="form-label">{{ __('label.accepted_abstracts') }}</label>

            <select
                class="form-control select2 @error('accepted_abstract_id') is-invalid @enderror"
                id="accepted_abstract_id">
                <option value="">{{ __('label.select') }}</option>
                    @foreach($accepted_abstracts as $i => $abstract)
                        <option value="{{ $abstract->id }}"> 
                                @if(App::getLocale()=='en')
                                    {{ substr($abstract->title_en, 0, 200) }}{{ strlen($abstract->title_en) > 200 ? '…' : '' }}
                                @else
                                    {{ substr($abstract->title_fa, 0, 200) }}{{ strlen($abstract->title_fa) > 200 ? '…' : '' }}
                                @endif
                        </option>
                    @endforeach
            </select>
        </div>
        @error('accepted_abstract_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

        <div class="row g-3 axis-wrapper" wire:ignore>
            <div class="col-sm-12 fv-plugins-icon-container">
                <label class="form-label">{{ __('label.main_axes') }}</label>
                <select class="form-control select2 @error('main_axis_id') is-invalid @enderror"
                    wire:model.lazy="main_axis_id" id="main_axis_id"
                    wire:change="loadSubAxes($event.target.value)" >
                    <option value="">{{ __('label.select') }}</option>
                    @foreach($main_axes as $i => $axis)
                        <option value="{{ $axis->id }}"> 
                                @if(App::getLocale()=='en')
                                    {{ substr($axis->title_en, 0, 100) }}{{ strlen($axis->title_en) > 100 ? '…' : '' }}
                                @else
                                    {{ substr($axis->title_fa, 0, 100) }}{{ strlen($axis->title_fa) > 100 ? '…' : '' }}
                                @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        @error('main_axis_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        <div class="row g-3">
            <div class="col-sm-12 fv-plugins-icon-container">
                <label class="form-label">{{ __('label.sub_axes') }}</label>
                <select class="form-control  @error('sub_axis_id') is-invalid @enderror"
                    wire:model.lazy="sub_axis_id" id="sub_axis_id" >
                    <option value="">{{ __('label.select') }}</option>
                    @foreach($sub_axes as $i => $sub)
                        <option value="{{ $sub->id }}"> 
                                @if(App::getLocale()=='en')
                                    {{ substr($sub->title_en, 0, 200) }}{{ strlen($sub->title_en) > 200 ? '…' : '' }}
                                @else
                                    {{ substr($sub->title_fa, 0, 200) }}{{ strlen($sub->title_fa) > 200 ? '…' : '' }}
                                @endif
                        </option>
                    @endforeach
                </select>
               <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
            </div>
            @error('sub_axis_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>
   <!-- Title -->
        <div class="row g-3">
            <div class="col-sm-12 fv-plugins-icon-container">
                <label class="form-label" for="">{{ __('label.title') }} <span class="required" style="color:red;">*</span></label>
                <input type="text" name="title" id="title" disabled class="form-control @error('title_fa') is-invalid @enderror" placeholder="" wire:model.lazy="title_fa" dir="rtl">
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

@script
<script>
document.addEventListener("livewire:initialized", function () {

    function initSelect2() {

        $('.select2').each(function () {
            const $select = $(this);
            const $modal  = $select.closest('.modal');

            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }

            $select.select2({
                width: '100%',
                dropdownParent: $modal.length ? $modal : $(document.body)
            });


        });


        $('#main_axis_id')
            .off('change')
            .on('change', function () {
                let mainAxisId = $(this).val();

                $wire.set('main_axis_id', mainAxisId);
                $wire.call('loadSubAxes', mainAxisId);
            });
    }

    initSelect2();

    Livewire.hook('morphed', () => {
        initSelect2();
    });

});
document.addEventListener('livewire:initialized', function () {

    function initSelect2() {

        $('#accepted_abstract_id').select2({
            // width: '100%',
            minimumInputLength: 2,
            ajax: {
                url: '/search-abstracts',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                }
            }
        });

            $('#accepted_abstract_id').off('change').on('change', function () {
            @this.set('accepted_abstract_id', $(this).val());
            @this.call('setTitle', $(this).val());
        });
    }

    initSelect2();

    Livewire.hook('morphed', () => {
        initSelect2();
    });
});

</script>
@endscript