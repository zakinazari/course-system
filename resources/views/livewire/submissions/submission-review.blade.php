<div>
 <div class="col-sm-12 fv-plugins-icon-container">
        <h5 class="mb-4">{{ __('label.reviewers') }}</h5>
        <!-- <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('label.reviewers') }}</h5>
            <div class="d-flex align-items-center gap-2">
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#{{$modalId}}" wire:click="openModal">
                        <i class="bi bi-plus-lg"></i> {{ __('label.adding') }} {{ __('label.reviewer') }}
                    </button>
                </div>
            </div>
        </div>
        <hr> -->
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead class="table-dark">
                    <tr>
                        <th>{{ __('label.NO') }}</th>
                        <th>{{ __('label.reviewer') }}</th>
                        <th>{{ __('label.round') }}</th>
                        <th>{{ __('label.status') }}</th>
                        <th>{{ __('label.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                     @foreach($reviewers as $i => $reviewer)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $reviewer->reviewer?->name }}</td>
                            <td>{{ __('label.round') }} {{ $reviewer->round }}</td>
                            <td>
                                @if($reviewer->status=='pending')
                                <span class="badge bg-label-primary me-1" style="font-size:10px;">{{ $reviewer->status }}</span>
                                @elseif($reviewer->status=='accepted')
                                <span class="badge bg-label-success me-1" style="font-size:10px;">{{ $reviewer->status }}</span>
                                @elseif($reviewer->status=='completed')
                                <span class="badge bg-label-success me-1" style="font-size:10px;">{{ $reviewer->status }}</span>
                                @elseif($reviewer->status=='declined')
                                <span class="badge bg-label-danger me-1" style="font-size:10px;">{{ $reviewer->status }}</span>
                                @endif
                            </td>
                            <td>
                                @if($submission->round===$round && $submission->status!='published')
                                <div class="dropdown position-static">
                                    <button type="button" class="btn btn-icon  dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item " href="javascript:void(0);"  onclick="confirmDelete({{ $reviewer->id }},'{{ $table_name }}')"
                                        ><i class="bx bx-trash me-1 text-danger"></i>{{ __('label.delete') }}</a>
                                    </div>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade modal-lg" id="{{$modalId}}" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header ">
                    <h5 class="modal-title">@if($editMode) {{ __('label.editing') }}  @else {{ __('label.adding') }} @endif {{ __('label.reviewer') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <form @if($editMode) wire:submit.prevent="update" @else wire:submit.prevent="store" @endif>
                    <div class="modal-body">
                        <div class="row">
                           <div class="col mb-3">
                                <label class="form-label">{{ __('label.reviewers') }}</label>
                                <select id="reviewer_id" class="form-control @error('reviewer_id') is-invalid @enderror" wire:model.defer="reviewer_id">
                                    <option value="">{{ __('label.select') }}</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">
                                            {{ App::getLocale() === 'en' ? $user->name : $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('reviewer_id') 
                                    <div class="invalid-feedback d-block">{{ $message }}</div> 
                                @enderror
                            </div>
                            <div class="row g-3">
                                <div class="col-sm-12 fv-plugins-icon-container">
                                    <label class="form-label" for="">{{ __('label.comments_for_editor',locale:'fa') }}</label>
                                    <textarea  id="editor_message_fa" class="form-control @error('editor_message_fa') is-invalid @enderror" placeholder="" wire:model.lazy="editor_message_fa" rows="3"></textarea>
                                        @error('editor_message_fa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
                                    
                                </div>
                            </div>

                            <div class="row g-3 mt-3">
                                <div class="col-sm-12 fv-plugins-icon-container">
                                    <label class="form-label" for="">{{ __('label.comments_for_editor',locale:'en') }}</label>
                                    <textarea  id="editor_message_en" class="form-control @error('editor_message_en') is-invalid @enderror" placeholder="" wire:model.lazy="editor_message_en" rows="3"></textarea>
                                        @error('editor_message_en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">@if($editMode) {{ __('label.update') }}  @else {{ __('label.save') }} @endif</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" >{{ __('label.close') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
