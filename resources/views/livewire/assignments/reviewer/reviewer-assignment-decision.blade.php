<div>
    <h5 class="mb-4">{{ __('label.reviewer_recommendations') }}</h5>
    <div>
    @if(!empty($reviews))
        @foreach($reviews as $review)
            <label class="form-check-label pt-1" for="companyName" style="width:100%;">    
                <span class="mb-1 h6">
                <div class="form-check mb-3">

                    <i class="fa fa-user mb-2"></i>
                    
                    {{ $review?->reviewer?->name }} &nbsp;
                    @if($review?->decision?->recommendation=='accept')
                    <span class="badge rounded-2 badge-warning bg-label-success fs-tiny py-1">{{ $review?->decision?->recommendation }}</span>
                    @elseif($review?->decision?->recommendation=='reject')
                    <span class="badge rounded-2 badge-danger bg-label-danger fs-tiny py-1">{{ $review?->decision?->recommendation }}</span>
                    @elseif($review?->decision?->recommendation=='revision_required')
                    <span class="badge rounded-2 badge-warning bg-label-warning fs-tiny py-1">{{ $review?->decision?->recommendation }}</span>
                    @endif
                    </span><br>
                        <small class="text-muted">
                            @if(App::getLocale()==='en')
                                {{ $review?->decision?->comments_en }}
                            @else
                                {{ $review?->decision?->comments_fa }}
                            @endif
                        </small>
                    </label>
                </div>

                @if($review?->file->count() > 0)
                    <div class="mt-3 px-4">
                        <small>{{ __('label.uploaded_files') }}</small>
                        <ul class="list-group small">
                            @foreach($review?->file as $f)
                                <li class="list-group-item d-flex justify-content-between align-items-center small">
                                    @php
                                        $ext = strtolower(pathinfo($f->original_name, PATHINFO_EXTENSION));
                                        switch ($ext) {
                                            case 'pdf':
                                                $icon = '📄'; 
                                                break;
                                            case 'doc':
                                            case 'docx':
                                                $icon = '📝'; 
                                                break;
                                            default:
                                                $icon = '📁'; 
                                                break;
                                        }
                                    @endphp
                                    <span>
                                        <span class="me-2">{{ $icon }}</span>
                                        <span>{{ $f->original_name }}</span>
                                    </span>
                                    &nbsp;&nbsp;
                                    <div class="d-flex gap-2 small">
                                        <a wire:click.prevent="downloadFile({{ $f->id }})" class="btn btn-xs btn-secondary text-white small">
                                            <i class="bx bx-download me-1"></i>{{ __('label.download') }}
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <br>
            @endforeach
        @endif
    </div>
</div>
