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
     {{ $active_menu?->name}} / {{ $submission_id }}
    </h4>

    <div class="col-12">
        <div class="bs-stepper wizard-vertical vertical wizard-vertical-icons-example mt-2">
            <div class="bs-stepper-header">
                <div class="step {{ $currentStep === 1 ? 'active' : '' }}" data-target="#step_1">
                    <button type="button" class="step-trigger" aria-selected="true" wire:click="goToStep(1)">
                        <span class="bs-stepper-circle">
                            <i class="bx bx-detail"></i>
                        </span>
                        <span class="bs-stepper-label">
                            <span class="bs-stepper-title">{{ __('label.details') }}</span>
                        </span>
                    </button>
                </div>
                <div class="line"></div>
                <div class="step {{ $currentStep === 2 ? 'active' : '' }}" data-target="#step_2">
                    <button type="button" class="step-trigger" aria-selected="false" wire:click="goToStep(2)">
                        <span class="bs-stepper-circle">
                            <i class="bx bx-upload"></i>
                        </span>
                        <span class="bs-stepper-label">
                            <span class="bs-stepper-title">{{ __('label.file_upload') }}</span>
                        </span>
                    </button>
                </div>
                <div class="line"></div>
                <div class="step {{ $currentStep === 3 ? 'active' : '' }}" data-target="#step_3">
                    <button type="button" class="step-trigger" aria-selected="false" wire:click="goToStep(3)">
                        <span class="bs-stepper-circle">
                            <i class="bx bx-group"></i>
                        </span>
                        <span class="bs-stepper-label">
                            <span class="bs-stepper-title">{{ __('label.contributors') }}</span>
                        </span>
                    </button>
                </div>
                <div class="line"></div>
                <div class="step {{ $currentStep === 4 ? 'active' : '' }}" data-target="#step_4">
                    <button type="button" class="step-trigger" aria-selected="false" wire:click="goToStep(4)">
                        <span class="bs-stepper-circle">
                            <i class="bx bx-comment"></i>
                        </span>
                        <span class="bs-stepper-label">
                            <span class="bs-stepper-title">{{ __('label.comments_for_editor') }}</span>
                        </span>
                    </button>
                </div>
            </div>

            <div class="bs-stepper-content ">
               <div class="{{ $currentStep !== 1 ? 'd-none' : '' }}">
                    @livewire(
                        'submissions.submission-details',
                        ['menu_id' => $active_menu_id, 'submission_id' => $submission_id],
                        key('submission-details-' . $submission_id)
                    )
                </div>

                <div class="{{ $currentStep !== 2 ? 'd-none' : '' }}">
                    @livewire(
                        'submissions.submission-files',
                        ['submission_id' => $submission_id],
                        key('submission-files-' . $submission_id)
                    )
                </div>

                <div class="{{ $currentStep !== 3 ? 'd-none' : '' }}">
                    @livewire(
                        'submissions.submission-authors',
                        ['submission_id' => $submission_id],
                        key('submission-authors-' . $submission_id)
                    )
                </div>
                @php
                    $currentPageUrl = url()->current();
                @endphp
                <div class="{{ $currentStep !== 4 ? 'd-none' : '' }}">
                    @livewire(
                        'submissions.submission-comment',
                        ['menu_id' => $active_menu_id, 'submission_id' => $submission_id,'current_url'=>$currentPageUrl],
                        key('submission-comment-' . $submission_id)
                    )
                </div>

            </div>
        </div>
    </div>
</div>
@section('vendor-script')
<!-- form-vizard -->
<script src="{{asset('assets/vendor/libs/@form-validation/umd/bundle/popular.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js')}}"></script>
@endsection

@section('page-script')
<!-- form-vizard -->
<script src="{{asset('assets/js/form-wizard-numbered.js')}}" defer></script>
<script src="{{asset('assets/js/form-wizard-validation.js')}}" defer></script>
@endsection
