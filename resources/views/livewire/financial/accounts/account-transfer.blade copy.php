
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

    <!-- Start Dashboard -->
    @if(auth()->user()->branch_id)
    <div class="row">
        <div class="col-md-6 col-lg-6 col-xl-6 col-xl-6 mb-4">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">
                        <i class="bx bx-wallet me-1"></i>
                        Previous Treasury Balance
                    </h5>
                </div>

                <div class="card-body">

                    <ul class="p-0 m-0">

                       @foreach($previous_treasury_balances as $prev)

                            <li class="d-flex mb-4 justify-content-between align-items-center">

                                <div class="d-flex align-items-center gap-2">

                                    <span class="avatar avatar-sm">
                                        <span class="avatar-initial rounded-circle bg-label-info">
                                            <i class="bx bx-category"></i>
                                        </span>
                                    </span>

                                    <div>
                                        <p class="mb-0 lh-1">{{ $prev['name'] }}</p>

                                       <small class="text-muted">Total {{ $prev['name'] }} Balance</small>
                                    </div>

                                </div>

                                <div class="fw-bold
                                    {{ $prev['balance'] > 0 ? 'text-success' : ( $prev['balance'] < 0 ? 'text-danger' : 'text-warning') }}">

                                    <i class="bx bx-wallet me-1"></i>
                                    {{ $prev['balance'] }}

                                </div>

                            </li>

                        @endforeach

                    </ul>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center">

                        <strong>Previous Balance</strong>

                        <span class="fs-5 fw-bold
                            {{ $previous_balance > 0 ? 'text-success' : ( $previous_balance < 0 ? 'text-danger' : 'text-warning') }}">
                            
                            <i class="bx bx-wallet me-1"></i>
                            {{ $previous_balance }}

                        </span>

                    </div>

                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-6 col-xl-6 mb-4">

            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">
                        <i class="bx bx-wallet me-1"></i>
                        Current Treasury Balance
                    </h5>
                </div>

                <div class="card-body">

                    <ul class="p-0 m-0">

                       @foreach($current_treasury_balances as $ctb)

                            <li class="d-flex mb-4 justify-content-between align-items-center">

                                <div class="d-flex align-items-center gap-2">

                                    <span class="avatar avatar-sm">
                                        <span class="avatar-initial rounded-circle bg-label-info">
                                            <i class="bx bx-category"></i>
                                        </span>
                                    </span>

                                    <div>
                                        <p class="mb-0 lh-1">{{ $ctb['name'] }}</p>

                                       <small class="text-muted">Total {{ $ctb['name'] }} Balance</small>
                                    </div>

                                </div>

                                <div class="fw-bold
                                    {{ $ctb['balance'] > 0 ? 'text-success' : ($ctb['balance'] < 0 ? 'text-danger' : 'text-warning') }}">

                                    <i class="bx bx-wallet me-1"></i>
                                    {{ $ctb['balance'] }}

                                </div>

                            </li>

                        @endforeach

                    </ul>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center">

                        <strong>Current Balance</strong>

                        <span class="fs-5 fw-bold {{ $current_balance > 0 ? 'text-success' : ($current_balance < 0  ? 'text-danger' : 'text-warning') }}">
                            
                        <i class="bx bx-wallet me-1"></i>
                            {{ $current_balance }}

                        </span>

                    </div>

                </div>

            </div>

        </div>
    </div>

    <div class="row">
        
        <!-- ==========balance -->
        <div class="col-md-12 col-lg-12 col-xl-12 col-xl-12 mb-4">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">
                        <i class="bx bx-wallet me-1"></i>
                        Daily Financial Summary
                    </h5>
                    <div class="dropdown">

                    <button
                        class="btn btn-sm btn-outline-secondary dropdown-toggle"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">

                        <i class="bx bx-calendar me-1"></i>

                        {{ $from_date ?? now()->toDateString() }}

                        -

                        {{ $to_date ?? now()->toDateString() }}

                    </button>

                    <div class="dropdown-menu dropdown-menu-end p-3" style="min-width:300px;">

                        <div class="mb-3">

                            <label class="form-label"> From Date </label>

                            <input type="date" class="form-control" wire:model.lazy="from_date">

                            </div>

                            <div class="mb-3">

                                <label class="form-label">To Date</label>

                                <input type="date" class="form-control" wire:model.lazy="to_date">

                            </div>

                            <!-- <button
                                class="btn btn-primary w-100"
                                wire:click="dailyFinancialSummary">

                                <i class="bx bx-search-alt"></i>
                                Search

                            </button> -->

                        </div>

                    </div>
                </div>

                <div class="card-body">

                    <ul class="p-0 m-0">

                        @foreach($daily_financial_summary as $financial_summary)

                            <li class="d-flex mb-4 justify-content-between align-items-center">

                                <div class="d-flex align-items-center gap-2">

                                    <span class="avatar avatar-sm">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            <i class="bx bx-category"></i>
                                        </span>
                                    </span>

                                    <div>
                                        <p class="mb-0 lh-1">{{ $financial_summary['name'] }}</p>

                                        <small class="text-muted">
                                            <i class="bx bx-trending-up text-success"></i>
                                            {{ $financial_summary['income'] }}

                                            |

                                            <i class="bx bx-trending-down text-danger"></i>
                                            {{ $financial_summary['expense'] }}
                                        </small>
                                    </div>

                                </div>
                                
                                @if($financial_summary['balance'] > 0)

                                    @if(add(Auth::user()->role_ids,$active_menu_id))
                                    <button
                                        class="btn btn-sm btn-primary"
                                        wire:click="openTransferModal(
                                            {{ $financial_summary['id'] }},
                                            {{ $financial_summary['balance'] }}
                                            )">

                                        <i class="bx bx-transfer-alt"></i>
                                        Transfer

                                    </button>
                                    @endif

                                @endif

                                <div class="fw-bold
                                    {{ $financial_summary['balance'] > 0 ? 'text-success' : ( $financial_summary['balance'] < 0 ? 'text-danger' : 'text-warning') }}">

                                    <i class="bx bx-wallet me-1"></i>
                                    {{ $financial_summary['balance'] }}

                                </div>

                            </li>

                        @endforeach

                    </ul>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center">

                        <strong>Total Amount</strong>

                        <span class="fs-5 fw-bold
                            {{ $daily_financial_total > 0 ? 'text-success' : ( $daily_financial_total < 0 ?  'text-danger' : 'text-warning' ) }}">
                            <i class="bx bx-wallet me-1"></i>
                            {{ $daily_financial_total }}

                        </span>

                    </div>

                </div>
            </div>
        </div>

    </div>
    <!-- Start Dashboard -->

   @endif

    <div class="card">
       
       <div class="card-header">
      
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="card-title mb-2 mb-md-0">
                    {{ $active_menu?->name }}
                </h5>
            
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <!-- Export Button -->
                    

                    <!-- Add New Record Button -->

                </div>
            </div>
        </div>
        <hr>
        <div class="table-responsive text-nowrap">
 
            <div class="mb-3 px-3">
                <form wire:submit.prevent="applySearch" class="row g-3 align-items-end">

                    <div class="col-md-3">
                        <label class="form-label" >{{ __('label.section') }}</label>
                        <select wire:model="search.section_id" class="form-control">
                            <option value="">{{ __('label.all') }}</option>

                            @foreach($sections as $sec)

                                <option value="{{ $sec->id }}">{{ $sec->name }}</option>

                            @endforeach

                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">{{ __('label.from_date') }}</label>
                        <div class="input-group input-daterange" id="bs-datepicker-daterange">
                            <input type="date" id="dateRangePicker" class="form-control" wire:model="search.from">
                            <span class="input-group-text">{{ __('label.to_date') }}</span>
                            <input type="date"  class="form-control" wire:model="search.to">
                        </div>
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
                            <th>{{ __('label.account') }}</th>
                            <th>{{ __('label.amount') }}</th>
                            <th>{{ __('label.section') }}</th>
                            <th>{{ __('label.status') }}</th>
                            <th>{{ __('label.transaction_date') }}</th>
                            <th>{{ __('label.note') }}</th>
                            <th>{{ __('label.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($external_funds as $i => $ef)
                        <tr>
                            <td>{{ ($external_funds->currentPage() - 1) * $external_funds->perPage() + $i + 1 }}</td>
                            <td>{{ $ef->account?->name }}</td>
                            <td>{{ $ef->amount }}</td>
                            <td>{{ $ef->section?->name }}</td>
                            <td>
                                
                                @if($ef->status==='pending')
                                <span class="badge bg-label-warning me-1" style="font-size:10px;">{{ ucfirst($ef->status) }}</span>
                                @elseif($ef->status==='approved')
                                <span class="badge bg-label-success me-1" style="font-size:10px;">{{ ucfirst($ef->status) }}</span>
                                @elseif($ef->status==='rejected')
                                <span class="badge bg-label-danger me-1" style="font-size:10px;">{{ ucfirst($ef->status) }}</span>
                                @endif
                            </td>
                            <td>{{ $ef->transaction_date->format('Y/m/d') }}</td>
                            <td>{{ $ef->note }}</td>
                            <td>
                                <div class="dropdown position-static">
                                    <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if(confirm(Auth::user()->role_ids,$active_menu_id))

                                            @if($ef->status === 'pending')
                                            <a
                                                class="dropdown-item"
                                                href="javascript:void(0);"
                                                wire:click="openRejectModal({{ $ef->id }})">

                                                <i class="bx bx-x-circle me-1 text-danger"></i>
                                                {{ __('label.reject') }}

                                            </a>

                                            <a
                                                class="dropdown-item"
                                                href="javascript:void(0);"
                                                wire:click="openApproveModal({{ $ef->id }})">

                                                <i class="bx bx-check-circle me-1 text-success"></i>
                                                {{ __('label.approve') }}

                                            </a>
                                            @endif
                                        @endif

                                        @if(delete(Auth::user()->role_ids,$active_menu_id))
                                            @if((Auth::user()->id === $ef->created_by && $ef->created_at->format('Y/m/d') == now()->format('Y/m/d')) || Auth::user()->isAdmin() || Auth::user()->isDeveloper())
                                                @if($ef->status !='approved')
                                                <a
                                                    class="dropdown-item"
                                                    href="javascript:void(0);"
                                                    onclick="confirmDelete({{ $ef->id }},'{{$table_name}}')">

                                                    <i class="bx bx-trash me-1 text-danger"></i>
                                                    {{ __('label.delete') }}

                                                </a>

                                                @endif

                                            @endif
                                        @endif

                                    </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 justify-content-end px-3">
                {{ $external_funds->links() }}
            </div>
        </div>
    </div>
    
    <!--/ Bootstrap Table with Header Dark -->
  

   <div class="modal fade" id="approveModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('label.approve') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="alert alert-solid-danger" role="alert">
                       Are you sure you want to approve this transaction?
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">{{__('label.close')}}</button>

                    <button class="btn btn-success" type="button" wire:click="approve" wire:loading.attr="disabled">
                        {{ __('label.approve') }}
                    </button>
                </div>

            </div>
        </div>
    </div>


    <div class="modal fade" id="rejectModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('label.reject') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="alert alert-solid-danger" role="alert">
                       Are you sure you want to reject this transaction?
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label" >{{ __('label.reason') }}</label>
                            <textarea type="text" id="nameBasic" class="form-control @error('note') is-invalid @enderror" wire:model.lazy="note"></textarea>
                            @error('note') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">{{__('label.close')}}</button>

                    <button class="btn btn-danger" type="button" wire:click="reject" wire:loading.attr="disabled">
                        {{ __('label.reject') }}
                    </button>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="{{$modalId}}" tabindex="-1" wire:ignore.self>

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        Transfer to Central Treasury
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>
                </div>

                <div class="modal-body">

                    <div class="alert alert-danger">

                        Are you sure you want to transfer

                        <strong>
                            {{ number_format($amount, 2) }}
                        </strong>

                        to the central treasury account?

                    </div>

                    @error('from_account_id')
                        <div class="invalid-efdback d-block">{{ $message }}</div>
                    @enderror

                    @error('to_account_id')
                        <div class="invalid-efdback d-block">{{ $message }}</div>
                    @enderror

                    @error('section_id')
                        <div class="invalid-efdback d-block">{{ $message }}</div>
                    @enderror

                    @error('amount')
                        <div class="invalid-efdback d-block">{{ $message }}</div>
                    @enderror

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                        Cancel

                    </button>

                    <button
                        type="button"
                        class="btn btn-primary"
                        wire:click="store" wire:loading.attr="disabled">

                        <i class="bx bx-check"></i>
                        Confirm Transfer

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

        $('#search_from_account_id').off('change').on('change', function () {
            @this.set('search.from_account_id', $(this).val());
        });

        $('#from_account_id').off('change').on('change', function () {
            $wire.set('from_account_id', $(this).val());
        });

        $('#search_to_account_id').off('change').on('change', function () {
            @this.set('search.to_account_id', $(this).val());
        });

        $('#to_account_id').off('change').on('change', function () {
            $wire.set('to_account_id', $(this).val());
        });

        $('#section_id').off('change').on('change', function () {
            $wire.set('section_id', $(this).val());
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


