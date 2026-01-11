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
    {{ $active_menu?->name }} / {{ $submission_id }} / ({{ __('label.round') }} {{ $submission->round }})
    </h4>

    <div class="row">
        <!-- First column-->
        <div class="col-12 col-lg-9">

            <!-- Inventory -->
            <div class="card mb-4">
         
                <div class="container-fluid">
                    <div class="row">

                        <!-- SIDEBAR -->
                        <div class="col-3 border-end sidebar" style="height:100vh; overflow-y:auto;">
                            <h5 class="mt-3">{{ __('label.status') }} :
                                <small>
                                @switch($submission->status)
                                
                                    @case('submitted')
                                        <span class="badge bg-label-primary me-1" style="font-size:10px;">
                                            {{ __('label.submitted') }}
                                        </span>
                                        @break

                                    @case('screening')
                                        <span class="badge bg-label-secondary me-1" style="font-size:10px;">
                                             {{ __('label.screening') }}
                                        </span>
                                        @break

                                    @case('under_review')
                                        <span class="badge bg-label-info me-1" style="font-size:10px;">
                                             {{ __('label.under_review') }}
                                        </span>
                                        @break
                                    @case('revision_required')
                                        <span class="badge bg-label-warning me-1" style="font-size:10px;">
                                             {{ __('label.revision_required') }}
                                        </span>
                                        @break

                                    @case('accepted')
                                        <span class="badge bg-label-success me-1" style="font-size:10px;">
                                            {{ __('label.accepted') }}
                                        </span>
                                        @break
                                    @case('rejected')
                                        <span class="badge bg-label-danger me-1" style="font-size:10px;">
                                             {{ __('label.rejected') }}
                                        </span>
                                        @break

                                    @case('published')
                                        <span class="badge bg-label-success me-1" style="font-size:10px;">
                                            {{ __('label.published') }}
                                        </span>
                                        @break
                                    @default
                                        <span class="badge bg-label-primary me-1" style="font-size:10px;">
                                            {{ $submission->status }}
                                        </span>
                                    @endswitch
                                </small>
                            </h5>
                            <!-- ------------start Submission------------------------- -->
                            <ul class="list-group" id="mainMenus">
                                <li class="list-group-item bg-transparent">
                                    <a class="text-decoration-none text-body" data-bs-toggle="collapse" href="#menu2">
                                        📝 {{ __('label.submission') }}
                                    </a>

                                    <ul class="collapse mt-2 ps-3 
                                        {{ in_array($current_page, ['details','files','contributors','comment']) ? 'show' : '' }}"
                                        id="menu2">

                                        <li>
                                            <a href="#" class="submenu text-body {{ $current_page==='details' ? 'active text-primary  fw-bold' : '' }}"
                                            wire:click="setPage('details',{{ $current_round }})">{{ __('label.details') }}</a>
                                        </li>

                                        <li>
                                            <a href="#" class="submenu text-body {{ $current_page==='files' ? 'active text-primary  fw-bold' : '' }}"
                                            wire:click="setPage('files',{{ $current_round }})">{{ __('label.files') }}</a>
                                        </li>

                                        <li>
                                            <a href="#" class="submenu text-body {{ $current_page==='contributors' ? 'active text-primary  fw-bold' : '' }}"
                                            wire:click="setPage('contributors',{{ $current_round }})">{{ __('label.contributors') }}</a>
                                        </li>

                                        <li>
                                            <a href="#" class="submenu text-body {{ $current_page==='comment' ? 'active text-primary  fw-bold' : '' }}"
                                            wire:click="setPage('comment',{{ $current_round }})">{{ __('label.author_comment') }}</a>
                                        </li>

                                    </ul>
                                </li>
                                <!-- ------------end Submission------------------------- -->


                                <!-- ------------start Reviews------------------------- -->
                                @if($reviews)

                                <li class="list-group-item bg-transparent" >
                                    <a class="text-decoration-none text-body" data-bs-toggle="collapse" href="#reviewMenu">
                                        🗂️ {{ __('label.reviews') }}
                                    </a>

                                    <ul class="collapse mt-2 ps-3
                                        {{ in_array($current_page, ['reviewers','recommendations','declined_reasons']) ? 'show' : '' }}"
                                        id="reviewMenu" >

                                        @for($i = 1; $i <= $submission->round; $i++)
                                            
                                            @php
                                                $hasRecommendation = $this->getRecommendationCountByRound($i) > 0;
                                                $hasDeclined = $this->getDeclinedCountByRound($i) > 0;
                                            @endphp

                                            <li class="mb-2" >

                                               
                                                <a class="text-decoration-none text-body"
                                                data-bs-toggle="collapse"
                                                href="#roundReview_{{ $i }}">
                                                    🟢 {{ __('label.round') }} {{ $i }}
                                                </a>

                                                
                                                <ul class="collapse mt-2 ps-3
                                                    {{ ($current_round == $i && in_array($current_page, ['reviewers','recommendations','declined_reasons'])) ? 'show' : '' }}"
                                                    id="roundReview_{{ $i }}" style="padding:0px !important;">

                                                    <li>
                                                        <a href="#"
                                                        class="submenu text-body 
                                                        {{ ($current_page === 'reviewers' && $current_round == $i) ? 'text-primary fw-bold' : '' }}"
                                                        wire:click="setPage('reviewers', {{ $i }})">
                                                            {{ __('label.reviewers') }}
                                                        </a>
                                                    </li>
                                                    @if($hasRecommendation)
                                                    <li >
                                                        <a href="#"
                                                        class="submenu text-body 
                                                        {{ ($current_page === 'recommendations' && $current_round == $i) ? 'text-primary fw-bold' : '' }}"
                                                        wire:click="setPage('recommendations', {{ $i }})">
                                                            {{ __('label.recommendations') }}
                                                        </a>
                                                    </li>
                                                    @endif
                                                    @if($hasDeclined)
                                                    <li>
                                                        <a href="#"
                                                        class="submenu text-body 
                                                        {{ ($current_page === 'declined_reasons' && $current_round == $i) ? 'text-primary fw-bold' : '' }}"
                                                        wire:click="setPage('declined_reasons', {{ $i }})">
                                                            {{ __('label.reviewer_declined_reasons') }}
                                                        </a>
                                                    </li>
                                                    @endif
                                                </ul>
                                            </li>
                                        @endfor

                                    </ul>
                                </li>
                                @endif
                                <!-- ------------end Reviews------------------------- -->

                                <!-- ------------start Editor Decision------------------------- -->
                                @if($submission->status==='accepted' || $submission->status==='rejected' || $submission->status==='revision_required' || $submission->status==='published')
                                <li class="list-group-item bg-transparent">
                                    <a class="text-decoration-none text-body" data-bs-toggle="collapse" href="#editorDecisionMenu">
                                        ⚖️ {{ __('label.editor_decision') }}
                                    </a>

                                    <ul class="collapse mt-2 ps-3 
                                        {{ str_starts_with($current_page, 'editor_decision.') ? 'show' : '' }}"
                                        id="editorDecisionMenu">

                                        @for($i = 1; $i <= $submission->round; $i++)
                                            <li>
                                                <a href="#"
                                                class="submenu text-body
                                                {{ $current_page === 'editor_decision.'.$i ? 'active text-primary fw-bold' : '' }}"
                                                wire:click="setPage('editor_decision.{{ $i }}',{{ $i }})">
                                                {{__('label.round')}} {{ $i }}
                                                </a>
                                            </li>
                                        @endfor

                                    </ul>
                                </li>
                                @endif
                                <!-- ------------end Editor Decision------------------------- -->
                            </ul>
                        </div>

                        <!-- CONTENT -->
                        <div class="col-9 p-4">
                            <!-- ------------start Submission------------------------- -->
                            @if($current_page === 'details')
                                @livewire('submissions.submission-show', ['submission_id' => $submission_id,'section'=>'details'])
                            @elseif($current_page === 'files')
                                @livewire('submissions.submission-show', ['submission_id' => $submission_id,'section'=>'files'])
                            @elseif($current_page === 'contributors')
                                @livewire('submissions.submission-show', ['submission_id' => $submission_id,'section'=>'contributors'])
                            @elseif($current_page === 'comment')
                               @livewire('submissions.submission-show', ['submission_id' => $submission_id,'section'=>'comment'])
                            @endif
                            <!-- ------------end Submission------------------------- -->

                            <!-- ------------start Reviews------------------------- -->
                           @if($current_page === 'reviewers')
                                @livewire('submissions.submission-review',
                                    [
                                        'active_menu_id' => $active_menu_id,
                                        'submission_id' => $submission_id,
                                        'round' => $current_round
                                    ],
                                    key('reviewers-' . $current_round) 
                                )

                            @elseif($current_page === 'recommendations')

                                @livewire('assignments.reviewer.reviewer-assignment-decision',
                                    [
                                        'submission_id' => $submission_id,
                                        'round' => $current_round
                                    ],
                                    key('recommendations-' . $current_round)  
                                )

                            @elseif($current_page === 'declined_reasons')

                                @livewire('assignments.reviewer.reviewer-assignment-declined-reasons',
                                    [
                                        'submission_id' => $submission_id,
                                        'round' => $current_round
                                    ],
                                    key('declined_reasons-' . $current_round)  
                                )

                            @endif

                            <!-- ------------end Reviews------------------------- -->
                            <!-- ------------start Editor Decision------------------------- -->
                            @if (str_starts_with($current_page, 'editor_decision.'))
                                @livewire('submissions.editor-decision', [
                                    'submission_id' => $submission_id,
                                    'round' => $current_round 
                                ],key('editor_decision_'.$current_round ))
                            @endif
                            <!-- ------------end Editor Decision------------------------- -->

                        </div>

                    </div>
                </div>
            </div>
            <!-- /Inventory -->
        </div>
        <!-- /Second column -->

        <!-- Second column -->
        <div class="col-12 col-lg-3">
            <!-- Pricing Card -->
            <div class="card mb-4">
                <div class="card-body">
                    @if(Auth::user()->isAdmin())
                    @if($submission->status==='submitted')
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#{{ $screeningModalId }}">
                        {{ __('label.screening') }}
                    </button><br>
                    @endif

                    @if($submission->status==='screening' || $submission->status==='under_review')
                    <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#{{ $sendForReviewModalId }}">
                         {{ __('label.send_for_review') }}
                    </button><br>
                    @endif
                    @if($submission->status==='screening' || $submission->status==='under_review')
                    <button type="button" class="btn btn-success mt-2" data-bs-toggle="modal"  data-bs-target="#{{ $acceptModalId }}">
                         {{ __('label.accept_skip_review') }}
                    </button><br>
                   
                    <button type="button" class="btn btn-danger mt-2" data-bs-toggle="modal" data-bs-target="#{{ $rejectModalId }}">
                        {{ __('label.reject_submission') }}
                    </button><br>
                    <button type="button" class="btn btn-warning mt-2" data-bs-toggle="modal" data-bs-target="#{{ $revisionModalId }}">
                        {{ __('label.revision_required') }}
                    </button><br>
                    @endif

                    @if($submission->status==='accepted')
                    <!-- <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal"  data-bs-target="#{{ $assignToIssueModalId }}" wire:click="assignToIssue">
                        {{ __('label.assign_to_issue') }}
                    </button> -->
                    @endif
                    @endif
                    <div class="mt-3">
                        <ul class="list-group">
                            <li class="list-group-item">
                                <div class="d-flex align-items-start">
                                    <i class="fa fa-user mt-1 me-2"></i>
                                    <div>
                                        <span>
                                            {{ App::getLocale()=='en' ? $authors->name_en: $authors->name_fa }}
                                            {{ App::getLocale()=='en' ? $authors->family_name_en: $authors->family_name_fa }}
                                        </span>
                                        <span class="text-muted">
                                            (<small>{{ __('label.author') }}</small>)
                                        </span>
                                        <hr>
                                        <div>
                                            <small>{{ __('label.education_degree') }}: <span class="text-muted">{{ App::getLocale()=='en' ? $authors->educationDegree?->name_en: $authors->educationDegree?->name_fa }} </span></small>
                                        </div>
                                        <div>
                                            <small>{{ __('label.academic_rank') }}: <span class="text-muted">{{ App::getLocale()=='en' ? $authors->academicRank?->name_en: $authors->academicRank?->name_fa }} </span></small>
                                        </div>
                                        <div>
                                            <small>{{ __('label.department') }}: <span class="text-muted">{{ App::getLocale()=='en' ? $authors->department_en: $authors->department_fa }} </span></small>
                                        </div>
                                        <div>
                                            <small>{{ __('label.preferred_research_area') }}: <span class="text-muted">{{ App::getLocale()=='en' ? $authors->preferred_research_area_en: $authors->preferred_research_area_fa }} </span></small>
                                        </div>
                                        <div>
                                            <small>{{ __('label.affiliation') }}: <span class="text-muted">{{ App::getLocale()=='en' ? $authors->affiliation_en: $authors->affiliation_fa }} </span></small>
                                        </div>
                                        <div>
                                            <small>{{ __('label.country') }}: <span class="text-muted">{{ App::getLocale()=='en' ? $authors->country?->country_name_en: $authors->country?->country_name_fa }} </span></small>
                                        </div>
                                        <div>
                                            <small>{{ __('label.province') }}: <span class="text-muted">{{ App::getLocale()=='en' ? $authors->province?->name_en: $authors->province?->name_fa }} </span></small>
                                        </div>
                                        <div>
                                            <small>{{ __('label.city') }}: <span class="text-muted">{{ App::getLocale()=='en' ? $authors->city_en: $authors->city_fa }} </span></small>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        
                    </div>
                </div>
            </div>
            <!-- /Pricing Card -->

        </div>
        <!-- /Second column -->
    </div>

     <!-- Small Modal -->

    <div class="modal fade" id="{{ $screeningModalId }}" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <form wire:submit.prevent="screeningSubmission">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel2">{{ __('label.screening') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="alert alert-warning" role="alert">
                                <h6 class="alert-heading mb-1">{{ __('label.warning') }}!</h6>
                                <span>{{ __('label.delete_title') }}!</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('label.no') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('label.yes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="{{ $sendForReviewModalId }}" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <form wire:submit.prevent="sendForReviewSubmission">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel2">{{ __('label.send_for_review') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">{{ __('label.reviewer') }}</label>

                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="{{ __('label.search') }}"
                                
                                 wire:model.live="search_reviewer"
                                 wire:keydown.enter.prevent="search_reviewer">

                                <select class="form-select" wire:model="reviewer_id">
                                    <option value="">{{ __('label.select') }}</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">
                                            {{ App::getLocale() === 'en' ? $user->name : $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            @error('reviewer_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                          <div class="row g-3">
                                <div class="col-sm-12 fv-plugins-icon-container">
                                    <label class="form-label" for="">{{ __('label.comments_for_author') }}</label>
                                    <textarea  id="comments_fa" class="form-control @error('comments_fa') is-invalid @enderror" placeholder="" wire:model.lazy="comments_fa" rows="4"></textarea>
                                        @error('comments_fa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
                                    
                                </div>
                            </div>

                            <div class="row g-3 mt-3">
                                <div class="col-sm-12 fv-plugins-icon-container">
                                    <label class="form-label" for="">{{ __('label.comments_for_author',locale:'en') }}</label>
                                    <textarea  id="comments_to_editor_en" class="form-control @error('comments_en') is-invalid @enderror" placeholder="" wire:model.lazy="comments_en" rows="4"></textarea>
                                        @error('comments_en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{__('label.no')}}</button>
                        <button type="submit" class="btn btn-primary">{{__('label.yes')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="{{ $acceptModalId }}" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <form wire:submit.prevent="acceptSubmission">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel2">{{ __('label.accept_skip_review') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                          <div class="row g-3">
                                <div class="col-sm-12 fv-plugins-icon-container">
                                    <label class="form-label" for="">{{ __('label.comments_for_author') }}</label>
                                    <textarea  id="comments_fa" class="form-control @error('comments_fa') is-invalid @enderror" placeholder="" wire:model.lazy="comments_fa" rows="4"></textarea>
                                        @error('comments_fa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
                                    
                                </div>
                            </div>

                            <div class="row g-3 mt-3">
                                <div class="col-sm-12 fv-plugins-icon-container">
                                    <label class="form-label" for="">{{ __('label.comments_for_author',locale:'en') }}</label>
                                    <textarea  id="comments_to_editor_en" class="form-control @error('comments_en') is-invalid @enderror" placeholder="" wire:model.lazy="comments_en" rows="4"></textarea>
                                        @error('comments_en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('label.no') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('label.yes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="{{ $rejectModalId }}" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <form wire:submit.prevent="rejectSubmission">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel2">{{ __('label.reject_submission') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                          <div class="row g-3">
                                <div class="col-sm-12 fv-plugins-icon-container">
                                    <label class="form-label" for="">{{ __('label.comments_for_author') }}</label>
                                    <textarea  id="comments_fa" class="form-control @error('comments_fa') is-invalid @enderror" placeholder="" wire:model.lazy="comments_fa" rows="4"></textarea>
                                        @error('comments_fa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
                                    
                                </div>
                            </div>

                            <div class="row g-3 mt-3">
                                <div class="col-sm-12 fv-plugins-icon-container">
                                    <label class="form-label" for="">{{ __('label.comments_for_author',locale:'en') }}</label>
                                    <textarea  id="comments_to_editor_en" class="form-control @error('comments_en') is-invalid @enderror" placeholder="" wire:model.lazy="comments_en" rows="4"></textarea>
                                        @error('comments_en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('label.no') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('label.yes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="{{ $revisionModalId }}" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <form wire:submit.prevent="revisionSubmission">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel2">{{ __('label.revision_required') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                          <div class="row g-3">
                                <div class="col-sm-12 fv-plugins-icon-container">
                                    <label class="form-label" for="">{{ __('label.comments_for_author') }}</label>
                                    <textarea  id="comments_fa" class="form-control @error('comments_fa') is-invalid @enderror" placeholder="" wire:model.lazy="comments_fa" rows="4"></textarea>
                                        @error('comments_fa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
                                    
                                </div>
                            </div>

                            <div class="row g-3 mt-3">
                                <div class="col-sm-12 fv-plugins-icon-container">
                                    <label class="form-label" for="">{{ __('label.comments_for_author',locale:'en') }}</label>
                                    <textarea  id="comments_to_editor_en" class="form-control @error('comments_en') is-invalid @enderror" placeholder="" wire:model.lazy="comments_en" rows="4"></textarea>
                                        @error('comments_en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('label.no') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('label.yes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="{{ $assignToIssueModalId }}" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <form wire:submit.prevent="assignToIssueSubmission">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel2">{{ __('label.assign_to_issue') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                          <div class="row g-3">
                                <div class="col-sm-12 fv-plugins-icon-container">
                                   <label class="form-label" for="multicol-country">{{ __('label.issue') }}</label>
                                    <select id="multicol-country" wire:ignore class="form-control @error('issue_id') is-invalid @enderror"  wire:model.lazy="issue_id" >
                                        <option value="">{{ __('label.select') }}</option>
                                        @foreach($issues as $issue)
                                        <option value="{{ $issue->id }}" >
                                            @if(App::getLocale()=='en')
                                                {{ $issue->title_en }}
                                            @elseif(App::getLocale()=='fa')
                                                {{ $issue->title_fa }}
                                            @endif
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('issue_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                                </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('label.no') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('label.yes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

 
    <!-- --------submissions----------------------- -->
    @if($submission->status!='published')
    <div class="col-12">
        <div class="card">

            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('label.submission') }}<small>({{ __('label.round') }} {{ $submission->round }})</small>:
                <small>
                @switch($submission->status)
                
                    @case('submitted')
                        <span class="badge bg-label-primary me-1" style="font-size:10px;">
                            {{ __('label.submitted') }}
                        </span>
                        @break

                    @case('screening')
                        <span class="badge bg-label-secondary me-1" style="font-size:10px;">
                            {{ __('label.screening') }}
                        </span>
                        @break

                    @case('under_review')
                        <span class="badge bg-label-info me-1" style="font-size:10px;">
                             {{ __('label.under_review') }}
                        </span>
                        @break
                    @case('revision_required')
                        <span class="badge bg-label-warning me-1" style="font-size:10px;">
                             {{ __('label.revision_required') }}
                        </span>
                        @break

                    @case('accepted')
                        <span class="badge bg-label-success me-1" style="font-size:10px;">
                            {{ __('label.accepted') }}
                        </span>
                        @break
                    @case('rejected')
                        <span class="badge bg-label-danger me-1" style="font-size:10px;">
                            {{ __('label.rejected') }}
                        </span>
                        @break

                    @case('published')
                        <span class="badge bg-label-success me-1" style="font-size:10px;">
                            {{ __('label.published') }}
                        </span>
                        @break
                    @default
                        <span class="badge bg-label-text-primary me-1" style="font-size:10px;">
                           {{ $submission->status }}
                        </span>
                    @endswitch
                </small>
                </h5>
            </div>
            
            <div style="">
                <div class="bs-stepper wizard-vertical vertical wizard-vertical-icons-example">

                    <div class="bs-stepper-header">

                        <div class="step {{ $currentStep === 1 ? 'active' : '' }}" data-target="#step_1">
                            <button type="button" class="step-trigger" wire:click="goToStep(1)">
                                <span class="bs-stepper-circle"><i class="bx bx-detail"></i></span>
                                <span class="bs-stepper-label">
                                    <span class="bs-stepper-title">{{ __('label.details') }}</span>
                                </span>
                            </button>
                        </div>

                        <div class="line"></div>

                        <div class="step {{ $currentStep === 2 ? 'active' : '' }}" data-target="#step_2">
                            <button type="button" class="step-trigger" wire:click="goToStep(2)">
                                <span class="bs-stepper-circle"><i class="bx bx-upload"></i></span>
                                <span class="bs-stepper-label">
                                    <span class="bs-stepper-title">{{ __('label.file_upload') }}</span>
                                </span>
                            </button>
                        </div>

                        <div class="line"></div>

                        <div class="step {{ $currentStep === 3 ? 'active' : '' }}" data-target="#step_3">
                            <button type="button" class="step-trigger" wire:click="goToStep(3)">
                                <span class="bs-stepper-circle"><i class="bx bx-group"></i></span>
                                <span class="bs-stepper-label">
                                    <span class="bs-stepper-title">{{ __('label.contributors') }}</span>
                                </span>
                            </button>
                        </div>

                        <div class="line"></div>

                        <div class="step {{ $currentStep === 4 ? 'active' : '' }}" data-target="#step_4">
                            <button type="button" class="step-trigger" wire:click="goToStep(4)">
                                <span class="bs-stepper-circle"><i class="bx bx-comment"></i></span>
                                <span class="bs-stepper-label">
                                    <span class="bs-stepper-title">{{ __('label.comments_for_editor') }}</span>
                                </span>
                            </button>
                        </div>

                    </div>

                    <div class="bs-stepper-content mt-3">

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
    </div>
   @endif

</div>

@section('vendor-script')
<!-- form-vizard -->
<script src="{{asset('assets/vendor/libs/@form-validation/umd/bundle/popular.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js')}}"></script>
<!-- form-editor -->
 <script src="{{ asset('assets/vendor/libs/quill/katex.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/quill/quill.js') }}"></script>
@endsection

@section('page-script')
<!-- form-vizard -->
<script src="{{asset('assets/js/form-wizard-numbered.js')}}" defer></script>
<script src="{{asset('assets/js/form-wizard-validation.js')}}" defer></script>
<script src="{{ asset('assets/js/forms-file-upload.js') }}"></script>
<script src="{{ asset('assets/js/forms-editors.js') }}"></script>
@endsection
