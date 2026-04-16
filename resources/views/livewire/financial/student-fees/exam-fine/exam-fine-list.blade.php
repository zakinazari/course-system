
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
                        <!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#{{$modalId}}" wire:click="openModal">
                            <i class="bi bi-plus-lg"></i> {{ __('label.add_new_record') }}
                        </button> -->
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
                            <th>{{ __('label.course') }}</th>
                            <th>{{ __('label.exam_type') }}</th>
                            <th>{{ __('label.amount') }}</th>
                            <th>{{ __('label.status') }}</th>
                            <th>{{ __('label.payment') }}</th>
                            <th>{{ __('label.payment_date') }}</th>
                            <th>{{ __('label.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($exam_fines as $i => $fine)
                        <tr>
                            <td>{{ ($exam_fines->currentPage() - 1) * $exam_fines->perPage() + $i + 1 }}</td>
                            <td>{{ $fine->course?->name }}</td>
                            <td>{{ $fine->examType?->name }}</td>
                            <td>{{ $fine->amount }}</td>
                            <td>
                                @if($fine->status=='unpaid')
                                <span class="badge bg-label-danger me-1" style="font-size:10px;">{{ ucfirst($fine->status) }}</span>
                                @elseif($fine->status=='waived')
                                <span class="badge bg-label-warning me-1" style="font-size:10px;">{{ ucfirst($fine->status) }}</span>
                                @elseif($fine->status=='paid')
                                <span class="badge bg-label-success me-1" style="font-size:10px;">{{ ucfirst($fine->status) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($fine->status=='waived')
                                    {{ $fine->reason }}
                                @endif
                                @if($fine->status=='unpaid')
                                <button
                                    class="btn btn-danger btn-sm rounded-pill"
                                    wire:click="openWaivedModal({{ $fine->id }})">
                                    <i class="bx bx-x"></i> {{ __('label.waive') }}
                                </button>
                                <button
                                    class="btn btn-success btn-sm rounded-pill"
                                    wire:click="openPayModal({{ $fine->id }})">
                                    <i class="bx bx-credit-card"></i> {{ __('label.pay') }}
                                </button>
                                @endif
                            </td>
                            <td>{{ $fine?->payment_date?->format('Y/m/d') }}</td>
                            
                            <td>
                                <div class="dropdown position-static">
                                    <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if(delete(Auth::user()->fine_ids,$active_menu_id))
                                            @if(Auth::user()->isAdmin() || Auth::user()->isDeveloper())
                                                @if($fine->status=='unpaid')
                                                    <a class="dropdown-item " href="javascript:void(0);"  onclick="confirmDelete({{ $fine->id }},'{{$table_name}}')"
                                                    ><i class="bx bx-trash me-1 text-danger"></i>{{ __('label.delete') }}</a>
                                                
                                                @endif
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
                {{ $exam_fines->links() }}
            </div>
        </div>
    <!--/ Bootstrap Table with Header Dark -->
    <div class="modal fade" id="{{$modalId}}" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog" fine="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@if($editMode) {{ __('label.editing') }}  @else {{ __('label.adding') }} @endif {{ $fee_type_name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <form @if($editMode) wire:submit.prevent="update" @else wire:submit.prevent="store" @endif>
                    <div class="modal-body">
            
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.amount') }} <span style="color:red;">*</span></label>
                               <div class="form-control bg-light @error('amount') is-invalid @enderror">
                                {{ $amount }} &nbsp;
                                </div>
                                @error('amount') <div class="invalid-finedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                         <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label" >{{ __('label.note') }}</label>
                                <textarea type="text" id="nameBasic" class="form-control @error('note') is-invalid @enderror" wire:model.lazy="note"></textarea>
                                @error('note') <div class="invalid-finedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col mb-3">
                            <label class="form-label">{{ __('label.payment_date') }}</label>
                            <div class="form-control bg-light @error('payment_date') is-invalid @enderror">
                                {{ $payment_date }} &nbsp;
                            </div>
                                @error('payment_date')<div class="invalid-finedback d-block">{{ $message }}</div>@enderror
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
  

    <div class="modal fade" id="payExamFineModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('label.pay_exam_fine') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="alert alert-solid-danger" role="alert">
                       Are you sure you want to pay this exam fine?
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">{{__('label.close')}}</button>

                    <button class="btn btn-success" type="button" wire:click="payExamFine">
                        {{ __('label.pay') }}
                    </button>
                </div>

            </div>
        </div>
    </div>

    
    <div class="modal fade" id="waivedModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('label.waived_exam_fine') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">{{ __('label.reason') }} <span style="color:red;">*</span></label>
                        <textarea class="form-control @error('reason') is-invalid @enderror" wire:model="reason"></textarea>
                    </div>
                    @error('reason') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">{{__('label.close')}}</button>

                    <button class="btn btn-danger" type="button" wire:click="waivedStore">
                        {{ __('label.confirm') }}
                    </button>
                </div>

            </div>
        </div>
    </div>

</div>

