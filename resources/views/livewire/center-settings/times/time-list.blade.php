
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

                    <div class="col-md-3" wire:ignore>
                        <label class="form-label">{{ __('label.shift') }}</label>
                        <select class="form-select select2" wire:model.defer="search.shift_id" id ="search_shift_id">
                           <option value="">{{ __('label.all') }}</option>
                           @foreach($shifts as $sh)
                                 <option value="{{ $sh->id }}"  wire:key="shift-search-{{ $sh->id }}">
                                    {{ $sh->name }}
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
                            <th>{{ __('label.start_time') }}</th>
                            <th>{{ __('label.end_time') }}</th>
                            <th>{{ __('label.shift') }}</th>
                            <th>{{ __('label.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($times as $i => $time)
                        <tr>
                            <td>{{ ($times->currentPage() - 1) * $times->perPage() + $i + 1 }}</td>
                            <td>{{ $time->start_time->format('h:i A') }}</td>
                            <td>{{ $time->end_time->format('h:i A') }}</td>
                            <td>{{ $time->shift?->name }}</td>
                            <td>
                                <div class="dropdown position-static">
                                    <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if(edit(Auth::user()->role_ids,$active_menu_id))
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="edit({{ $time->id }})"
                                            ><i class="bx bx-edit-alt me-1 text-success"></i>{{ __('label.edit') }}</a>
                                        @endif
                                        @if(delete(Auth::user()->role_ids,$active_menu_id))
                                            <a class="dropdown-item " href="javascript:void(0);"  onclick="confirmDelete({{ $time->id }},'{{$table_name}}')"
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
                {{ $times->links() }}
            </div>
        </div>
    </div>
    
    <!--/ Bootstrap Table with Header Dark -->
    <div class="modal fade" id="{{$modalId}}" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog modal-lg" branch="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@if($editMode) {{ __('label.editing') }}  @else {{ __('label.adding') }} @endif @if(App::getLocale() =='en') {{ $active_menu?->name_en }} @elseif(App::getLocale()=='fa') {{ $active_menu?->name }} @endif</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <form @if($editMode) wire:submit.prevent="update" @else wire:submit.prevent="store" @endif>
                    <div class="modal-body">
                        <div class="row">
                           <div class="col mb-3" wire:ignore>
                              <label class="form-label">{{ __('label.shift') }} <span style="color:red;">*</span></label>
                              <select class="form-select select2" wire:model.lazy="shift_id" id ="shift_id">
                                 <option value="">{{ __('label.select') }}</option>
                                 @foreach($shifts as $shift)
                                       <option value="{{ $shift->id }}"  wire:key="shift-add-edit-{{ $shift->id }}">
                                          {{ $shift->name }}
                                       </option>
                                 @endforeach
                              </select>
                           </div>
                           @error('shift_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                           @enderror
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.start_time') }} <span style="color:red;">*</span></label>
                                <input type="time" id="nameBasic" class="form-control @error('start_time') is-invalid @enderror" wire:model.lazy="start_time">
                                @error('start_time') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.end_time') }} <span style="color:red;">*</span></label>
                                <input type="time" id="nameBasic" class="form-control @error('end_time') is-invalid @enderror" wire:model.lazy="end_time">
                                @error('end_time') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
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

@script
<script>
document.addEventListener("livewire:initialized", function () {

    function initSelect2() {

        $('.select2').each(function () {
            const $select = $(this);
            const $modal  = $select.closest('.modal');

           
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }

            $select.select2({
                width: '100%',
                dropdownParent: $modal.length ? $modal : $(document.body)
            });
        });

        $('#search_shift_id').off('change').on('change', function () {
            @this.set('search.shift_id', $(this).val());
        });

        $('#shift_id').off('change').on('change', function () {
            $wire.set('shift_id', $(this).val());
        });
    }

    initSelect2();

    Livewire.hook('morphed', () => {
        initSelect2();
    });

    $(document).on('shown.bs.modal', function () {
        initSelect2();
    });

});
</script>
@endscript

