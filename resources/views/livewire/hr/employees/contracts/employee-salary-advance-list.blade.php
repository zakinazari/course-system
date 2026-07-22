
<div>
    @php
    $printId = 'printArea';
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
    <div >
       
       <div class="card-header">
            
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="card-title mb-2 mb-md-0">
                    {{ __('label.salary_advance') }}
                </h5>

                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                

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
        <div class="text-nowrap">
 
            <div class="mb-3 px-3">
   
                <form wire:submit.prevent="applySearch" class="row g-3 align-items-end">
               
                 
                    @if(!auth()->user()->branch_id)
                    <div class="col-md-3">
                        <label class="form-label">{{ __('label.branch') }}</label>
                        <select class="form-select" wire:model.defer="search.branch_id" id ="">
                           <option value="">{{ __('label.all') }}</option>
                           @foreach($branches as $branch)
                                 <option value="{{ $branch->id }}"  wire:key="branch-search-{{ $branch->id }}">
                                    {{ $branch->name }}
                                 </option>
                           @endforeach
                        </select>
                     </div>
                     @endif
                    <div class="col-md-3">
                        <label class="form-label">{{ __('label.status') }}</label>
                        <select class="form-select" wire:model.defer="search.status" id ="">
                           <option value="">{{ __('label.all') }}</option>
                            <option value="active">{{ __('label.active') }}</option>
                            <option value="completed">{{ __('label.completed') }}</option>
                            <option value="cancelled">{{ __('label.cancelled') }}</option>
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
                            <th style="width:40px;">{{ __('label.NO') }}</th>
                            <th>{{ __('label.total_amount') }}</th>
                            <th>{{ __('label.remaining_amount') }}</th>
                            <th>{{ __('label.note') }}</th>
                            <th>{{ __('label.date') }}</th>
                            <th>{{ __('label.status') }}</th>
                            <th>{{ __('label.bill') }}</th>
                            <th>{{ __('label.payment') }}</th>
                            <th>{{ __('label.section') }}</th>
                            <th>{{ __('label.auto_deduct') }}</th>
                            @if(!auth()->user()->branch_id)
                            <th>{{ __('label.branch') }}</th>
                            @endif
                            <th>{{ __('label.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($advances as $i => $advance)
                        <tr>
                            <td>{{ ($advances->currentPage() - 1) * $advances->perPage() + $i + 1 }}</td>
                            <td>{{ $advance->total_amount }}</td>
                            <td>{{ $advance->remaining_amount }}</td>
                            <td>{{ $advance->note }}</td>
                            <td>{{ $advance->created_at->format('Y/m/d h:i A') }}</td>
                            <td>
                              @if($advance->status==='active')
                              <span class="badge bg-label-success me-1" style="font-size:10px;">{{ ucfirst($advance->status) }}</span>
                              @elseif($advance->status==='completed')
                              <span class="badge bg-label-danger me-1" style="font-size:10px;">{{ ucfirst($advance->status) }}</span>
                              @elseif($advance->status==='cancelled')
                              <span class="badge bg-label-info me-1" style="font-size:10px;">{{ ucfirst($advance->status) }}</span>
                              @endif
                            </td>
                                <td>
                                <a class="btn btn-primary btn-icon rounded-pill"
                                href="javascript:void(0);"
                                wire:click="bill({{ $advance->id }})">
                                    <i class="bx bx-money text-white"></i>
                                </a>
                            </td>

                              <td>
                                <a class="btn btn-success btn-icon rounded-pill"
                                href="javascript:void(0);"
                                wire:click="showAdvancePayments({{ $advance->id }})">
                                    <i class="bx bx-money text-white"></i>
                                </a>
                            </td>
                            <td>{{ $advance->section?->name }}</td>

                            <td>
                                @if($advance->auto_deduct)
                                    <span class="badge bg-label-success me-1" style="font-size:10px;">{{ __('label.enable') }}</span>
                                @else
                                    <span class="badge bg-label-danger me-1" style="font-size:10px;">{{ __('label.disable') }}</span>
                                @endif
                            </td>
                              
                            @if(!auth()->user()->branch_id)
                            <td>{{ $advance->branch?->name }}</td>
                            @endif

                        
                            <td>
                                @if($advance->remaining_amount == $advance->total_amount)
                                <div class="dropdown position-static">
                                    <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if(edit(Auth::user()->role_ids,$active_menu_id))
                                        @if((Auth::user()->id === $advance->user_id && $advance->created_at->format('Y/m/d') == now()->format('Y/m/d')) || Auth::user()->isAdmin() || Auth::user()->isDeveloper())
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="edit({{ $advance->id }})"
                                            ><i class="bx bx-edit-alt me-1 text-success"></i>{{ __('label.edit') }}</a>
                                        @endif
                                        @endif
                                   
                                        @if(delete(Auth::user()->role_ids,$active_menu_id))
                                            @if((Auth::user()->id === $advance->user_id && $advance->created_at->format('Y/m/d') == now()->format('Y/m/d')) || Auth::user()->isAdmin() || Auth::user()->isDeveloper())
                                            <a class="dropdown-item " href="javascript:void(0);"  onclick="confirmDelete({{ $advance->id }},'{{$table_name}}')"
                                            ><i class="bx bx-trash me-1 text-danger"></i>{{ __('label.delete') }}</a>
                                            @endif
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
                {{ $advances->links() }}
            </div>
        </div>
    </div>
    
    <!--/ Bootstrap Table with Header Dark -->
    <div class="modal fade" id="{{$modalId}}" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog modal-lg" branch="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@if($editMode) {{ __('label.editing') }}  @else {{ __('label.adding') }} @endif {{ __('label.salary_advance') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <form @if($editMode) wire:submit.prevent="update" @else wire:submit.prevent="store" @endif>
                    <div class="modal-body">
                        <div class="row">
                            @if(!auth()->user()->branch_id)
                            <div class="col mb-3">
                                <label class="form-label">{{ __('label.branch') }} <span style="color:red;">*</span></label>
                                <select class="form-select @error('branch_id') is-invalid @enderror" wire:model.lazy="branch_id" id ="branch_id">
                                    <option value="">{{ __('label.select') }}</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}"  wire:key="branch-{{ $branch->id }}">
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('branch_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            @endif

                            <div class="col mb-3">
                                <label>{{ __('label.section') }}<span style="color:red;">*</span></label>
                                <div wire:ignore>
                                <select  class="form-control select2" id="advance_form_section_id" wire:model.lazy ="section_id">
                                    <option value="">{{ __('label.select') }}</option>
                                    @foreach($sections as $section)
                                    <option value="{{ $section->id }}" wire:key="sections-section">
                                        {{ $section->name }}
                                    </option>
                                    @endforeach
                                </select>
                                </div>
                              
                            </div>
                           @error('section_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="row">
                            

                            <div class="mb-3">
                                <label>{{ __('label.amount') }}<span style="color:red;">*</span></label>
                                <input type="number" wire:model.lazy="total_amount" class="form-control" min="0">
                                @error('total_amount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.note') }}</label>
                                <textarea type="text" id="nameBasic" class="form-control @error('note') is-invalid @enderror" wire:model.lazy="note"></textarea>
                                @error('note') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label class="form-label d-block">{{ __('label.auto_deduct') }}</label>

                            <div class="form-check form-check-inline">
                                <input type="radio"
                                    class="form-check-input"
                                    value="1"
                                    wire:model="auto_deduct">
                                <label class="form-check-label">
                                    {{ __('label.enable') }}
                                </label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input type="radio"
                                    class="form-check-input"
                                    value="0"
                                    wire:model="auto_deduct">
                                <label class="form-check-label">
                                    {{ __('label.disable') }}
                                </label>
                            </div>

                            @error('auto_deduct')
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

    <div class="modal fade" id="advance_payments_modal" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog modal-lg" branch="document">
            <div class="modal-content">
                <div class="modal-header">
                    
                    <h5 class="modal-title">{{__('label.salary_advance_payments')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                    
                </div>
                
                <div class="modal-body">
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
                                    {{ __('label.payment_date') }}
                                </th>
                            </tr>

                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach($advance_payments as $i => $payment)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $payment->amount}}</td>
                                <td>{{ $payment->payment_date?->format('Y/m/d') }}</td>
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
                                {{ __('label.advance_salary_receipt') }}
                            </h2>

                            <!-- Student Info -->
                            <table style="width:100%;border-collapse:collapse;margin-bottom:20px;">
                                <tr>
                                    <td><b>{{ __('label.employee') }}</b></td>
                                    <td>{{ $employee->name ?? '' }} {{ $employee->last_name ?? '' }}</td>

                                    <td><b>{{ __('label.print_date') }}</b></td>
                                    <td>{{ now()->format('Y/m/d H:i A') }}</td>
                                </tr>
                                <tr>

                                    <td><b>{{ __('label.bill_no') }}</b></td>
                                    <td>#{{ $advance_bill?->id }}</td>

                                    <td><b></b></td>
                                    <td></td>

                                    
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
                                        <td style="padding:8px;border:1px solid #ddd;">{{ $advance_bill?->advance_date?->format('Y/m/d') }}</td>
                                        <td style="padding:8px;border:1px solid #ddd;">{{ $advance_bill?->total_amount }}</td>
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
                                {{ __('label.advance_salary_receipt') }}
                            </h2>

                            <!-- Student Info -->
                            <table style="width:100%;border-collapse:collapse;margin-bottom:20px;">
                                <tr>
                                    <td><b>{{ __('label.employee') }}</b></td>
                                    <td>{{ $employee->name ?? '' }} {{ $employee->last_name ?? '' }}</td>

                                    <td><b>{{ __('label.print_date') }}</b></td>
                                    <td>{{ now()->format('Y/m/d H:i A') }}</td>
                                </tr>
                                <tr>

                                    <td><b>{{ __('label.bill_no') }}</b></td>
                                    <td>#{{ $advance_bill?->id }}</td>

                                    <td><b></b></td>
                                    <td></td>

                                    
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
                                        <td style="padding:8px;border:1px solid #ddd;">{{ $advance_bill?->advance_date?->format('Y/m/d') }}</td>
                                        <td style="padding:8px;border:1px solid #ddd;">{{ $advance_bill?->total_amount }}</td>
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


        $('#advance_form_section_id').off('change').on('change', function () {
            @this.set('section_id', $(this).val());
            
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