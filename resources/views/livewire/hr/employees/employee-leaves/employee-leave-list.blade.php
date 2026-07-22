
<div>

    <!-- title -->
      @section('title',(($active_menu?->parent?->name_en ?? '') ? $active_menu?->parent?->name_en . '-' : ''
        ) . $active_menu?->name_en . ' | '.__('label.app_name'))
    <!-- end title -->
        <div class="card-header">
      
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="card-title mb-2 mb-md-0">
                    {{ __('label.employee_leave') }}
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
                        <label class="form-label">{{ __('label.leave_type') }}</label>
                        <select class="form-select" wire:model.defer="search.leave_type" id ="">
                            <option value="">{{ __('label.all') }}</option>
                            @foreach($leave_types as $leave_type)
                            <option value="{{ $leave_type->id }}"  wire:key="leave_type_{{ $leave_type->id }}">{{ $leave_type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
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
                            <th>{{ __('label.leave_type') }}</th>
                            <th>{{ __('label.contract') }}</th>
                            <th>{{ __('label.start_date') }}</th>
                            <th>{{ __('label.end_date') }}</th>
                            <th>{{ __('label.days') }}</th>
                            <th>{{ __('label.reason') }}</th>
                            <th>{{ __('label.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($employee_leaves as $i => $leave)
                        <tr>
                            <td>{{ ($employee_leaves->currentPage() - 1) * $employee_leaves->perPage() + $i + 1 }}</td>
                            <td>{{ $leave?->leaveType?->name }}</td>
                            <td>{{ $leave?->contract?->position?->name }}</td>
                            <td>{{ $leave->start_date?->format('Y/m/d') }}</td>
                            <td>{{ $leave->end_date?->format('Y/m/d') }}</td>
                            <td>{{ $leave->days }}</td>
                            <td>{{ $leave->reason }}</td>
                            <td>
                              
                                   
                              <div class="dropdown position-static">
                                   <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                   <i class="bx bx-dots-vertical-rounded"></i>
                                   </button>
                                   <div class="dropdown-menu">
                                        @if(edit(Auth::user()->role_ids,$active_menu_id))
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="edit({{ $leave->id }})"
                                            ><i class="bx bx-edit-alt me-1 text-success"></i>{{ __('label.edit') }}</a>
                                        @endif
                                        @if(delete(Auth::user()->role_ids,$active_menu_id))
                                            <a class="dropdown-item " href="javascript:void(0);"  onclick="confirmDelete({{ $leave->id }},'{{$table_name}}')"
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
                {{ $employee_leaves->links() }}
            </div>
        </div>
    <!--/ Bootstrap Table with Header Dark -->
    <div class="modal fade" id="{{$modalId}}" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog" leave="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> @if(!$editMode) {{ __('label.add') }} @else {{ __('label.edit') }} @endif {{ __('label.employee_leave') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <form @if($editMode) wire:submit.prevent="update" @else wire:submit.prevent="store" @endif>
                    <div class="modal-body">
           
                        <div class="row">
                            <div class="col mb-3">
                                <div wire.ignore>
                                    <label for="nameBasic" class="form-label">{{ __('label.leave_type') }} <span style="color:red;">*</span></label>
                                    <div wire:ignore>
                                    <select  class="form-select @error('leave_type_id') is-invalid @enderror" wire:model.lazy ="leave_type_id">
                                        <option value="">{{ __('label.select') }}</option>
                                        @foreach($leave_types as $leave_type)
                                        <option value="{{ $leave_type->id }}">{{ $leave_type->name }}</option>
                                        @endforeach
                                    </select>
                                    </div>
                                     @error('leave_type_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                               
                            </div>
                            <div class="col mb-3">

                                <label class="form-label"> {{ __('label.contract_type') }} <span style="color:red;">*</span></label>

                                <select class="form-select" wire:model.live="contract_type">

                                    <option value="">  {{ __('label.select') }} </option>

                                    <option value="permanent">{{ __('label.permanent_contract') }}</option>

                                    <option value="temporary">{{ __('label.temporary_contract') }}</option>

                                </select>
                                 @error('contract_type') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                            </div>
                        </div>

                        <div class="row">
                            <div class="col mb-3">

                                <label class="form-label"> {{ __('label.contract') }} <span style="color:red;">*</span></label>

                                <select class="form-select" wire:model.lazy="contract_id">

                                    <option value=""> {{ __('label.select') }}</option>

                                    @foreach($contracts as $contract)

                                        <option value="{{ $contract->id }}">
                                            {{ $contract->position?->name }}

                                            ({{ $contract->branch?->name }})

                                        </option>

                                    @endforeach

                                </select>
                                  @error('contract_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                            </div>
                        </div>

                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.start_date') }} <span style="color:red;">*</span></label>
                                <input type="date" id="nameBasic" class="form-control @error('start_date') is-invalid @enderror" wire:model.lazy="start_date">
                                @error('start_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.end_date') }} <span style="color:red;">*</span></label>
                                <input type="date" id="nameBasic" class="form-control @error('end_date') is-invalid @enderror" wire:model.lazy="end_date">
                                @error('end_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                       <div class="row">
                            <div class="col mb-3">

                                <label class="form-label">
                                    {{ __('label.days') }}
                                </label>

                                <div class="form-control bg-light">
                                    {{ $days ?? 0 }}
                                </div>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label" >{{ __('label.reason') }}</label>
                                <textarea type="text" id="nameBasic" class="form-control @error('reason') is-invalid @enderror" wire:model.lazy="reason"></textarea>
                                @error('reason') <div class="invalid-leavedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" >{{ __('label.close') }}</button>
                        <button type="submit" class="btn @if($editMode) btn-success @else btn-primary @endif">@if($editMode) {{ __('label.edit') }}  @else {{ __('label.save') }} @endif</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
   
    <div class="modal fade" id="confirmExemptionModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('label.confirm_exemption') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="alert alert-solid-danger" role="alert">
                       Are you sure you want to confirm this exemption?
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">{{__('label.close')}}</button>

                    <button class="btn btn-success" type="button" wire:click="confirmExemption">
                        {{ __('label.confirm') }}
                    </button>
                </div>

            </div>
        </div>
    </div>


    <div class="modal fade" id="rejectExemptionModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('label.reject_exemption') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">{{ __('label.reason') }} <span style="color:red;">*</span></label>
                        <textarea class="form-control @error('note') is-invalid @enderror" wire:model="note"></textarea>
                    </div>
                    @error('note') <div class="invalid-leavedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">{{__('label.close')}}</button>

                    <button class="btn btn-danger" type="button" wire:click="rejectExemption">
                        {{ __('label.confirm') }}
                    </button>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="payModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('label.pay') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="alert alert-solid-danger" role="alert">
                       Are you sure you want to pay this book price?
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">{{__('label.close')}}</button>

                    <button class="btn btn-success" type="button" wire:click="payStore">
                        {{ __('label.pay') }}
                    </button>
                </div>

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


        $('#select_physical_book_id').off('change').on('change', function () {
            @this.set('physical_book_id', $(this).val());
        });
    }

    
    initSelect2();

    
    Livewire.hook('morphed', () => {
        initSelect2();
    });

   
    Livewire.hook('message.processed', function (message, component) {
        const $modal = $('#{{$modalId}}');
        if ($modal.is(':visible')) {
            initSelect2();
        }
    });


    $(document).on('shown.bs.modal', function () {
        initSelect2();
    });
});
</script>
  
@endscript



