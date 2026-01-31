<div>
   <div class="mt-3">
        <span>{{ __('label.recommendation') }}:  (@if($decision->recommendation=='accept')
        <span class="badge rounded-2 badge-warning bg-label-success fs-tiny py-1">{{ $decision->recommendation }}</span>
        @elseif($decision->recommendation=='reject')
        <span class="badge rounded-2 badge-danger bg-label-danger fs-tiny py-1">{{ $decision->recommendation }}</span>
        @elseif($decision->recommendation=='revision_required')
        <span class="badge rounded-2 badge-warning bg-label-warning fs-tiny py-1">{{ $decision->recommendation }}</span>
        @endif)

            @if($review->round == $submission->round && $submission->status !='published')        
            <a class="" href="javascript:void(0);"  onclick="confirmDelete({{ $decision->id }},'{{$table_name}}')">
                    <i class="bx bx-trash me-1 text-danger"></i></a>
            @endif
        </span>
        
        <br>
        <small class="text-muted">
            @if(App::getLocale() ==='en')
                {{ $decision->comments_en }}
            @else
                {{ $decision->comments_fa }}
            @endif
        </small>
        </label>
        @if($uploaded_files->count() > 0)
        <div class="mt-3 px-1">
            <small>{{ __('label.uploaded_files') }}</small>
            <ul class="list-group small">
                @foreach($uploaded_files as $f)
                    <li class="list-group-item d-flex justify-content-between align-items-center small table-responsive">
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
    </div>

</div>



