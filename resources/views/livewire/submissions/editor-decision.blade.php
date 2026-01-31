<div>
      <h5 class="mb-4">{{ __('label.editor_decision') }}</h5>
    <div>
    @if(!empty($editor_decision))
        @foreach($editor_decision as $decision)
            <label class="form-check-label pt-1" for="companyName">    
                <span class="mb-1 h6">
                <div class="form-check mb-3">

                <i class="fa fa-user mb-2"></i>

                {{ $decision->editor?->name }} &nbsp;

                @if($decision->decision=='accept')
                <span class="badge rounded-2 badge-warning bg-label-success fs-tiny py-1">{{ $decision?->decision }}</span>
                @elseif($decision->decision=='reject')
                <span class="badge rounded-2 badge-danger bg-label-danger fs-tiny py-1">{{ $decision->decision }}</span>
                @elseif($decision->decision=='revision_required')
                <span class="badge rounded-2 badge-warning bg-label-warning fs-tiny py-1">{{ $decision->decision }}</span>
                @endif
                </span> &nbsp; 
                @if($round == $submission->round && $submission->status !='published' && Auth::user()->isAdmin() && Auth::user()->id ==$decision->editor_id)     
                <a class="" href="javascript:void(0);" wire:click="editDecision({{ $decision->id }})">
                    <i class="bx bx-edit-alt me-1 text-success"></i></a>
                <a class="" href="javascript:void(0);"  onclick="confirmDelete({{ $decision->id }},'{{$table_name}}')">
                    <i class="bx bx-trash me-1 text-danger"></i></a>
                @endif
                <br>
                    <small class="text-muted font-body">
                        @if(App::getLocale()==='en')
                            {{ $decision->comments_en }}
                        @else
                            {{ $decision->comments_fa }}
                        @endif
                    </small>
                </label>
            </div>

            @endforeach
        @endif
    </div>

    <div class="modal fade" id="{{$modalId}}" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@if($editMode) {{ __('label.editing') }}  @else {{ __('label.adding') }} @endif {{ __('label.editor_decision') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <form @if($editMode) wire:submit.prevent="updateDecision" @else wire:submit.prevent="updateDecision" @endif>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-sm-12 fv-plugins-icon-container">
                                <label class="form-label" for="">{{ __('label.comment',locale:'fa') }}</label>
                                <textarea  id="comments_fa" class="form-control @error('comments_fa') is-invalid @enderror" placeholder="" wire:model.lazy="comments_fa" rows="4"></textarea>
                                    @error('comments_fa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
                                
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-sm-12 fv-plugins-icon-container">
                                <label class="form-label" for="">{{ __('label.comment',locale:'en') }}</label>
                                <textarea  id="comments_to_editor_en" class="form-control @error('comments_en') is-invalid @enderror" placeholder="" wire:model.lazy="comments_en" rows="4"></textarea>
                                    @error('comments_en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
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
