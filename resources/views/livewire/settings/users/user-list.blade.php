
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
    {{ $active_menu?->name }}  
    </h4>
    <!-- end header -->


    <div class="card">
       
        <div class="card-header d-flex justify-content-between align-items-center">

            <h5 class="card-title mb-0">{{ $active_menu?->name }}</h5>

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
                        <input type="text" class="form-control" placeholder="{{ __('label.name') }}/{{ __('label.email') }}" wire:model="search.identity">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('label.access_role') }}</label>
                        <select class="form-control" wire:model.live="search.role">
                            <option value="">{{ __('label.all') }}</option>
                                <option value="Admin">Admin</option>
                                <option value="Author">Author</option>
                                <option value="Reviewer">Reviewer</option>
                        </select>
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
                            <th>{{ __('label.name') }}</th>
                            <th>{{ __('label.email') }}</th>
                            <th>{{ __('label.role_name') }}</th>
                            <th>{{ __('label.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($users as $i => $user)
                        <tr>
                            <td>{{ ($users->currentPage() - 1) * $users->perPage() + $i + 1 }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->roles->pluck('role_name')->join(', ') }}</td>
                            <td>
                                <div class="dropdown position-static">
                                    <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if(edit(Auth::user()->role_ids,$active_menu_id))
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="edit({{ $user->id }})"
                                            ><i class="bx bx-edit-alt me-1 text-success"></i>{{ __('label.edit') }}</a>
                                        @endif
                                        @if(delete(Auth::user()->role_ids,$active_menu_id) && Auth::user()->isAdmin())
                                            <a class="dropdown-item " href="javascript:void(0);"  onclick="confirmDelete({{ $user->id }},'{{$table_name}}')"
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
                {{ $users->links() }}
            </div>
        </div>
    </div>
    
    <!--/ Bootstrap Table with Header Dark -->
    <div class="modal fade" id="{{$modalId}}" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@if($editMode) {{ __('label.editing') }}  @else {{ __('label.adding') }} @endif  {{ $active_menu?->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <form @if($editMode) wire:submit.prevent="update" @else wire:submit.prevent="store" @endif>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.name') }}</label>
                                <input type="text" id="nameBasic" class="form-control @error('name') is-invalid @enderror" wire:model.lazy="name">
                                @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                             <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.email') }}</label>
                                <input type="email" id="nameBasic" class="form-control @error('email') is-invalid @enderror" wire:model.lazy="email">
                                @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <!-- Password -->
                            <div class="col mb-3">
                                <label class="form-label">{{ __('label.password') }}</label>
                                <div class="input-group">
                                    <input 
                                        type="{{ $show_password ? 'text' : 'password' }}" 
                                        class="form-control @error('password') is-invalid @enderror" 
                                        wire:model.lazy="password"
                                    >
                                    <span class="input-group-text" style="cursor:pointer;" wire:click="togglePassword">
                                        @if($show_password)
                                            <i class="bx bx-show"></i>
                                        @else
                                            <i class="bx bx-hide"></i>
                                        @endif
                                    </span>
                                </div>
                                @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            
                            <!-- Confirm Password -->
                            <div class="col mb-3">
                                <label class="form-label">{{ __('label.confirm_password') }}</label>
                                <div class="input-group">
                                    <input 
                                        type="{{ $show_confirm_password ? 'text' : 'password' }}" 
                                        class="form-control @error('confirm_password') is-invalid @enderror" 
                                        wire:model.lazy="confirm_password"
                                    >
                                    <span class="input-group-text" style="cursor:pointer;" wire:click="toggleConfirmPassword">
                                        @if($show_confirm_password)
                                            <i class="bx bx-show"></i>
                                        @else
                                            <i class="bx bx-hide"></i>
                                        @endif
                                    </span>
                                </div>
                                @error('confirm_password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                         <div class="col-12">
                        
                          <div class="table-responsive">
                            <table class="table table-flush-spacing">
                              <tbody>
                                <tr>
                                    <td class="text-nowrap fw-medium">
                                        <label class="form-check-label" for="selectAll">{{ __('label.access_role') }} </label>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <div class="form-check me-3 me-lg-5">
                                                <input class="form-check-input" type="checkbox" id="selectAll"
                                                    wire:model="check_all"
                                                    wire:change="toggleSelectAll">
                                                <label class="form-check-label" for="selectAll">{{ __('label.all') }} </label>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                @foreach($access_roles as $role)
                                    <tr wire:key="role-{{ $role->id }}">
                                        <td class="text-nowrap fw-medium">{{ $role->role_name }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <div class="form-check me-3 me-lg-5">
                                                    <input 
                                                        type="checkbox"
                                                        wire:model="role_ids"
                                                        value="{{ $role->id }}"
                                                        id="role_{{ $role->id }}"
                                                        class="form-check-input" 
                                                    />
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                              </tbody>
                            </table>
                                @error('role_ids')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                          </div>
                          <!-- Permission table -->
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


