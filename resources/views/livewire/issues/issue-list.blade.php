
<div>
    
    <!-- title -->
      @section('title',(($active_menu?->parent?->name_en ?? '') ? $active_menu?->parent?->name_en . '-' : ''
        ) . $active_menu?->name_en . ' | '.__('label.app_name'))
    <!-- end title -->
    <!-- start header -->
    <h4 class="py-3 breadcrumb-wrapper mb-4">
    @if(!empty($active_menu?->grandParent?->name_en))
    <span class="text-muted fw-light"> @if(App::getLocale()=='en') {{ $active_menu?->grandParent?->name_en }} @else {{ $active_menu?->grandParent?->name }}  @endif  /</span>
    @endif
    @if(!empty($active_menu?->parent?->name_en))
    <span class="text-muted fw-light"> @if(App::getLocale()=='en') {{ $active_menu?->parent?->name_en }} @else {{ $active_menu?->parent?->name }}  @endif /</span>
    @endif
    @if(App::getLocale()=='en') {{ $active_menu?->name_en }} @else {{ $active_menu?->name }}  @endif
    </h4>
    <!-- end header -->


    <div class="card">
       
        <div class="card-header d-flex justify-content-between align-items-center">

            <h5 class="card-title mb-0">@if(App::getLocale()=='en') {{ $active_menu?->name_en }} @else {{ $active_menu?->name }}  @endif</h5>

            <div class="d-flex align-items-center gap-2">
                
                @if(add(Auth::user()->role_ids,$active_menu_id))
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#{{$modalId}}" wire:click="openModal">
                            <i class="bi bi-plus-lg"></i> {{ __('label.add_new_record') }} 
                        </button>
                    </div>
                @endif
            </div>

        </div>
        <hr>
        <div class="table-responsive text-nowrap">
 
            <div class="mb-3 px-3">
                <form wire:submit.prevent="applySearch" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <input type="text" class="form-control" placeholder="{{ __('label.title') }}/{{ __('label.volume') }}/{{ __('label.number') }}" wire:model="search.identity">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">
                            {{ __('label.search') }}
                        </button>
                    </div>
                    
                </form>

                <!-- perPage -->
                <div class="d-flex align-items-center gap-1 mt-3 justify-content-end">
                    <span>{{ __('label.show') }}</span>
                    <select class="form-select w-auto" wire:model.live="perPage">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span>{{ __('label.entries') }}</span>
                </div>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead class="table-dark">
                        <tr>
                            <th>{{ __('label.NO') }}</th>
                            <th>{{ __('label.title') }}</th>
                            <th>{{ __('label.volume') }}</th>
                            <th>{{ __('label.number') }}</th>
                            <th>{{ __('label.status') }}</th>
                            <th>{{ __('label.date_published') }}</th>
                            <th>{{ __('label.cover_image') }}</th>
                            <th>{{ __('label.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($issues as $i => $issue)
                        <tr>
                            <td>{{ ($issues->currentPage() - 1) * $issues->perPage() + $i + 1 }}</td>
                            <td> @if(App::getLocale()=='en') {{ $issue->title_en }} @elseif(App::getLocale()=='fa') {{ $issue->title_fa }} @endif </td>
                            <td>{{ $issue->volume }}</td>
                            <td>{{ $issue->number }}</td>
                            <td>
                                @if($issue->status ==='published')
                                <span class="badge bg-label-success me-1">{{ $issue->status }}</span>
                                @else
                                <span class="badge bg-label-danger me-1">{{ $issue->status }}</span>
                                @endif
                            </td>
                            <td>
                                @if(App::getLocale() == 'en')
                                    {{ $issue->published_at ? \Carbon\Carbon::parse($issue->published_at)->format('Y/m/d') : '' }}
                                @else
                                    {{ $issue->published_at ? verta($issue->published_at)->format('Y/m/d') : '' }}
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                @if($issue->cover_image && \Storage::disk('public')->exists($issue->cover_image))
                                    <img src="{{ asset('storage/' . $issue->cover_image) }}" 
                                        alt="Cover Image" 
                                        class="img-fluid border rounded shadow-sm"
                                        style="width:60px; height:60px; object-fit:cover;">
                                @else
                                    <img src="{{ asset('images/default.png') }}" 
                                        alt="No Cover" 
                                        class="img-fluid border rounded shadow-sm"
                                        style="width:60px; height:60px; object-fit:cover;">
                                @endif
                            </td>

                            <td>
                                <div class="dropdown position-static">
                                    <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if(edit(Auth::user()->role_ids,$active_menu_id))
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="edit({{ $issue->id }})"
                                            ><i class="bx bx-edit-alt me-1 text-success"></i>{{ __('label.edit') }}</a>
                                        @endif
                                        @if(delete(Auth::user()->role_ids,$active_menu_id))
                                            <a class="dropdown-item " href="javascript:void(0);"  onclick="confirmDelete({{ $issue->id }},'{{$table_name}}')"
                                            ><i class="bx bx-trash me-1 text-danger"></i>{{ __('label.delete') }}</a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 justify-content-end px-3">
                {{ $issues->links() }}
            </div>
        </div>
    </div>
    
    <!--/ Bootstrap Table with Header Dark -->
    <div class="modal fade" id="{{$modalId}}" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@if($editMode) {{ __('label.editing') }}  @else {{ __('label.adding') }} @endif @if(App::getLocale() =='en') {{ $active_menu?->name_en }} @elseif(App::getLocale()=='fa') {{ $active_menu?->name }} @endif</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <form @if($editMode) wire:submit.prevent="update" @else wire:submit.prevent="store" @endif>
                    <div class="modal-body">

                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.volume') }}</label>
                                <input type="text" id="nameBasic" class="form-control @error('volume') is-invalid @enderror" wire:model.lazy="volume">
                                @error('volume') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                             <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.number') }}</label>
                                <input type="text" id="nameBasic" class="form-control @error('number') is-invalid @enderror" wire:model.lazy="number">
                                @error('number') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.title',locale:'fa') }}</label>
                                <input type="text" id="nameBasic" class="form-control @error('title_fa') is-invalid @enderror" wire:model.lazy="title_fa">
                                @error('title_fa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                             <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.title',locale:'en') }}</label>
                                <input type="text" id="nameBasic" class="form-control @error('title_en') is-invalid @enderror" wire:model.lazy="title_en">
                                @error('title_en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
    
                                @if($existing_cover_image)
                                    <div class="mb-2 text-center">
                                        <img src="{{ asset('storage/' . $existing_cover_image) }}" 
                                            alt="Cover Image" 
                                            class="rounded" 
                                            width="120" 
                                            height="120">
                                    </div>
                                @endif

                                <label for="formFile" class="form-label">{{ __('label.cover_image') }}</label>
                                <input 
                                    class="form-control @error('cover_image') is-invalid @enderror" 
                                    type="file" 
                                    id="formFile"  
                                    wire:model.lazy="cover_image"
                                >
                                @error('cover_image')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mt-4">
                                <div class="col-md-6 mb-3">
                                <label class="form-label d-block">{{ __('label.status') }}</label>
                                <div class="form-check form-check-inline">
                                    <input name="status" 
                                        class="form-check-input" 
                                        type="radio" 
                                        id="status-active" 
                                        value="published" 
                                        
                                        wire:model.lazy="status"  @checked($status == 'published' || is_null($status))>
                                    <label class="form-check-label" for="status-active">Publish</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input name="status" 
                                        class="form-check-input" 
                                        type="radio" 
                                        id="status-inactive" 
                                        value="unpublished" 
                                        wire:model.lazy="status"  @checked($status == 'unpublished')>
                                    <label class="form-check-label" for="status-inactive">Unpublish</label>
                                </div>
                                @error('status') 
                                    <div class="invalid-feedback d-block">{{ $message }}</div> 
                                @enderror
                            </div>
                            <div class="col mb-3">
                                <label class="form-label">{{ __('label.date_published') }}</label>
                                <input type="date" id="" class="form-control" placeholder="" wire:model.lazy="published_at">
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

<script>
    

</script>


