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
        <!-- First column-->
        <div class="col-12 col-lg-9">
            <div class="card mb-4">
            
                <div class="container-fluid">
                    <div class="row">

                        <!-- SIDEBAR -->
                        <div class="col-3 border-end sidebar" style="height:100vh; overflow-y:auto;">
                            <h5 class="mt-3">{{ __('label.status') }} :
                                <small>
                                    @if($review->status ==='pending')
                                    <span class="badge bg-label-primary me-1" style="font-size:10px;">{{ __('label.pending') }}</span>
                                    @elseif($review->status==='accepted')
                                    <span class="badge bg-label-success me-1" style="font-size:10px;">{{ __('label.accepted') }}</span>
                                    @elseif($review->status==='completed')
                                    <span class="badge bg-label-success me-1" style="font-size:10px;">{{ __('label.completed') }}</span>
                                    @elseif($review->status==='declined')
                                    <span class="badge bg-label-danger me-1" style="font-size:10px;">{{ __('label.declined') }}</span>
                                    @endif
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

                                    </ul>
                                </li>
                                <!-- ------------end Submission------------------------- -->

                                <!-- ----------start Declined Reason--------------------- -->
                                @if($review->status==='declined')
                                 <li class="list-group-item bg-transparent">
                                    <a class="text-decoration-none text-body  {{ $current_page === 'declined_reason' ? 'active text-primary fw-bold' : '' }}"
                                    wire:click="setPage('declined_reason',{{ $current_round }})"
                                    data-bs-toggle="collapse" href="#reviewerDeclinedMenu">
                                        ⚖️ {{ __('label.reviewer_declined_reason') }}
                                    </a>
                                </li>
                                @endif
                                <!-- ----------end Declined Reason--------------------- -->
                                <!-- ----------start Declined Reason--------------------- -->
                                @if($review->status==='completed')
                                 <li class="list-group-item bg-transparent">
                                    <a class="text-decoration-none text-body  {{ $current_page === 'recommendation' ? 'active text-primary fw-bold' : '' }}"
                                    wire:click="setPage('recommendation',{{ $current_round }})"
                                    data-bs-toggle="collapse" href="#reviewerRecommendationMenu">
                                        ⚖️ {{__('label.recommendation')}}
                                    </a>
                                </li>
                                @endif
                                <!-- ----------end Declined Reason--------------------- -->
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


                            <!-- ------------end Reviews------------------------- -->

                             
                            @if ($current_page ==='declined_reason')
                                @if($review->status==='declined')
                                    @livewire('assignments.reviewer.reviewer-assignment-decline-reason', ['review_id' => $review_id])
                                @endif
                            @endif
                            
                            @if ($current_page ==='recommendation')
                                @if($review->status==='completed')
                                    @livewire('assignments.reviewer.reviewer-assignment-review', ['review_id' => $review_id])
                                @endif
                            @endif
                        </div>

                    </div>
                </div>
            </div>
            <!-- /Second column -->

        </div>
        <!-- Second column -->
          <div class="col-12 col-lg-3">
                <!-- Pricing Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        @if($review->status ==='pending')
                        <button  type="button" class="btn btn-success mt-2" data-bs-toggle="modal" data-bs-target="#{{ $acceptModalId }}">
                            {{ __('label.accept_review') }}
                        </button><br>
                        
                        <button  type="button" class="btn btn-danger mt-2" data-bs-toggle="modal" data-bs-target="#{{$modalId}}" wire:click="openModal">
                            {{ __('label.decline_review') }}
                        </button><br>
                        @endif

                        @if($review->status ==='accepted')
                        <button  type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#{{ $recommendationModalId }}">
                            {{ __('label.make_recommendation') }}
                        </button><br>
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

    <div class="modal fade" id="{{ $acceptModalId }}" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <form wire:submit.prevent="acceptReview">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel2">{{ __('label.accept_review') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="alert alert-warning" role="alert">
                                <h6 class="alert-heading mb-1"><span>{{ __('label.warning') }}!<span></h6>
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

    
    <div class="modal fade" id="{{$modalId}}" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel2">{{ __('label.decline_review') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="declineReview">
                        <div class="row">
                            <div class="row g-3">
                                <div class="col-sm-12 fv-plugins-icon-container">
                                    <label class="form-label" for="">{{ __('label.comment',locale:'fa') }}</label>
                                    <textarea  id="decline_reason_fa" class="form-control @error('decline_reason_fa') is-invalid @enderror" placeholder="" wire:model.lazy="decline_reason_fa" rows="4"></textarea>
                                        @error('decline_reason_fa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
                                    
                                </div>
                            </div>
                            <div class="row g-3 mt-3">
                                <div class="col-sm-12 fv-plugins-icon-container">
                                    <label class="form-label" for="">{{ __('label.comment',locale:'en') }}</label>
                                    <textarea  id="comments_to_editor_en" class="form-control @error('decline_reason_en') is-invalid @enderror" placeholder="" wire:model.lazy="decline_reason_en" rows="4"></textarea>
                                        @error('decline_reason_en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('label.close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('label.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- ---------make a recommendation -------------------- -->
    <div class="modal fade" id="{{$recommendationModalId}}" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel2">{{ __('label.make_recommendation') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="reviewRecommendationStore">
                        <div class="row">

                            <div class="row g-3">
                                <div class="col-sm-12 fv-plugins-icon-container">
                                    <label for="formFileMultiple" class="form-label">{{ __('label.file_upload') }}</label>
                                    <div class="input-group mb-2">
                                        <input type="file" class="form-control @error('file.*') is-invalid @enderror" id="fileInput" multiple wire:model.lazy="file" multiple>
                                         
                                    </div>
                                    @error('file.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    <div id="fileError" class="invalid-feedback d-block" style="display:none;"></div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-sm-12 fv-plugins-icon-container">
                                    <label class="form-label" for="">{{ __('label.comment',locale:'fa') }}</label>
                                    <textarea  id="recommendation_comments_fa" class="form-control @error('recommendation_comments_fa') is-invalid @enderror" placeholder="" wire:model.lazy="recommendation_comments_fa" rows="4"></textarea>
                                        @error('recommendation_comments_fa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
                                    
                                </div>
                            </div>
                            <div class="row g-3 mt-3">
                                <div class="col-sm-12 fv-plugins-icon-container">
                                    <label class="form-label" for="">{{ __('label.comment',locale:'en') }}</label>
                                    <textarea  id="recommendation_comments_en" class="form-control @error('recommendation_comments_en') is-invalid @enderror" placeholder="" wire:model.lazy="recommendation_comments_en" rows="4"></textarea>
                                        @error('recommendation_comments_en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="row g-3 mt-3">
                                <div class="col-sm-12 fv-plugins-icon-container">
                                    <label class="form-label" for="multicol-country">{{ __('label.recommendation') }}</label>
                                    <select id="multicol-country" wire:ignore class="form-control @error('recommendation') is-invalid @enderror"  wire:model.lazy="recommendation" >
                                        <option value="">{{ __('label.select') }}</option>
                                        <option value="accept" >{{ __('label.accept_skip_review') }}</option>
                                        <option value="reject">{{ __('label.reject_submission') }}</option>
                                        <option value="revision_required">{{ __('label.revision_required') }}</option>
                                    </select>
                                    @error('recommendation') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('label.close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('label.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
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