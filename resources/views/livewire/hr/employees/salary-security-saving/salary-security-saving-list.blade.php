
<div>

    <!-- title -->
      @section('title',(($active_menu?->parent?->name_en ?? '') ? $active_menu?->parent?->name_en . '-' : ''
        ) . $active_menu?->name_en . ' | '.__('label.app_name'))
    <!-- end title -->
     
        <div class="card-header">
      
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="card-title mb-2 mb-md-0">
                    {{ __('label.salary_security_saving') }}
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

          <div class="card mb-4">

            <div class="card-header bg-light">
                <i class="bx bx-shield-quarter me-2 text-success"></i>
                Security Saving Balance
            </div>


            <div class="card-body">

                <div class="row">

                    {{-- Permanent Contracts --}}
                    <div class="col-md-6 border-end">

                        <h6 class="mb-3 mt-3">
                            <i class="bx bx-briefcase me-2 text-primary"></i>
                           {{ __('label.permanent_contract') }}
                        </h6>


                        @forelse($employee->activePermanentContract as $contract)

                            <div class="d-flex justify-content-between align-items-center mb-3">

                                <div>
                                    <strong>
                                        {{ $contract->branch?->name }}
                                    </strong>
                                </div>


                                <span class="badge bg-success">
                                    {{ number_format($contract->security_saving_balance,2) }}
                                </span>

                            </div>

                        @empty

                            <span class="text-muted">
                                No permanent contract
                            </span>

                        @endforelse

                    </div>



                    {{-- Temporary Contracts --}}
                    <div class="col-md-6 ">

                        <h6 class="mb-3 mt-3">
                            <i class="bx bx-time-five me-2 text-warning"></i>
                            {{ __('label.temporary_contract') }}
                        </h6>


                        @forelse($employee->activeTemporaryContract as $contract)

                            <div class="d-flex justify-content-between align-items-center mb-3">

                                <div>

                                    <strong>
                                        {{ $contract->branch?->name }}
                                    </strong>

                                    <br>

                                    <small class="text-muted">
                                        {{ $contract->section?->name }}
                                    </small>

                                </div>


                                <span class="badge bg-success">
                                    {{ number_format($contract->security_saving_balance,2) }}
                                </span>

                            </div>


                        @empty

                            <span class="text-muted">
                                No temporary contract
                            </span>

                        @endforelse

                    </div>


                </div>

            </div>

        </div>

        <hr>

        <div class="table-responsive text-nowrap">

            <div class="mb-3 px-3">
                <form wire:submit.prevent="applySearch" class="row g-3 align-items-end">
               
                    <div class="col-md-3">
                        <label class="form-label">{{ __('label.transaction_type') }}</label>
                        <select class="form-select" wire:model.defer="search.type" id ="">
                            <option value="">{{ __('label.all') }}</option>

                            <option value="deposit" >{{ __('label.deposit') }}</option>
                            <option value="refund" >{{ __('label.refund') }}</option>
                            <option value="deduction" >{{ __('label.deduction') }}</option>
         
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
                            <th>{{ __('label.contract') }}</th>
                            <th>{{ __('label.amount') }}</th>
                            <th>{{ __('label.transaction_type') }}</th>
                            <th>{{ __('label.date') }}</th>
                            <th>{{ __('label.note') }}</th>
                            <th>{{ __('label.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($salary_saving as $i => $saving)
                        <tr>
                            <td>{{ ($salary_saving->currentPage() - 1) * $salary_saving->perPage() + $i + 1 }}</td>
                            
                            <td>{{ $saving?->contract?->position?->name }}</td>
                            <td>{{ $saving->amount }}</td>
                            <td>
                                @if($saving?->type == 'deposit')
                                    <span class="badge bg-success">
                                        {{ $saving->type }}
                                    </span>

                                @elseif($saving?->type == 'refund')
                                    <span class="badge bg-info">
                                        {{ $saving->type }}
                                    </span>

                                @elseif($saving?->type == 'deduction')
                                    <span class="badge bg-danger">
                                        {{ $saving->type }}
                                    </span>

                                @endif
                            </td>
                            <td>{{ $saving->transaction_date }}</td>
                            <td>{{ $saving->note }}</td>
                            <td>
                            
                              <div class="dropdown position-static">
                                   <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                   <i class="bx bx-dots-vertical-rounded"></i>
                                   </button>
                                   <div class="dropdown-menu">
                                        @if(delete(Auth::user()->role_ids,$active_menu_id) && $saving->type!='deposit')
                                            <a class="dropdown-item " href="javascript:void(0);"  onclick="confirmDelete({{ $saving->id }},'{{$table_name}}')"
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
                {{ $salary_saving->links() }}
            </div>
        </div>
    <!--/ Bootstrap Table with Header Dark -->
    <div class="modal fade" id="{{$modalId}}" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog" saving="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> @if(!$editMode) {{ __('label.add') }} @else {{ __('label.edit') }} @endif {{ __('label.salary_security_saving') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <form @if($editMode) wire:submit.prevent="update" @else wire:submit.prevent="store" @endif>
                    <div class="modal-body">
           
                        <div class="row">
                            <div class="col mb-3">
                                <div wire.ignore>
                                    <label for="nameBasic" class="form-label">{{ __('label.transaction_type') }} <span style="color:red;">*</span></label>
                                    <div wire:ignore>
                                        <select class="form-select" wire:model.lazy="type" id ="">
                                            <option value="">{{ __('label.select') }}</option>

                                            <option value="refund" >{{ __('label.refund') }}</option>
                                            <option value="deduction" >{{ __('label.deduction') }}</option>
                        
                                        </select>
                                    </div>
                                     @error('type') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
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
                                <label>{{ __('label.amount') }}<span style="color:red;">*</span></label>
                                <input type="number" wire:model.lazy="amount" class="form-control" min="0" step="any">
                                @error('amount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.transaction_date') }} <span style="color:red;">*</span></label>
                                <input type="date" id="nameBasic" class="form-control @error('transaction_date') is-invalid @enderror" wire:model.lazy="transaction_date">
                                @error('transaction_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>


                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label" >{{ __('label.note') }}</label>
                                <textarea type="text" id="nameBasic" class="form-control @error('note') is-invalid @enderror" wire:model.lazy="note"></textarea>
                                @error('note') <div class="invalid-savingdback d-block">{{ $message }}</div> @enderror
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

