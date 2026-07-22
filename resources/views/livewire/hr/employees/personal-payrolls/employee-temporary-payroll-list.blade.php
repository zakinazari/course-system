
<div>

<style>
   

</style>

    <div class="">
       
       <div class="card-header">
            
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="card-title mb-2 mb-md-0">
                    {{ __('label.temporary_payroll') }}
                </h5>

                <!-- <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <div class="btn-group">
                        <button type="button" class="btn btn-secondary">
                            <i class="fa fa-file-export"></i> {{ __('label.export') }}
                        </button>

                        <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Toggle Dropdown</span>
                        </button>

                        <ul class="dropdown-menu">
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
                    </div>

     
                </div> -->

            </div>
        </div>
        <hr>
        <div class=" text-nowrap">
 
            <div class="mb-3 px-3 mb-5">
                <form wire:submit.prevent="applySearch" class="row g-3 align-items-end">

                    <div class="col-md-3 d-flex flex-column">
                        <label class="form-label">{{ __('label.year') }}</label>
                        <select  class="form-select" wire:model.lazy ="search.year">
                           @foreach($years as $year)
                                 <option value="{{ $year->year }}"  wire:key="year-search-{{ $year->year }}">
                                    {{ $year->year }}
                                 </option>
                           @endforeach
                        </select>
                        
                    </div>
                    <div class="col-md-3 d-flex flex-column">
                        <label class="form-label">{{ __('label.month') }}</label>
                        <select  class="form-select" wire:model.lazy ="search.month">
                            <option value="">{{ __('label.all') }}</option>
                           @foreach($months as $month)
                                 <option value="{{ $month->number }}"  wire:key="month-search-{{ $month->number }}">
                                    {{ $month->name }}
                                 </option>
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
            <!--  contents --> 

               <div class="table-responsive text-nowrap">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>{{ __('label.NO') }}</th>
                            <th>{{ __('label.year') }}</th>
                            <th>{{ __('label.month') }}</th>
                            <th>{{ __('label.status') }}</th>
                            <th>{{ __('label.payment_date') }}</th>
                            <th>{{ __('label.details') }}</th>
                            <th>{{ __('label.actions') }}</th>
                        </tr>

                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($payrolls as $i => $payroll)
                        <tr>
                            <td>{{ ($payrolls->currentPage() - 1) * $payrolls->perPage() + $i + 1 }}</td>
                            <td>{{ $payroll->year }} </td>
                            <td>{{ $payroll?->month?->name }} </td>
                            <td>
                                
                                 @if($payroll->status === 'paid')
                                    <span class="badge bg-label-success me-1" style="font-size:10px;">
                                        {{ ucfirst($payroll->status) }}
                                    </span>
                                @elseif($payroll->status === 'pending')
                                    <span class="badge bg-label-warning me-1" style="font-size:10px;">
                                        {{ ucfirst($payroll->status) }}
                                    </span>
                                @endif
                            </td>
                            <td>{{ $payroll->payment_date?->format('Y/m/d') }}</td>
                            <td>
                                <a class="btn btn-success btn-icon rounded-pill"
                                href="javascript:void(0);"
                                wire:click="showPairollDetails({{ $payroll->id }})">
                                    <i class="bx bx-money text-white"></i>
                                </a>
                            </td>
                            <td>
                                @if($payroll->status!='paid')
                                <div class="dropdown position-static">
                                    <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">

                                        @if(delete(Auth::user()->role_ids,$active_menu_id))
                                            <a class="dropdown-item " href="javascript:void(0);"  onclick="confirmDelete({{ $payroll->id }},'{{$table_name}}')"
                                            ><i class="bx bx-trash me-1 text-danger"></i>{{ __('label.delete') }}</a>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 justify-content-end px-3">
                {{ $payrolls->links() }}
            </div>
            <!--  contents --> 
        </div>
    </div>


    <div class="modal fade" id="{{$modalId}}" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog modal-lg" branch="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> {{ __('label.details') }}</h5>
                    
                    <div class="d-flex gap-2 align-items-center">

                        <a href="#"
                        wire:click.prevent="print"
                        class="btn btn-secondary btn-sm d-flex align-items-center gap-1">

                            <i class="fa fa-print"></i>
                            <span class="d-none d-sm-inline">
                                {{ __('label.print') }}
                            </span>

                        </a>

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Close">
                        </button>

                    </div>

                </div>
                
                <div class="modal-body">

                    @if(!empty($show_payroll_details))
                    <div id="printArea" dir="ltr">
                            
                        <div class="print-page">

                             <!-- Logo -->
                            <div style="text-align:center;margin-bottom:10px;">
                                <img src="{{ asset('logo.png') }}" alt="Logo" style="height:70px;">
                            </div>

                            <!-- Title -->
                            <h5 style="text-align:center;font-weight:bold;margin-bottom:25px;">
                                {{ __('label.teacher_payroll_receipt') }}
                            </h5>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle mb-0">

                                    <tbody>

                                        <tr>
                                            <th class="bg-light" style="width:35%">{{ __('label.employee') }}</th>
                                            <td>{{ $show_payroll_details->employee?->name }} {{ $show_payroll_details->employee?->last_name }}</td>
                                        </tr>

                                        <tr>
                                            <th class="bg-light">{{ __('label.employee_code') }}</th>
                                            <td>{{ $show_payroll_details->employee?->employee_code }}</td>
                                        </tr>

                                        <tr>
                                            <th class="bg-light">{{ __('label.year') }}/{{ __('label.month') }}</th>
                                            <td>{{ $show_payroll_details->year }}/{{ $show_payroll_details->month?->name }}</td>
                                        </tr>

                                        <tr>
                                            <th class="bg-light">{{ __('label.gross_salary') }}</th>
                                            <td>{{ number_format($show_payroll_details?->gross_salary) }}</td>
                                        </tr>
                                    
                                        <tr>
                                            <th class="bg-light">{{ __('label.taxi_fare') }}</th>
                                            <td>{{ number_format($show_payroll_details?->taxi_fare) }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">{{ __('label.credit_card') }}</th>
                                            <td>{{ number_format($show_payroll_details?->credit_card) }}</td>
                                        </tr>

                                        <tr>
                                            <th class="bg-light">{{ __('label.total_allowances') }}</th>
                                            <td>{{ number_format($show_payroll_details?->total_allowances) }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">{{ __('label.subtotal_salary') }}</th>
                                            <td class="fw-bold">{{ number_format($show_payroll_details?->total_allowances+$show_payroll_details?->gross_salary) }}</td>
                                        </tr>


                                        <tr>
                                            <th class="bg-light">{{ __('label.tax') }}</th>
                                            <td class="text-danger">
                                                {{ number_format($show_payroll_details->tax) }}
                                            </td>
                                        </tr>
                                        
                                        <tr>
                                            <th class="bg-light">{{ __('label.advance_deduction') }}</th>
                                            <td class="text-danger">
                                                {{ number_format($show_payroll_details->advance_deduction) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">{{ __('label.food_deduction') }}</th>
                                            <td class="text-danger">
                                                {{ number_format($show_payroll_details->food_deduction) }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th class="bg-light">{{ __('label.security_saving_deduction') }}</th>
                                            <td class="text-danger">
                                                {{ number_format($show_payroll_details->security_saving_deduction) }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th class="bg-light">{{ __('label.total_deduction') }}</th>
                                            <td class="text-danger">
                                                {{ number_format($show_payroll_details->total_deductions) }}
                                            </td>
                                        </tr>


                                        <tr>
                                            <th class="bg-light">{{ __('label.net_salary') }}</th>
                                            <td class="fw-bold text-success">
                                                {{ number_format($show_payroll_details->net_salary) }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th class="bg-light">{{ __('label.status') }}</th>
                                            <td>
                                                @if($show_payroll_details->status == 'paid')
                                                    <span class="badge bg-success">{{ __('label.paid') }}</span>
                                                @else
                                                    <span class="badge bg-warning">{{ __('label.pending') }}</span>
                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <th class="bg-light">{{ __('label.payment_date') }}</th>
                                            <td>{{ $show_payroll_details->payment_date?->format('Y/m/d') ?? '-' }}</td>
                                        </tr>

                                        <tr>
                                            <th class="bg-light">{{ __('label.note') }}</th>
                                            <td>{{ $show_payroll_details->note ?? '-' }}</td>
                                        </tr>
                                    

                                    </tbody>

                                </table>
                            </div>

                            <!-- --book salary details------------- -->
                            @if($show_payroll_details?->details->count())

                                <hr>

                                <h6 class="fw-bold mb-3">
                                    {{ __('label.book_salary_details') }}
                                </h6>

                                <div class="">
                                    <table class="table table-bordered table-striped">

                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('label.book') }}</th>
                                                <th>{{ __('label.total_days') }}</th>
                                                <th>{{ __('label.daily_rate') }}</th>
                                                <!-- <th>{{ __('label.present_days') }}</th> -->
                                                <th>{{ __('label.unpaid_leave_days') }}</th>
                                                <th>{{ __('label.payable_days') }}</th>
                                                <th>{{ __('label.total_salary') }}</th>
                                            </tr>
                                        </thead>

                                        <tbody>

                                            @foreach($show_payroll_details->details as $index => $detail)

                                                <tr>

                                                    <td>{{ $index + 1 }}</td>

                                                    <td>
                                                        {{ $detail->book?->name ?? '-' }}
                                                    </td>

                                                    <td>
                                                        {{ $detail->total_days_snapshot }}
                                                    </td>

                                                    <td>
                                                        {{ number_format($detail->daily_rate_snapshot) }}
                                                    </td>

                                                    <!-- <td>
                                                        {{ $detail->attendance_count }}
                                                    </td> -->
                                                    <td>
                                                        {{ $detail->unpaid_leave_days }}
                                                    </td>
                                                    <td>
                                                        {{ $detail->payable_days }}
                                                    </td>

                                                    <td class="fw-bold">
                                                        {{ number_format($detail->total_salary) }}
                                                    </td>

                                                </tr>

                                            @endforeach

                                        </tbody>

                                    </table>
                                </div>

                            @endif

                        </div>
                    </div>
                    @endif

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" >{{ __('label.close') }}</button>
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



        $('#employee_id').off('change').on('change', function () {
            $wire.set('employee_id', $(this).val());
        });

        $('#search_position_id').off('change').on('change', function () {
            $wire.set('position_id', $(this).val());
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

<script>
    window.addEventListener('show-print-preview', () => {
        printDiv('printArea');
    });
</script>


