
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
                        <label class="form-label">{{ __('label.menu_name') }}, {{ __('label.url') }}</label>
                        <input type="text" class="form-control"
                            placeholder="{{ __('label.menu_name') }}, {{ __('label.url') }}"
                            wire:model.defer="search.identity">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">{{ __('label.main_menu') }}</label>
                        <select class="form-control"
                            wire:model.defer="search.main_menu"
                            wire:change="loadSubMenus($event.target.value)">
                            <option value="">{{ __('label.all') }}</option>
                            @foreach($main_menus as $mm)
                                <option value="{{ $mm->id }}">
                                        {{ $mm->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">{{ __('label.sub_menu') }}</label>
                        <select class="form-control" wire:model.defer="search.sub_menu">
                            <option value="">{{ __('label.all') }}</option>
                            @foreach($sub_menus as $sm)
                                <option value="{{ $sm->id }}">
                                   {{ $sm->name }}
                                </option>
                            @endforeach
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
                            <th>{{ __('label.menu_name') }}</th>
                            <th>{{ __('label.url') }}</th>
                            <!-- <th>{{ __('label.icon') }}</th> -->
                            <th>{{ __('label.menu_type') }}</th>
                            <th>{{ __('label.section') }}</th>
                            <th>{{ __('label.grand_parent') }}</th>
                            <th>{{ __('label.parent') }}</th>
                            <th>{{ __('label.order') }}</th>
                            <th>{{ __('label.status') }}</th>
                            <th>{{ __('label.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($menus as $i => $menu)
                        <tr>
                            <td>{{ ($menus->currentPage() - 1) * $menus->perPage() + $i + 1 }}</td>
                            <td>
                                {{ $menu->name }}
                            </td>
                            <td>{{ $menu->url }}</td>
                            <!-- <td>{{ $menu->icon }}</td> -->
                            <td>
                                {{ $menu->type?->type_name }}
                            </td>
                            <td>
                                {{ $menu->section?->section_name }}
                            </td>
                            <td>

                                {{ $menu->grandParent?->name }}

                            </td>
                            <td>
                                {{ $menu->parent?->name }}
                            </td>
                            <td>{{ $menu->order }}</td>
                            <td>
                                @if($menu->status ==1 ) 
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
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="edit({{ $menu->id }})"
                                            ><i class="bx bx-edit-alt me-1 text-success"></i>{{ __('label.edit') }}</a>
                                        @endif
                                        @if(delete(Auth::user()->role_ids,$active_menu_id))
                                            <a class="dropdown-item " href="javascript:void(0);"  onclick="confirmDelete({{ $menu->id }},'{{ $table_name }}')"
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
                {{ $menus->links() }}
            </div>

        </div>
    </div>
    
    <!--/ Bootstrap Table with Header Dark -->
    <div class="modal fade modal-lg" id="{{$modalId}}" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@if($editMode) {{ __('label.editing') }}  @else {{ __('label.adding') }} @endif  {{ $active_menu?->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <form @if($editMode) wire:submit.prevent="update" @else wire:submit.prevent="store" @endif>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.menu_name',locale:'en') }}</label>
                                <input type="text" id="nameBasic" class="form-control @error('name_en') is-invalid @enderror" wire:model.lazy="name_en">
                                @error('name_en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label class="form-label" for="multicol-country">{{ __('label.menu_type') }}</label>
                                <select id="multicol-country" wire:ignore class="form-control @error('menu_type') is-invalid @enderror"  wire:model.lazy="menu_type" >
                                    <option value="">{{ __('label.select') }}</option>
                                    @foreach($menu_types as $type)
                                        <option value="{{ $type->id }}">
                                            {{ $type->type_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('menu_type') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            @if($show_parent)
                                <div class="col-md-12" id="div_parent">
                                    <label class="form-label">{{ __('label.parent') }}</label>
                                    <select class="form-control @error('parent') is-invalid @enderror" wire:model="parent">
                                        <option value="">{{ __('label.select') }}</option>
                                        @foreach($parents as $m)
                                            <option value="{{ $m->id }}">
                                                    {{ $m->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('parent') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.url') }}</label>
                                <input type="text" id="nameBasic" class="form-control @error('url') is-invalid @enderror" wire:model.lazy="url">
                                @error('url') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.icon') }}</label>
                                <input type="text" id="nameBasic" class="form-control @error('icon') is-invalid @enderror" wire:model.lazy="icon">
                                @error('icon') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="category">{{ __('label.category') }}</label>
                                <select id="category" class="form-control @error('category') is-invalid @enderror"  wire:model.lazy="category">
                                    <option value="">{{ __('label.select') }}</option>
                                    <option value="1">{{ __('label.is_just_viewed') }}</option>
                                    <option value="2">{{ __('label.has_operations') }}</option>
                                    <option value="3">{{ __('label.has_operations_confirm') }}</option>
                                </select>
                                @error('category') <div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.order') }}</label>
                                <input type="text" id="nameBasic" class="form-control @error('order') is-invalid @enderror" wire:model.lazy="order">
                                @error('order') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label class="form-label" for="multicol-country1">{{ __('label.section') }}</label>
                                <select id="multicol-country1" class="form-control select2 @error('menu_section') is-invalid @enderror"  wire:model.lazy="menu_section" >
                                    <option value="">{{ __('label.select') }}</option>
                                    @foreach($menu_sections as $section)
                                        <option value="{{ $section->id }}">
                                            {{ $section->section_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('menu_section') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label d-block">{{ __('label.status') }}</label>
                                <div class="form-check form-check-inline">
                                    <input name="status" 
                                        class="form-check-input" 
                                        type="radio" 
                                        id="status-active" 
                                        value="1" 
                                        
                                        wire:model.lazy="status"  @checked($status == 1 || is_null($status))>
                                    <label class="form-check-label" for="status-active">{{ __('label.active') }}</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input name="status" 
                                        class="form-check-input" 
                                        type="radio" 
                                        id="status-inactive" 
                                        value="0" 
                                        wire:model.lazy="status"  @checked($status == 0)>
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
