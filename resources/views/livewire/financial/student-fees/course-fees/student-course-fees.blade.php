<div>
<style>
@media print {
    body, html {
        background: #fff !important;
        -webkit-print-color-adjust: exact;
    }

    body * {
        visibility: hidden;
    }

    #printArea, #printArea * {
        visibility: visible;
    }

    #printArea {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        display: block !important;
        direction: ltr !important;
    }
}

/* Logo */
.logo-container {
    text-align: center;
    margin-bottom: 15px;
}

.logo-container .logo {
    max-width: 120px;
}

/* Table Styling */
.bill-table {
    width: 100%;
}

.bill-table td {
    padding: 8px;
    border: 1px solid #000;
}

</style>

<div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">

    <!-- عنوان کارت -->
    <h5 class="card-title mb-0">
            {{ $active_menu?->name }} 
    </h5>

    <!-- دکمه‌ها -->
        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-2">

            <!-- دکمه Export -->
            <!-- <div class="btn-group mb-2 mb-md-0">
                <button type="button" class="btn btn-secondary">
                    <i class="fa fa-file-export"></i> {{ __('label.export') }}
                </button>

                <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="visually-hidden">Toggle Dropdown</span>
                </button>

                <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                    <li class="px-3 py-2">
                        <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-3 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" id="portraitRadio" wire:model="pdfOrientation" value="portrait">
                                <label class="form-check-label" for="portraitRadio">(Portrait)</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" id="landscapeRadio" wire:model="pdfOrientation" value="landscape">
                                <label class="form-check-label" for="landscapeRadio">(Landscape)</label>
                            </div>
                        </div>

                        <a class="dropdown-item d-flex align-items-center gap-2" href="#" wire:click.prevent="exportPdf">
                            <i class="fa fa-file-pdf text-danger"></i> {{ __('label.export_to_pdf') }}
                        </a>
                    </li>
                </ul>
            </div> -->

            <!-- دکمه Add New Record -->
            @if(add(Auth::user()->role_ids,$active_menu_id))
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#{{$modalId}}" wire:click="openModal">
                    <i class="bi bi-plus-lg"></i> {{ __('label.add_new_record') }}
                </button>
            @endif

            </div>
        </div>
        <hr>
        <div class="table-responsive text-nowrap">
 
            <div class="mb-3 px-3">
                <!-- <form wire:submit.prevent="applySearch" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('label.name') }} / {{ __('label.student_code') }} </label>
                        <input type="text" class="form-control" placeholder="" wire:model="search.identity">
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">
                            {{ __('label.search') }}
                        </button>
                    </div>
                </form> -->

                <!-- perPage -->
                <!-- <div class="d-flex align-items-center gap-1 mt-3 justify-content-end">
                    <span>{{ __('label.show') }}</span>
                    <select class="form-select w-auto" wire:model.live="perPage">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span>{{ __('label.entries') }}</span>
                </div> -->
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead class="table-dark">
                        <tr>
                            <th>
                                {{ __('label.NO') }}
                            </th>
                            <th>
                                {{ __('label.course') }}
                            </th>
                            <th>
                                {{ __('label.fee_amount') }}
                            </th>

                            <th>
                                {{ __('label.discount_value') }}
                            </th>
                            <th>
                                {{ __('label.g_discount_amount') }}
                            </th>
                            <th>
                                {{ __('label.total_amount') }}
                            </th>
                            <th>
                                {{ __('label.paid_amount') }}
                            </th>
                            <th>
                                {{ __('label.remaining_amount') }}
                            </th>
                          
                            <th>
                                {{ __('label.status') }}
                            </th>
                            <th>
                                {{ __('label.installments') }}
                            </th>
                            <th>
                                {{ __('label.actions') }}
                            </th>
                        </tr>

                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($course_fees as $i => $fee)
                        <tr>
                            <td>{{ ($course_fees->currentPage() - 1) * $course_fees->perPage() + $i + 1 }}</td>
                            <td>{{ $fee->course?->name }}</td>
                            <td>{{ $fee->fee_amount }}</td>
                            <td>{{ $fee->discount_value }} @if($fee->discount_type=='percentage') % @endif</td>
                            <td>{{ $fee->g_discount_amount }}</td>
                            <td>{{ $fee->total_amount }}</td>
                            <td>{{ $fee->paid_amount }}</td>
                            <td>{{ $fee->remaining_amount }}</td>

                            <td>
                                @if($fee->status=='unpaid')
                                <span class="badge bg-label-danger me-1" style="font-size:10px;">{{ ucfirst($fee->status) }}</span>
                                @elseif($fee->status=='partial')
                                <span class="badge bg-label-warning me-1" style="font-size:10px;">{{ ucfirst($fee->status) }}</span>
                                @elseif($fee->status=='paid')
                                <span class="badge bg-label-success me-1" style="font-size:10px;">{{ ucfirst($fee->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <a class="btn btn-success btn-icon rounded-pill"
                                href="javascript:void(0);"
                                wire:click="showInstallments({{ $fee->id }})">
                                    <i class="bx bx-money text-white"></i>
                                </a>
                            </td>
                            <td>
                                
                                <div class="dropdown position-static">
                                    <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if(edit(Auth::user()->role_ids,$active_menu_id))
                                            <!-- <a class="dropdown-item" href="javascript:void(0);" wire:click="edit({{ $fee->id }})"
                                            ><i class="bx bx-edit-alt me-1 text-success"></i>{{ __('label.edit') }}</a> -->
                                        @endif
                                        @if(delete(Auth::user()->role_ids,$active_menu_id))
                                            @if(Auth::user()->isAdmin() || Auth::user()->isDeveloper())
                                            <a class="dropdown-item " href="javascript:void(0);"  onclick="confirmDelete({{ $fee->id }},'{{$table_name}}')"
                                            ><i class="bx bx-trash me-1 text-danger"></i>{{ __('label.delete') }}</a>
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
                {{ $course_fees->links() }}
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
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.student_course') }}</label>
                                <select  class="form-select @error('course_id') is-invalid @enderror" wire:model.lazy ="course_id">
                                    <option value="">{{ __('label.select') }}</option>
                                    @foreach($student_courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                                    @endforeach
                                </select>
                                @error('course_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        
                        <!-- ---------physical Books------------------ -->
                        @if(count($selected_physical_books) > 0)
                            <div class="card mt-4 shadow-sm border-info mb-4">
                                
                                <div class="card-header bg-info text-white">
                                    {{ __('label.physical_book') }}
                                </div>

                                <div class="card-body">

                                    @foreach($selected_physical_books as $index => $book)
                                        <div wire:key="physical-book-{{ $index }}" class="row mb-2 mt-2 align-items-center border-bottom pb-2">
                                            
                                            <div class="col">
                                                {{ $book['name'] ?? '' }}
                                            </div>

                                            <div class="col text-center">
                                                <span class="text-primary fw-bold">
                                                    {{ number_format($book['price'] ?? 0) }}
                                                </span>
                                            </div>

                                            <div class="col-auto">
                                                @if(($book['status'] ?? 'paid') === 'requested_exemption')
                                                    <!-- فقط کنسل معافیت -->
                                                    <button type="button" class="btn btn-secondary btn-sm"
                                                        wire:click="cancelExemption({{ $index }})">
                                                        {{ __('label.cancel_exemption') }}
                                                    </button>
                                                @else
                                                    <!-- فقط درخواست معافیت -->
                                                    <button type="button" class="btn btn-warning btn-sm"
                                                        wire:click="requestExemption({{ $index }})">
                                                        {{ __('label.request_exemption') }}
                                                    </button>
                                                @endif
                                            </div>

                                        </div>
                                    @endforeach

                                    <div class="mt-3 text-end border-top pt-2">
                                        <strong class="text-dark">
                                            {{ __('label.total') }}: 
                                            <span class="text-success">
                                                {{ number_format($physical_books_total) }}
                                            </span>
                                        </strong>
                                    </div>

                                </div>
                            </div>
                            @endif
                        <!-- ---------physical Books------------------ -->

                        <div class="row">

                            <div class="col mb-3">
                                <label class="form-label">{{ __('label.fee_amount') }}</label>
                                <div class="form-control bg-light @error('fee_amount') is-invalid @enderror">
                                    {{ $fee_amount }} &nbsp;
                                </div>
                                 @error('fee_amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.discount_type') }}</label>
                                <select  class="form-select @error('discount_type') is-invalid @enderror" wire:model.lazy ="discount_type">
                                    <option value="">{{ __('label.no_discount') }}</option>
                                    <option value="percentage">{{ __('label.percentage') }}</option>
                                    <option value="fixed">{{ __('label.fixed') }}</option>
                                </select>
                                @error('discount_type') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                         
                        </div>
                     <div class="row" >
                             @if(!empty($discount_type))
                             <div class="col mb-3" >
                                <label for="nameBasic" class="form-label">{{ __('label.discount_provider') }}: @if($discount_provider_id)
                                    <span class="@if($remaining_discount==0) text-danger @else text-success @endif mt-2">
                                        Remaining discount this month:
                                        <strong>{{ number_format($remaining_discount,2) }}</strong>
                                    </span>
                                    @endif
                                </label>
                                <div wire:ignore>
                                    <select  class="form-select select2 @error('discount_provider_id') is-invalid @enderror" wire:model.lazy="discount_provider_id" id="discount_provider_id">
                                        <option value="">{{ __('label.select') }}</option>
                                        @foreach($discount_providers as $provider)
                                        <option value="{{ $provider->id }}">{{ $provider->name }} {{ $provider->last_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if($discount_provider_id)
                                <div class="mt-2">

                                    <div class="progress">
                                        <div class="progress-bar @if($remaining_discount==0) bg-danger @else bg-info @endif"
                                            role="progressbar"
                                            style="width: {{ $discount_progress }}%">
                                        </div>
                                    </div>
                                    
                                    <small class="text-muted">
                                        Used: {{ number_format($used_discount,2) }}
                                        /
                                        Limit: {{ number_format($used_discount + $remaining_discount,2) }}
                                    </small>

                                </div>
                                @endif
                                @error('discount_provider_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                @if($discount_provider_error)<div class="invalid-feedback d-block">{{ $discount_provider_error }}</div>@endif
                            </div>
                            @endif
                        </div>
                        @if(!empty($discount_type))
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label" >{{ __('label.discount_reason') }} <span style="color:red;">*</span></label>
                                <textarea type="text" id="nameBasic" class="form-control @error('discount_reason') is-invalid @enderror" wire:model.lazy="discount_reason"></textarea>
                                @error('discount_reason') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        @endif
                        <div class="row">
                            @if(!empty($discount_type))
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label" >{{ __('label.discount_value') }} <span style="color:red;">*</span></label>
                                <input type="text" id="nameBasic" class="form-control @error('discount_value') is-invalid @enderror" wire:model.lazy="discount_value">
                                @error('discount_value') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                @if($discount_error)<div class="invalid-feedback d-block">{{ $discount_error }}</div>@endif
                            </div>
                            @endif
                            @if(!empty($g_discount_amount))
                            <div class="col mb-3">
                                <label class="form-label">{{ __('label.g_discount_amount') }}</label>
                                <div class="form-control bg-light">
                                    {{ $g_discount_amount }}&nbsp;
                                </div>
                                 @error('g_discount_amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            @endif
                            <div class="col mb-3">
                                <label class="form-label">{{ __('label.total_amount') }}</label>
                                <div class="form-control bg-light">
                                    {{ $total_amount }} &nbsp;
                                </div>
                                 @error('total_amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.payment_type') }}</label>
                                <select  class="form-select @error('payment_type') is-invalid @enderror" wire:model.lazy ="payment_type">
                                <option value="">{{ __('label.select') }}</option>
                                <option value="full">{{ __('label.full') }}</option>
                                    <option value="installment">{{ __('label.installment') }}</option>
                                </select>
                                @error('payment_type') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                             @if($payment_type === 'installment')
                                <div class="col mb-3">
                                    <label for="nameBasic" class="form-label" >{{ __('label.amount') }} <span style="color:red;">*</span></label>
                                    <input type="text" id="nameBasic" class="form-control @error('installment_amount') is-invalid @enderror" wire:model.lazy="installment_amount">
                                    @error('installment_amount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            @endif
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

    <div class="modal fade" id="{{$installmentModalId}}" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog modal-lg" branch="document">
            <div class="modal-content">
                <div class="modal-header">
                    
                    <h5 class="modal-title">{{__('label.installments')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                    
                </div>
                
                <div class="modal-body">
                    @if($course_fee?->payment_type=='installment')
                     <div class="text-end">
                        <button
                            class="btn btn-primary btn-sm mb-2"
                            wire:click="openPaymentModal({{ $selected_fee_id }})">
                            <i class="bx bx-x"></i> {{ __('label.add_new_record') }}
                        </button>
                    </div>
                    @endif
                    <table class="table">
                        <thead class="table-dark">
                            <tr>
                                <th>
                                    {{ __('label.NO') }}
                                </th>
                                <th>
                                    {{ __('label.amount') }}
                                </th>

                                <th>
                                    {{ __('label.due_date') }}
                                </th>
                                <th>
                                    {{ __('label.status') }}
                                </th>
                                <th>
                                    {{ __('label.payment') }}
                                </th>
                            </tr>

                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach($fee_installments as $i => $installment)
                            <tr>
                                <td>{{ $i + 1 }}</td>

                                <td>{{ $installment->amount }}</td>
                                <td>{{ $installment->due_date->format('Y/m/d') }}</td>

                                <td>
                                    @if($installment->status=='unpaid')
                                    <span class="badge bg-label-danger me-1" style="font-size:10px;">{{ ucfirst($installment->status) }}</span>
                                   
                                    @elseif($installment->status=='cancelled')
                                    <span class="badge bg-label-danger me-1" style="font-size:10px;">{{ ucfirst($installment->status) }}</span>
                                    @elseif($installment->status=='paid')
                                    <span class="badge bg-label-success me-1" style="font-size:10px;">{{ ucfirst($installment->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                @if($installment->status == 'unpaid')
                                <button
                                    class="btn btn-danger btn-sm rounded-pill"
                                    wire:click="openCancelModal({{ $installment->id }})">
                                    <i class="bx bx-x"></i> {{ __('label.cancel') }}
                                </button>
                                <button
                                    class="btn btn-success btn-sm rounded-pill"
                                    wire:click="openPayModal({{ $installment->id }})">
                                    <i class="bx bx-credit-card"></i> {{ __('label.pay') }}
                                </button>
                                @elseif($installment->status == 'cancelled')
                                    {{ $installment->cancel_reason }}
                                @elseif($installment->status == 'paid')

                                <!-- <span class="badge bg-success">{{ __('label.paid') }}</span> -->
                                    <button class="btn btn-primary btn-sm" wire:click="loadInstallmentForPrint({{ $installment->id }})">
                                    <i class="bx bx-printer"></i> {{ __('label.print') }}
                                </button>
                                @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('label.close') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cancelInstallmentModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('label.cancel_installment') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">{{ __('label.cancel_reason') }} <span style="color:red;">*</span></label>
                        <textarea class="form-control @error('cancel_reason') is-invalid @enderror" wire:model="cancel_reason"></textarea>
                    </div>
                    @error('cancel_reason') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">{{__('label.close')}}</button>

                    <button class="btn btn-danger" wire:click="cancelInstallment">
                        {{ __('label.confirm_cancel') }}
                    </button>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="payInstallmentModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('label.pay_installment') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="alert alert-solid-danger" role="alert">
                       Are you sure you want to pay this installment?
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">{{__('label.close')}}</button>

                    <button class="btn btn-success" type="button" wire:click="payInstallment">
                        {{ __('label.pay') }}
                    </button>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="openPaymentModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('label.pay') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form  wire:submit.prevent="storeInstallment">
                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">{{ __('label.amount') }} <span style="color:red;">*</span></label>
                            <input type="text" class="form-control @error('install_amount') is-invalid @enderror" wire:model.lazy="install_amount"/>
                             @error('install_amount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('label.close')}}</button>
                        <button type="submit" class="btn btn-success" >
                            {{ __('label.pay') }}
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <!-- --------------for print-------------------------------- -->
   @if(!empty($installmentToPrint))
    <div id="printArea" style="display:none;">
        <!----------- one-------- -->
        <div class="bill-container" style="width:700px;margin:auto;font-family:Arial;border:1px solid #ddd;padding:20px;border-radius:8px;">
            <!-- Logo -->
            <div style="text-align:center;margin-bottom:10px;">
                <img src="{{ asset('logo.png') }}" alt="Logo" style="height:70px;">
            </div>

            <!-- Title -->
            <h2 style="text-align:center;font-weight:bold;margin-bottom:25px;">
                {{ __('label.student_course_fee_receipt') }}
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
                    <td><b>{{ __('label.course') }}</b></td>
                    <td>{{ $studentCourseFee->course->name ?? '' }}</td>

                    <td><b>{{ __('label.bill_no') }}</b></td>
                    <td>#{{ $installmentToPrint?->payments?->id }}</td>
                </tr>
            </table>

            <!-- Installment Info -->
            <table style="width:100%;border-collapse:collapse;margin-bottom:20px;border:1px solid #ddd;">
                <thead style="background:#f5f5f5;">
                    <tr>
                        <th style="padding:8px;border:1px solid #ddd;">{{ __('label.payment_date') }}</th>
                        <th style="padding:8px;border:1px solid #ddd;">{{ __('label.amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding:8px;border:1px solid #ddd;">{{ $installmentToPrint?->payments?->payment_date->format('Y/m/d') }}</td>
                        <td style="padding:8px;border:1px solid #ddd;">{{ $installmentToPrint?->payments?->amount }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Fee Summary -->
            <table style="width:100%;border-collapse:collapse;">
                <tr>
                    <td style="text-align:right;"><b>{{ __('label.total_course_fee') }}:</b></td>
                    <td style="width:150px;text-align:right;">
                        {{ $studentCourseFee->total_amount ?? '' }}
                    </td>
                </tr>

                <tr>
                    <td style="text-align:right;"><b>{{ __('label.total_paid') }}:</b></td>
                    <td style="text-align:right;">
                        {{ $studentCourseFee->paid_amount }}
                    </td>
                </tr>

                <tr>
                    <td style="text-align:right;"><b>{{ __('label.remaining_balance') }}:</b></td>
                    <td style="text-align:right;font-weight:bold;">
                        {{ $studentCourseFee->remaining_amount }}
                    </td>
                </tr>
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
        <br>
        <br>
         <!----------- two-------- -->
        <div class="bill-container" style="width:700px;margin:auto;font-family:Arial;border:1px solid #ddd;padding:20px;border-radius:8px;">
            <!-- Logo -->
            <div style="text-align:center;margin-bottom:10px;">
                <img src="{{ asset('logo.png') }}" alt="Logo" style="height:70px;">
            </div>

            <!-- Title -->
            <h2 style="text-align:center;font-weight:bold;margin-bottom:25px;">
                {{ __('label.student_course_fee_receipt') }}
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
                    <td><b>{{ __('label.course') }}</b></td>
                    <td>{{ $studentCourseFee->course->name ?? '' }}</td>

                    <td><b>{{ __('label.bill_no') }}</b></td>
                    <td>#{{ $installmentToPrint?->payments?->id }}</td>
                </tr>
            </table>

            <!-- Installment Info -->
            <table style="width:100%;border-collapse:collapse;margin-bottom:20px;border:1px solid #ddd;">
                <thead style="background:#f5f5f5;">
                    <tr>
                        <th style="padding:8px;border:1px solid #ddd;">{{ __('label.payment_date') }}</th>
                        <th style="padding:8px;border:1px solid #ddd;">{{ __('label.amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding:8px;border:1px solid #ddd;">{{ $installmentToPrint?->payments?->payment_date->format('Y/m/d') }}</td>
                        <td style="padding:8px;border:1px solid #ddd;">{{ $installmentToPrint?->payments?->amount }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Fee Summary -->
            <table style="width:100%;border-collapse:collapse;">
                <tr>
                    <td style="text-align:right;"><b>{{ __('label.total_course_fee') }}:</b></td>
                    <td style="width:150px;text-align:right;">
                        {{ $studentCourseFee->total_amount ?? '' }}
                    </td>
                </tr>

                <tr>
                    <td style="text-align:right;"><b>{{ __('label.total_paid') }}:</b></td>
                    <td style="text-align:right;">
                        {{ $studentCourseFee->paid_amount }}
                    </td>
                </tr>

                <tr>
                    <td style="text-align:right;"><b>{{ __('label.remaining_balance') }}:</b></td>
                    <td style="text-align:right;font-weight:bold;">
                        {{ $studentCourseFee->remaining_amount }}
                    </td>
                </tr>
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
    @endif
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


        $('#discount_provider_id').off('change').on('change', function () {
            @this.set('discount_provider_id', $(this).val());
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

<script>
    window.addEventListener('show-print-preview', () => {
        printDiv('printArea');
    });
</script>