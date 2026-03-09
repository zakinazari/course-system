
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
       
        <div class="card-header">
      
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="card-title mb-2 mb-md-0">
                    {{ $active_menu?->name }}
                </h5>
            
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <!-- Export Button -->
                    

                    <!-- Add New Record Button -->
                    @if(add(Auth::user()->role_ids,$active_menu_id))
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#{{$modalId}}" wire:click="openModal">
                            <i class="bi bi-plus-lg"></i> {{ __('label.add_new_record') }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
        <hr>
        <div class="table-responsive text-nowrap">
 
            <div class="mb-3 px-3">
                <form wire:submit.prevent="applySearch" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <input type="text" class="form-control" placeholder="{{ __('label.branch_name') }}" wire:model="search.name">
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
                            <th>{{ __('label.branch_name') }}</th>
                            <th>{{ __('label.branch_code') }}</th>
                            <th>{{ __('label.branch_location') }}</th>
                            <th>{{ __('label.status') }}</th>
                            <th>{{ __('label.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($branches as $i => $branch)
                        <tr>
                            <td>{{ ($branches->currentPage() - 1) * $branches->perPage() + $i + 1 }}</td>
                            <td>{{ $branch->name }}</td>
                            <td>{{ $branch->code }}</td>
                            <td>{{ $branch->location }}</td>
                            <td>
                                @if($branch->status ==='active' ) 
                                    <span class="badge rounded-pill bg-success">{{ __('label.active') }}</span>
                                @else
                                    <span class="badge rounded-pill bg-danger">{{ __('label.inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown position-static">
                                    <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if(edit(Auth::user()->role_ids,$active_menu_id))
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="edit({{ $branch->id }})"
                                            ><i class="bx bx-edit-alt me-1 text-success"></i>{{ __('label.edit') }}</a>
                                        @endif
                                        @if(delete(Auth::user()->role_ids,$active_menu_id))
                                            <a class="dropdown-item " href="javascript:void(0);"  onclick="confirmDelete({{ $branch->id }},'{{$table_name}}')"
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
                {{ $branches->links() }}
            </div>
        </div>
    </div>
    
    <!--/ Bootstrap Table with Header Dark -->
    <div class="modal fade" id="{{$modalId}}" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog" branch="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@if($editMode) {{ __('label.editing') }}  @else {{ __('label.adding') }} @endif @if(App::getLocale() =='en') {{ $active_menu?->name_en }} @elseif(App::getLocale()=='fa') {{ $active_menu?->name }} @endif</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <form @if($editMode) wire:submit.prevent="update" @else wire:submit.prevent="store" @endif>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.branch_name') }} <span style="color:red;">*</span></label>
                                <input type="text" id="nameBasic" class="form-control @error('name') is-invalid @enderror" wire:model.lazy="name">
                                @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.branch_code') }} <span style="color:red;">*</span></label>
                                <input type="text" id="nameBasic" class="form-control @error('code') is-invalid @enderror" wire:model.lazy="code">
                                @error('code') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.branch_location') }}</label>
                                <input type="text" id="nameBasic" class="form-control @error('location') is-invalid @enderror" wire:model.lazy="location">
                                @error('location') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                               <label class="form-label d-block">{{ __('label.status') }}</label>
                                <div class="form-check form-check-inline">
                                    <input name="status" 
                                        class="form-check-input" 
                                        type="radio" 
                                        id="status-active" 
                                        value="active" 
                                        
                                        wire:model.lazy="status"  @checked($status == 'active' || is_null($status))>
                                    <label class="form-check-label" for="status-active">{{ __('label.active') }}</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input name="status" 
                                        class="form-check-input" 
                                        type="radio" 
                                        id="status-inactive" 
                                        value="inactive" 
                                        wire:model.lazy="status"  @checked($status ==='inactive')>
                                    <label class="form-check-label" for="status-inactive">{{ __('label.inactive') }}</label>
                                </div>
                                @error('status') 
                                    <div class="invalid-feedback d-block">{{ $message }}</div> 
                                @enderror
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


