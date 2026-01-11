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
                        <input type="text" class="form-control" placeholder="{{ __('label.name') }}" wire:model="search.identity">
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
                            <th>{{ __('label.members') }}</th>
                            <th>{{ __('label.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($scientific_board as $i => $board)
                        <tr>
                            <td>{{ ($scientific_board->currentPage() - 1) * $scientific_board->perPage() + $i + 1 }}</td>
                            <td>
                                 @if(App::getLocale()=='fa') {{ $board->name_fa }} @else {{ $board->name_en }} @endif 
                            </td>
                            <td>
                                <button type="button" class="btn btn-success btn-icon rounded-pill" wire:click="showMembers({{ $board->id }})">
                                    <i class="bx bx-show"></i>
                                </button>
                           </td>
                           
                            <td>
                                <div class="dropdown position-static">
                                    <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if(edit(Auth::user()->role_ids,$active_menu_id))
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="edit({{ $board->id }})"
                                            ><i class="bx bx-edit-alt me-1 text-success"></i>{{ __('label.edit') }}</a>
                                        @endif
                                        @if(delete(Auth::user()->role_ids,$active_menu_id))
                                            <a class="dropdown-item " href="javascript:void(0);"  onclick="confirmDelete({{ $board->id }},'{{$table_name}}')"
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
                {{ $scientific_board->links() }}
            </div>
        </div>
    </div>
    
    <!--/ Bootstrap Table with Header Dark -->
    <div class="modal fade" id="{{$modalId}}" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@if($editMode) {{ __('label.editing') }}  @else {{ __('label.adding') }} @endif {{ $active_menu?->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <form wire:submit.prevent="{{ $editMode ? 'update' : 'store' }}">
                    <div class="modal-body">

                        {{-- Board name --}}
                        <div class="row">
                            <div class="col mb-3">
                                <label class="form-label">{{ __('label.name',locale:'fa') }}</label>
                                <input type="text"
                                    class="form-control @error('name_fa') is-invalid @enderror"
                                    wire:model.defer="name_fa">
                                @error('name_fa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col mb-3">
                                <label class="form-label">{{ __('label.name', locale:'pa') }}</label>
                                <input type="text"
                                    class="form-control @error('name_en') is-invalid @enderror"
                                    wire:model.defer="name_en">
                                @error('name_en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <hr>

                        @foreach($members as $index => $member)
                            <div class="row border rounded p-3 mb-3">

                                <div class="col-md-5">
                                    <label class="form-label">{{ __('label.name',locale:'fa') }}</label>
                                    <input type="text"
                                        class="form-control"
                                        wire:model.defer="members.{{ $index }}.name_fa">
                                    @error("members.$index.name_fa")
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-5">
                                    <label class="form-label">{{ __('label.name', locale:'pa') }}</label>
                                    <input type="text"
                                        class="form-control"
                                        wire:model.defer="members.{{ $index }}.name_en">
                                    @error("members.$index.name_en")
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button"
                                            class="btn btn-danger"
                                            wire:click="removeMember({{ $index }})"
                                            @if(count($members) === 1) disabled @endif>
                                        {{ __('label.delete') }}
                                    </button>
                                </div>
                            </div>
                        @endforeach

                        <button type="button"
                                class="btn btn-primary"
                                wire:click="addMember">
                            ➕ {{ __('label.add_member') }}
                        </button>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('label.close') }}
                        </button>

                        <button type="submit" class="btn btn-primary">
                            {{ $editMode ? __('label.update') : __('label.save') }}
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="modalShowFiles" tabindex="-1" style="display: none;" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel1">{{ __('label.members') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($existing_members)
                    <table class="table">
                        <thead class="table-dark">
                            <tr>
                                <th>{{ __('label.NO') }}</th>
                                <th>{{ __('label.name') }}</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach($existing_members as $i => $member)
                            <tr>
                                <td style="width:10%;">{{ $i + 1 }}</td>
                                <td>
                                    @if(App::getLocale()=='fa') {{ $member->name_fa }} @else {{ $member->name_en }} @endif 
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                {{ __('label.close') }}
            </button>
            </div>
        </div>
        </div>
    </div>
</div>
