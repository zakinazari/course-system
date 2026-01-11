<div>
    @if($section ==='details')
        <div class="card-body">
            <h5 class="card-title">{{ __('label.title') }}</h5>
            <p class="card-text">
                @if(App::getLocale() == 'en')
                    {{ $submission->title_en }}
                @else
                    {{ $submission->title_fa }}
                @endif
            </p>

            <br>

            <h5 class="card-title">{{ __('label.abstract') }}</h5>
            <p class="card-text">
                
                @if(App::getLocale() == 'en')
                    {!! $submission->abstract_en !!}
                @else
                    {!! $submission->abstract_fa !!}
                @endif
            </p>

            <h5 class="card-title">{{ __('label.keywords') }}</h5>
            <p class="card-text">
                @if(App::getLocale() == 'en')
                    @foreach($keywords_en as $i => $keyword)
                        <span class="badge bg-label-secondary me-1">{{ $keyword }}</span>
                    @endforeach
                @else
                    @foreach($keywords_fa as $i => $keyword)
                        <span class="badge bg-label-secondary me-1">{{ $keyword }}</span>
                    @endforeach
                @endif
            </p>
                    
        </div>
    @elseif($section ==='files')
        @if($uploaded_files->count())
            <div class="mt-3">
                <span>{{ __('label.uploaded_files') }}</span>
                <ul class="list-group">
                    @foreach($uploaded_files as $f)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
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
                                <span>{{ $f->original_name }}</span>&nbsp;&nbsp;&nbsp;<small>({{ __('label.round') }} {{ $f->round }})</small>
                            </span>
                            
                            <div class="d-flex gap-2">
                                <a wire:click.prevent="downloadFile({{ $f->id }})" class="btn btn-sm btn-secondary text-white">
                                      <i class="bx bx-download me-1"></i> {{ __('label.download') }}
                                </a>
                            </div>

                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    @elseif($section ==='contributors')

        <h5 class="card-title">{{ __('label.contributors') }}</h5>
        @foreach($authors as $author)
            <div class="card-body">
                
                <h5 class="pb-2 border-bottom mb-3 small"><i class="fa fa-user mb-2"></i> 
                            @if(App::getLocale() == 'en')
                                {{ $author->given_name_en }} {{ $author->family_name_en }}
                            @else
                                {{ $author->given_name_fa }} {{ $author->family_name_fa }}
                            @endif
                            <span class="text-muted">
                            (@if(App::getLocale()=='en')
                                {{ $author->type?->type_name_en }}
                            @else
                                {{ $author->type?->type_name_fa }}
                            @endif)
                            </span>
                        </h5>
                <div class="info-container small">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <span class="fw-bold me-2">{{ __('label.given_name') }}: </span>
                            <span class="text-muted">
                                @if(App::getLocale() == 'en')
                                    {{ $author->given_name_en }}
                                @else
                                    {{ $author->given_name_fa }}
                                @endif
                            </span>
                        </li>
                        <li class="mb-2">
                            <span class="fw-bold me-2">{{ __('label.family_name') }}: </span>
                            <span class="text-muted">
                                @if(App::getLocale() == 'en')
                                    {{ $author->family_name_en }}
                                @else
                                    {{ $author->family_name_fa }}
                                @endif
                            </span>
                        </li>
                        <li class="mb-2">
                            <span class="fw-bold me-2">{{ __('label.education_degree') }}: </span>
                            <span class="text-muted">
                                @if(App::getLocale() == 'en')
                                    {{ $author->educationDegree?->name_en }}
                                @else
                                    {{ $author->educationDegree?->name_fa }}
                                @endif
                            </span>
                        </li>
                        <li class="mb-2">
                            <span class="fw-bold me-2">{{ __('label.academic_rank') }}: </span>
                            <span class="text-muted">
                                @if(App::getLocale() == 'en')
                                    {{ $author->academicRank?->name_en }}
                                @else
                                    {{ $author->academicRank?->name_fa }}
                                @endif
                            </span>
                        </li>
                        <li class="mb-2">
                            <span class="fw-bold me-2">{{ __('label.department') }}:</span>
                            <span class="text-muted">
                                @if(App::getLocale() == 'en')
                                    {{ $author->department_en }}
                                @else
                                    {{ $author->department_fa }}
                                @endif
                            </span>
                        </li>
                        <li class="mb-2">
                            <span class="fw-bold me-2">{{ __('label.preferred_research_area') }}:</span>
                            <span class="text-muted">
                                @if(App::getLocale() == 'en')
                                    {{ $author->preferred_research_area_en }}
                                @else
                                    {{ $author->preferred_research_area_fa }}
                                @endif
                            </span>
                        </li>
                        <li class="mb-2">
                            <span class="fw-bold me-2">{{ __('label.email') }}:</span>
                            <span class="text-muted">{{ $author->email }}</span>
                            </li>
                            <li class="mb-2">
                            <span class="fw-bold me-2">{{ __('label.phone') }}:</span>
                            <span class="text-muted">{{ $author->phone_no }}</span>
                        </li>
                 
                        <li class="mb-2">
                            <span class="fw-bold me-2">{{ __('label.affiliation') }}:</span>
                            <span class="text-muted">
                                @if(App::getLocale() == 'en')
                                    {{ $author->affiliation_en }}
                                @else
                                    {{ $author->affiliation_fa }}
                                @endif
                            </span>
                        </li>
                        <li class="mb-2">
                            <span class="fw-bold me-2">{{ __('label.country') }}:</span>
                            <span class="text-muted">
                                @if(App::getLocale()=='en')
                                    {{ $author->country?->country_name_en }}
                                @else
                                    {{ $author->country?->country_name_fa }}
                                @endif
                            </span>
                        </li>
                        <li class="mb-2">
                            <span class="fw-bold me-2">{{ __('label.province') }}:</span>
                            <span class="text-muted">
                                @if(App::getLocale()=='en')
                                    {{ $author->province?->name_en }}
                                @else
                                    {{ $author->province?->name_fa }}
                                @endif
                            </span>
                        </li>
                        <li class="mb-2">
                            <span class="fw-bold me-2">{{ __('label.city') }}:</span>
                            <span class="text-muted">
                                @if(App::getLocale()=='en')
                                    {{ $author->city_en }}
                                @else
                                    {{ $author->city_fa }}
                                @endif
                            </span>
                        </li>
                    </ul>
                </div>

            </div>
        @endforeach
    @elseif($section==='comment')
    <h5 class="card-title">{{ __('label.comments_for_editor') }}</h5>
         <i class="fa fa-comment mb-2"></i>
            {{ __('label.comments_for_editor') }} &nbsp;
            </span><br>
            <small class="card-text ">
                @if(App::getLocale() == 'en')
                    {{ $submission->comments_to_editor_en }}
                @elseif(App::getLocale() == 'fa')
                    {{ $submission->comments_to_editor_fa }}
                @endif
            </small>
    @endif
</div>
