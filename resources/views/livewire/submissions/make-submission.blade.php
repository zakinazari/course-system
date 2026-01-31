

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
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div class="col-md mb-4 mb-md-0">
                <div class="card">
                    <h5 class="card-header">{{ __('label.submission_submit') }}</h5>
                    
                    <div class="card-body">

                        <form class="browser-default-validation" wire:submit.prevent="store">

                            <div class="mb-3" wire:ignore>
                                <label class="form-label">{{ __('label.accepted_abstracts') }}</label>

                                <select
                                    class="form-control select2 @error('accepted_abstract_id') is-invalid @enderror"
                                    id="accepted_abstract_id">
                                    <option value="">{{ __('label.select') }}</option>

                                </select>
                            </div>
                            @error('accepted_abstract_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                            <div class=" mb-3" wire:ignore>
                                <label class="form-label">{{ __('label.main_axes') }}</label>
                                <select class="form-control select2 @error('main_axis_id') is-invalid @enderror"
                                    wire:model.lazy="main_axis_id" id="main_axis_id"
                                    wire:change="loadSubAxes($event.target.value)" >
                                    <option value="">{{ __('label.select') }}</option>
                                    @foreach($main_axes as $i => $axis)
                                        <option value="{{ $axis->id }}"> 
                                                @if(App::getLocale()=='en')
                                                   {{ $i+1 }}. {{ $axis->title_en }}
                                                @else
                                                    {{ $i+1 }}. {{ $axis->title_fa }}
                                                @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('main_axis_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            <div class=" mb-3">
                                <label class="form-label">{{ __('label.sub_axes') }}</label>
                                <select class="form-control  @error('sub_axis_id') is-invalid @enderror"
                                    wire:model.lazy="sub_axis_id" id="sub_axis_id" >
                                    <option value="">{{ __('label.select') }}</option>
                                    @foreach($sub_axes as $i => $sub)
                                        <option value="{{ $sub->id }}"> 
                                                @if(App::getLocale()=='en')
                                                   {{ $i+1 }}. {{ $sub->title_en }}
                                                @else
                                                    {{ $i+1 }}. {{ $sub->title_fa }}
                                                @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('sub_axis_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="basic-default-name">{{ __('label.title') }}</label>
                                <input type="text" disabled class="form-control" id="basic-default-name" placeholder="" class="form-control @error('title_fa') is-invalid @enderror" wire:model.lazy='title_fa' dir="rtl">
                                 @error('title_fa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="basic-default-name">{{ __('label.title') }} ({{ __('label.en',locale:'en')}})</label>
                                <input type="text" class="form-control" id="basic-default-name" placeholder="" class="form-control @error('title_en') is-invalid @enderror" wire:model.lazy='title_en' dir="ltr">
                                 @error('title_en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary form-control">{{ __('label.save') }}</button>
                                </div>
                            </div>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2"></div>
    </div>
</div>

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

        // $('#sub_axis_id')
        //     .off('change')
        //     .on('change', function () {
        //         $wire.set('sub_axis_id', $(this).val());
        //     });
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

