
<div>
    @php
    $printId = 'printArea' . $fee_type_id;
    @endphp
<style>
    @media print {
        body, html {
            background: #fff !important;
            -webkit-print-color-adjust: exact;
        }

        body * {
            visibility: hidden;
        }

        [id^="printArea"], [id^="printArea"] * {
            visibility: visible;
        }

        [id^="printArea"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            display: block !important;
            direction: ltr !important;
        }
    }
</style>
    <!-- title -->
      @section('title',(($active_menu?->parent?->name_en ?? '') ? $active_menu?->parent?->name_en . '-' : ''
        ) . $active_menu?->name_en . ' | '.__('label.app_name'))
    <!-- end title -->
        <div class="card-header">
      
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="card-title mb-2 mb-md-0">
                    {{ $fee_type_name }}
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
                            <th>{{ __('label.physical_book') }}</th>
                            <th>{{ __('label.price') }}</th>
                            <th>{{ __('label.status') }}</th>
                            <th>{{ __('label.payment_date') }}</th>
                            <th>{{ __('label.type') }}</th>
                            <th>{{ __('label.exemption_reason') }}</th>
                            <th>{{ __('label.note') }}</th>
                            <th>{{ __('label.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($book_fees as $i => $fee)
                        <tr>
                            <td>{{ ($book_fees->currentPage() - 1) * $book_fees->perPage() + $i + 1 }}</td>
                            <td>{{ $fee->book?->name }}</td>
                            <td>{{ $fee->price }}</td>
                           
                            <td>
                                @if($fee->status==='paid')
                                <span class="badge bg-label-success me-1" style="font-size:10px;">{{ ucfirst($fee->status) }}</span>
                                @elseif($fee->status==='requested_exemption')
                                <span class="badge bg-label-warning me-1" style="font-size:10px;">{{ ucfirst($fee->status) }}</span>
                                    
                                @elseif($fee->status==='accepted_exemption')
                                <span class="badge bg-label-info me-1" style="font-size:10px;">{{ ucfirst($fee->status) }}</span>
                                @elseif($fee->status==='rejected_exemption')
                                <span class="badge bg-label-danger me-1" style="font-size:10px;">{{ ucfirst($fee->status) }}</span>
                                    
                                @endif
                            </td>
                            <td>{{ $fee->payment_date?->format('Y/m/d') }}</td>
                            <td>{{ ucfirst($fee->type) }}</td>
                            <td>{{ $fee->reason }}</td>
                            <td>{{ $fee->notes }}</td>
                            <td>
                                <div class="dropdown position-static">
                                    <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if(Auth::user()->id == $fee->user_id && $fee->status === 'rejected_exemption')

                                            <a
                                                class="dropdown-item"
                                                href="javascript:void(0);"
                                                wire:click="openPayModal({{ $fee->id }})">

                                                <i class="bx bx-wallet me-1 text-success"></i>
                                                {{ __('label.pay') }}

                                            </a>

                                        @endif

                                       @if(confirm(Auth::user()->role_ids,$active_menu_id))

                                        @if($fee->status === 'requested_exemption')

                                            <a
                                                class="dropdown-item"
                                                href="javascript:void(0);"
                                                wire:click="openRejectExemption({{ $fee->id }})">

                                                <i class="bx bx-x-circle me-1 text-danger"></i>
                                                {{ __('label.reject') }}

                                            </a>

                                            <a
                                                class="dropdown-item"
                                                href="javascript:void(0);"
                                                wire:click="openConfirmExemption({{ $fee->id }})">

                                                <i class="bx bx-check-circle me-1 text-success"></i>
                                                {{ __('label.confirm') }}

                                            </a>

                                        @endif

                                    @endif


                                    @if(delete(Auth::user()->role_ids,$active_menu_id))

                                        @if(Auth::user()->isAdmin() || Auth::user()->isDeveloper())

                                            <a
                                                class="dropdown-item"
                                                href="javascript:void(0);"
                                                onclick="confirmDelete({{ $fee->id }},'{{$table_name}}')">

                                                <i class="bx bx-trash me-1 text-danger"></i>
                                                {{ __('label.delete') }}

                                            </a>

                                        @endif

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
                {{ $book_fees->links() }}
            </div>
        </div>
    <!--/ Bootstrap Table with Header Dark -->
    <div class="modal fade" id="{{$modalId}}" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog" fee="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@if($editMode) {{ __('label.editing') }}  @else {{ __('label.adding') }} @endif {{ $fee_type_name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <form @if($editMode) wire:submit.prevent="update" @else wire:submit.prevent="store" @endif>
                    <div class="modal-body">
                     
                        <div class="row">
                            <div class="col mb-3">
                                <div wire.ignore>
                                    <label for="nameBasic" class="form-label">{{ __('label.physical_book') }} <span style="color:red;">*</span></label>
                                    <div wire:ignore>
                                    <select  class="form-select select2 @error('physical_book_id') is-invalid @enderror" wire:model.lazy ="physical_book_id" id ="select_physical_book_id">
                                        <option value="">{{ __('label.select') }}</option>
                                        @foreach($physical_books as $book)
                                        <option value="{{ $book->id }}">{{ $book->name }} ({{ __('label.price') }}: {{ $book->price }})</option>
                                        @endforeach
                                    </select>
                                    </div>
                                </div>
                                @error('physical_book_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label" >{{ __('label.note') }}</label>
                                <textarea type="text" id="nameBasic" class="form-control @error('note') is-invalid @enderror" wire:model.lazy="note"></textarea>
                                @error('note') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
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
                    @error('note') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
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


