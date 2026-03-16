
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
                            <th>{{ __('label.amount') }}</th>
                            <th>{{ __('label.payment_date') }}</th>
                            <th>{{ __('label.note') }}</th>
                            <th>{{ __('label.bill') }}</th>
                            <th>{{ __('label.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($other_fees as $i => $fee)
                        <tr>
                            <td>{{ ($other_fees->currentPage() - 1) * $other_fees->perPage() + $i + 1 }}</td>
                            <td>{{ $fee->amount }}</td>
                            <td>{{ $fee->payment_date->format('Y/m/d') }}</td>
                            <td>{{ $fee->notes }}</td>
                            <td>
                                <a class="btn btn-success btn-icon rounded-pill"
                                href="javascript:void(0);"
                                wire:click="bill({{ $fee->id }})">
                                    <i class="bx bx-money text-white"></i>
                                </a>
                            </td>
                            <td>
                                <div class="dropdown position-static">
                                    <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if(edit(Auth::user()->fee_ids,$active_menu_id))
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="edit({{ $fee->id }})"
                                            ><i class="bx bx-edit-alt me-1 text-success"></i>{{ __('label.edit') }}</a>
                                        @endif
                                        @if(delete(Auth::user()->fee_ids,$active_menu_id))
                                            <a class="dropdown-item " href="javascript:void(0);"  onclick="confirmDelete({{ $fee->id }},'{{$table_name}}')"
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
                {{ $other_fees->links() }}
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
                                <label for="nameBasic" class="form-label">{{ __('label.amount') }} <span style="color:red;">*</span></label>
                               <div class="form-control bg-light @error('amount') is-invalid @enderror">
                                {{ $amount }} &nbsp;
                                </div>
                                @error('amount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                         <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label" >{{ __('label.note') }}</label>
                                <textarea type="text" id="nameBasic" class="form-control @error('note') is-invalid @enderror" wire:model.lazy="note"></textarea>
                                @error('note') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col mb-3">
                            <label class="form-label">{{ __('label.payment_date') }}</label>
                            <div class="form-control bg-light @error('payment_date') is-invalid @enderror">
                                {{ $payment_date }} &nbsp;
                            </div>
                                @error('payment_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
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
   
    <div class="modal fade" id="{{$billModalId}}" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog modal-lg" branch="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('label.bill')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
            
                <div class="modal-body">
                    <div id="{{ $printId  }}">
                        <div class="bill-container" style="width:700px;margin:auto;font-family:Arial;border:1px solid #ddd;padding:20px;border-radius:8px;">
                            <!-- -------bill-------------- -->
                            <div style="text-align:center;margin-bottom:10px;">
                                <img src="{{ asset('logo.png') }}" alt="Logo" style="height:70px;">
                            </div>

                            <!-- Title -->
                            <h2 style="text-align:center;font-weight:bold;margin-bottom:25px;">
                                {{ __('label.student_fee_receipt') }}
                            </h2>

                            <!-- Student Info -->
                            <table style="width:100%;border-collapse:collapse;margin-bottom:20px;">
                                <tr>
                                    <td><b>{{ __('label.student') }}</b></td>
                                    <td>{{ $student->name ?? '' }} {{ $student->last_name ?? '' }}</td>

                                    <td><b>{{ __('label.print_date') }}</b></td>
                                    <td>{{ now()->format('Y/m/d H:i A') }}</td>
                                </tr>
                                <tr>
                                    <td><b>{{ __('label.fee_type') }}</b></td>
                                    <td>{{ $fee_bill->feeType->name ?? '' }}</td>

                                    <td><b>{{ __('label.bill_no') }}</b></td>
                                    <td>#{{ $fee_bill?->id }}</td>
                                </tr>
                            </table>
                            <!-- -------bill-------------- -->
                            <table style="width:100%;border-collapse:collapse;margin-bottom:20px;border:1px solid #ddd;">
                                <thead style="background:#f5f5f5;">
                                    <tr>
                                        <th style="padding:8px;border:1px solid #ddd;">{{ __('label.payment_date') }}</th>
                                        <th style="padding:8px;border:1px solid #ddd;">{{ __('label.amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="padding:8px;border:1px solid #ddd;">{{ $fee_bill?->payment_date->format('Y/m/d') }}</td>
                                        <td style="padding:8px;border:1px solid #ddd;">{{ $fee_bill?->amount }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- Footer -->
                            <div style="margin-top:40px;display:flex;justify-content:space-between;">
                                <div>
                                    ____________________<br>
                                    {{ __('label.cashier') }}
                                </div>

                                <div>
                                    ____________________<br>
                                    {{ __('label.signature') }}
                                </div>
                            </div>
                        </div>
                        <!-- =========second ----------- -->
                         <br>
                         <br>
                         <div class="bill-container" style="width:700px;margin:auto;font-family:Arial;border:1px solid #ddd;padding:20px;border-radius:8px;">
                            <!-- -------bill-------------- -->
                            <div style="text-align:center;margin-bottom:10px;">
                                <img src="{{ asset('logo.png') }}" alt="Logo" style="height:70px;">
                            </div>

                            <!-- Title -->
                            <h2 style="text-align:center;font-weight:bold;margin-bottom:25px;">
                                {{ __('label.student_fee_receipt') }}
                            </h2>

                            <!-- Student Info -->
                            <table style="width:100%;border-collapse:collapse;margin-bottom:20px;">
                                <tr>
                                    <td><b>{{ __('label.student') }}</b></td>
                                    <td>{{ $student->name ?? '' }} {{ $student->last_name ?? '' }}</td>

                                    <td><b>{{ __('label.print_date') }}</b></td>
                                    <td>{{ now()->format('Y/m/d H:i A') }}</td>
                                </tr>
                                <tr>
                                    <td><b>{{ __('label.fee_type') }}</b></td>
                                    <td>{{ $fee_bill->feeType->name ?? '' }}</td>

                                    <td><b>{{ __('label.bill_no') }}</b></td>
                                    <td>#{{ $fee_bill?->id }}</td>
                                </tr>
                            </table>
                            <!-- -------bill-------------- -->
                            <table style="width:100%;border-collapse:collapse;margin-bottom:20px;border:1px solid #ddd;">
                                <thead style="background:#f5f5f5;">
                                    <tr>
                                        <th style="padding:8px;border:1px solid #ddd;">{{ __('label.payment_date') }}</th>
                                        <th style="padding:8px;border:1px solid #ddd;">{{ __('label.amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="padding:8px;border:1px solid #ddd;">{{ $fee_bill?->payment_date->format('Y/m/d') }}</td>
                                        <td style="padding:8px;border:1px solid #ddd;">{{ $fee_bill?->amount }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- Footer -->
                            <div style="margin-top:40px;display:flex;justify-content:space-between;">
                                <div>
                                    ____________________<br>
                                    {{ __('label.cashier') }}
                                </div>

                                <div>
                                    ____________________<br>
                                    {{ __('label.signature') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('label.close') }}</button>
                    <button type="button" class="btn btn-primary" onclick="printDiv('{{ $printId  }}')">{{ __('label.print') }}</button>
                </div>
            </div>
        </div>
    </div>

</div>

