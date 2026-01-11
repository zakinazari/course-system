<div>
   <div class="mt-3">
        <span>{{ __('label.reviewer_declined_reason') }}: 
            @if($review->round == $submission->round && $submission->status !='published')      
            <a class="" href="javascript:void(0);" data-bs-toggle="modal"  wire:click="editReason">
            <i class="bx bx-edit-alt me-1 text-success"></i></a>
            <a class="" href="javascript:void(0);"  onclick="confirmDelete({{ $review->id }},'{{$table_name}}')">
                    <i class="bx bx-trash me-1 text-danger"></i></a>
            @endif
        </span>
        <ul class="list-group">
            <li class="list-group-item d-flex justify-content-between align-items-center">
                @if(App::getLocale()==='en')
                    {{ $review->decline_reason_en }} 
                @else
                    {{ $review->decline_reason_fa }} 
                @endif
            </li>
        </ul>
    </div>

<div class="modal fade" id="{{$modalId}}" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@if($editMode) {{ __('label.editing') }}  @else {{ __('label.adding') }} @endif {{ __('label.reviewer_declined_reason') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <form @if($editMode) wire:submit.prevent="declineReviewUpdate" @else wire:submit.prevent="declineReviewUpdate" @endif>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-sm-12 fv-plugins-icon-container">
                                <label class="form-label" for="">{{ __('label.comment',locale:'fa') }}</label>
                                <textarea  id="comments_fa" class="form-control @error('decline_reason_fa') is-invalid @enderror" placeholder="" wire:model.lazy="decline_reason_fa" rows="4"></textarea>
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
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" >{{ __('label.close') }}</button>
                        <button type="submit" class="btn btn-primary">@if($editMode) {{ __('label.update') }}  @else {{ __('label.save') }} @endif</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
