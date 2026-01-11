

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
                    <!-- <p style="text-align: justify; margin: 0 20px; line-height: 1.6; font-size: 0.95rem;">
                        Thank you for submitting to the <strong> Journal </strong>. 
                        You will be asked to upload files, identify co-authors, and provide information such as the title and abstract.
                    </p>
                    <p style="text-align: justify; margin: 0 20px; line-height: 1.6; font-size: 0.95rem;">
                        Once you begin, you can save your submission and come back to it later. You will be able to review and correct any information before you submit.
                    </p> -->
                    <div class="card-body">
                        <form class="browser-default-validation" wire:submit.prevent="store">
                            <div class="mb-3">
                                <label class="form-label" for="basic-default-name">{{ __('label.title') }}</label>
                                <input type="text" class="form-control" id="basic-default-name" placeholder="" class="form-control @error('title_fa') is-invalid @enderror" wire:model.lazy='title_fa' dir="rtl">
                                 @error('title_fa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="basic-default-name">{{ __('label.title') }} ({{ __('label.en',locale:'en')}})</label>
                                <input type="text" class="form-control" id="basic-default-name" placeholder="" class="form-control @error('title_en') is-invalid @enderror" wire:model.lazy='title_en' dir="ltr">
                                 @error('title_en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">{{ __('label.save') }}</button>
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


