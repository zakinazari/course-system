
<div>
    <style>
        #printArea{
            visibility: hidden;
            position: absolute;
            z-index: -9999;
        }
    </style>
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


    <!-- Central Dashboard -->
    @if(!auth()->user()->branch_id)
   
    <div class="row">

        <div class="col-md-6 col-lg-6 col-xl-6 col-xl-6 mb-4">

            <div class="card h-100">

                <div class="card-header">
                    <strong>Resources</strong>
                </div>

                <div class="card-body">

                    <ul class="p-0 m-0">

                        @foreach($central_resource_summary as $resource)

                            <li class="d-flex justify-content-between align-items-center mb-4">

                                <div class="d-flex align-items-center gap-2">

                                    <span class="avatar avatar-sm">
                                        <span class="avatar-initial rounded-circle bg-label-success">
                                            <i class="bx bx-money"></i>
                                        </span>
                                    </span>

                                    <div>

                                        <p class="mb-0">
                                            {{ $resource['name'] }}
                                        </p>

                                    </div>

                                </div>

                                <div class="fw-bold
                                    {{ $resource['amount'] > 0
                                        ? 'text-success'
                                        : ($resource['amount'] < 0 ? 'text-danger' : 'text-warning') }}">
                                    
                                    <i class="bx bx-wallet me-1"></i>

                                    {{ number_format($resource['amount']) }}

                                </div>

                            </li>

                        @endforeach

                    </ul>

                    <hr>

                    <div class="d-flex justify-content-between">

                        <strong>Total Resources</strong>

                        <strong class="
                            {{ $central_resource_total > 0
                                ? 'text-success'
                                : ($central_resource_total < 0 ? 'text-danger' : 'text-warning') }}">
                            
                            {{ number_format($central_resource_total) }}

                        </strong>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-md-6 col-lg-6 col-xl-6 col-xl-6 mb-4">

            <div class="card h-100">

                <div class="card-header">
                    <strong>Finance Sections</strong>
                </div>

                <div class="card-body">

                    <ul class="p-0 m-0">

                        @foreach($central_finance_summary as $finance)

                            <li class="d-flex justify-content-between align-items-start mb-4">

                                {{-- LEFT SIDE --}}
                                <div class="d-flex align-items-start gap-2">

                                    <span class="avatar avatar-sm mt-1">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            <i class="bx bx-category"></i>
                                        </span>
                                    </span>

                                    <div>

                                        {{-- NAME --}}
                                        <p class="mb-0">
                                            {{ $finance['name'] }}
                                        </p>

                                        {{-- INCOME / EXPENSE --}}
                                        <small class="text-muted d-block">

                                            <i class="bx bx-trending-up text-success"></i>
                                            {{ number_format($finance['income']) }}

                                            |

                                            <i class="bx bx-trending-down text-danger"></i>
                                            {{ number_format($finance['expense']) }}

                                        </small>

                                        {{--  ACCOUNT BREAKDOWN (FIXED POSITION) --}}
                                        @if($finance['accounts']->count())

                                            <div class="mt-1 ps-2 border-start">

                                                @foreach($finance['accounts'] as $account)

                                                    <div class="d-flex justify-content-between align-items-center small text-muted py-1">

                                                        <span>
                                                            {{ $account['name'] }}
                                                        </span>

                                                        <div class="d-flex align-items-center gap-2">
                                                            <span class="{{ $account['balance'] > 0 ? 'text-success' : 'text-danger' }}">
                                                                {{ number_format($account['balance']) }}
                                                            </span>
                                                            
                                                            @if($account['balance'] > 0)

                                                                <button class="btn btn-sm btn-primary px-2 py-1"
                                                                    wire:click="openCentralToBranchTransferModal(
                                                                        {{ $finance['id'] }},
                                                                        {{ $account['account_id'] }},
                                                                        {{ $account['balance'] }},
                                                                        'finance'
                                                                    )">

                                                                    <i class="bx bx-transfer-alt"></i>

                                                                </button>

                                                            @endif
                                                        </div>

                                                    </div>

                                                @endforeach

                                            </div>

                                        @endif

                                    </div>

                                </div>

                                {{-- RIGHT SIDE --}}
                                <div class="d-flex align-items-center gap-2">

                                    

                                    <div class="fw-bold
                                        {{ $finance['balance'] > 0
                                            ? 'text-success'
                                            : ($finance['balance'] < 0 ? 'text-danger' : 'text-warning') }}">

                                        <i class="bx bx-wallet me-1"></i>
                                        {{ number_format($finance['balance']) }}

                                    </div>

                                </div>

                            </li>

                        @endforeach

                    </ul>

                    <hr>

                    <div class="d-flex justify-content-between">

                        <strong>Total Finance</strong>

                        <strong class="
                            {{ $central_finance_total > 0
                                ? 'text-success'
                                : ($central_finance_total < 0 ? 'text-danger' : 'text-warning') }}">

                            {{ number_format($central_finance_total) }}

                        </strong>

                    </div>

                </div>

            </div>

        </div>
        
    </div>

    <div class="row">

        <div class="col-md-6 col-lg-6 col-xl-6 col-xl-6 mb-4">

            <div class="card h-100">

                <div class="card-header">
                    <strong>Book Sections</strong>
                </div>

                <div class="card-body">

                    <ul class="p-0 m-0">

                        @foreach($central_book_summary as $book)

                             <li class="d-flex justify-content-between align-items-start mb-4">

                                {{-- LEFT SIDE --}}
                                <div class="d-flex align-items-start gap-2">

                                    <span class="avatar avatar-sm mt-1">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            <i class="bx bx-category"></i>
                                        </span>
                                    </span>

                                    <div>

                                        {{-- NAME --}}
                                        <p class="mb-0">
                                            {{ $book['name'] }}
                                        </p>

                                        {{-- INCOME / EXPENSE --}}
                                        <small class="text-muted d-block">

                                            <i class="bx bx-trending-up text-success"></i>
                                            {{ number_format($book['income']) }}

                                            |

                                            <i class="bx bx-trending-down text-danger"></i>
                                            {{ number_format($book['expense']) }}

                                        </small>

                                        {{--  ACCOUNT BREAKDOWN (FIXED POSITION) --}}
                                        @if($book['accounts']->count())

                                            <div class="mt-1 ps-2 border-start">

                                                @foreach($book['accounts'] as $account)

                                                   <div class="d-flex justify-content-between align-items-center small text-muted py-1">

                                                        <span>
                                                            {{ $account['name'] }}
                                                        </span>

                                                        <div class="d-flex align-items-center gap-2">

                                                            <span class="{{ $account['balance'] > 0 ? 'text-success' : 'text-danger' }}">
                                                                {{ number_format($account['balance']) }}
                                                            </span>

                                                            @if($account['balance'] > 0)

                                                                <button
                                                                    class="btn btn-sm btn-primary px-2 py-1"
                                                                    wire:click="openCentralToBranchTransferModal(
                                                                        {{ $book['id'] }},
                                                                        {{ $account['account_id'] }},
                                                                        {{ $account['balance'] }},
                                                                        'book'
                                                                    )">

                                                                    <i class="bx bx-transfer-alt"></i>

                                                                </button>

                                                            @endif

                                                        </div>

                                                    </div>

                                                     

                                                @endforeach

                                            </div>

                                        @endif

                                    </div>

                                </div>

                                {{-- RIGHT SIDE --}}
                                <div class="d-flex align-items-center gap-2">

                                 

                                    <div class="fw-bold
                                        {{ $book['balance'] > 0
                                            ? 'text-success'
                                            : ($book['balance'] < 0 ? 'text-danger' : 'text-warning') }}">

                                        <i class="bx bx-wallet me-1"></i>
                                        {{ number_format($book['balance']) }}

                                    </div>

                                </div>

                            </li>

                        @endforeach

                    </ul>

                    <hr>

                    <div class="d-flex justify-content-between">

                        <strong>Total Books</strong>

                        <strong class="
                            {{ $central_book_total > 0
                                ? 'text-success'
                                : ($central_book_total < 0 ? 'text-danger' : 'text-warning') }}">

                            {{ number_format($central_book_total) }}

                        </strong>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-md-6 col-lg-6 col-xl-6 col-xl-6 mb-4">

            <div class="card h-100">

                <div class="card-header">
                    <strong>General Treasury Balance</strong>
                </div>

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div class="d-flex align-items-center gap-2">

                            <span class="avatar avatar-sm">
                                <span class="avatar-initial rounded-circle bg-label-info">
                                    <i class="bx bx-wallet"></i>
                                </span>
                            </span>

                            <div>

                                <p class="mb-0">
                                    Total Cash Balance
                                </p>

                                <small class="text-muted">
                                    All sections combined
                                </small>

                            </div>

                        </div>

                        <div class="fw-bold fs-5
                            {{ $central_current_balance > 0
                                ? 'text-success'
                                : ($central_current_balance < 0 ? 'text-danger' : 'text-warning') }}">

                            <i class="bx bx-wallet me-1"></i>

                            {{ number_format($central_current_balance) }}

                        </div>

                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center">

                        <!-- <div>

                            <small class="text-muted">Previous Balance</small>

                            <div class="fw-bold
                                {{ $central_previous_balance > 0
                                    ? 'text-success'
                                    : ($central_previous_balance < 0 ? 'text-danger' : 'text-warning') }}">

                                {{ number_format($central_previous_balance) }}

                            </div>

                        </div> -->

                        <!-- <div>

                            <small class="text-muted">Current Balance</small>

                            <div class="fw-bold
                                {{ $central_current_balance > 0
                                    ? 'text-success'
                                    : ($central_current_balance < 0 ? 'text-danger' : 'text-warning') }}">

                                {{ number_format($central_current_balance) }}

                            </div>

                        </div> -->

                    </div>

                </div>

            </div>

        </div>
     
    </div>

    @endif


    <!----------------------- Start Branches Dashboard ------------------------------->
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

                <div class="row">

                    {{-- ================= LEFT: FINANCIAL ================= --}}
                    <div class="col-md-6">

                        <div class="card">
                            <div class="card-header">
                                <strong>Financial Summary</strong>
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
                                                    wire:click="openBranchToCentralTransferModal(
                                                        {{ $financial_summary['id'] }},
                                                        {{ $financial_summary['balance'] }},
                                                        'finance',
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
                                        {{ $daily_financial_total > 0 ? 'text-success' : ( $daily_financial_total < 0 ? 'text-danger' : 'text-warning' ) }}">
                                        <i class="bx bx-wallet me-1"></i>
                                        {{ $daily_financial_total }}
                                    </span>

                                </div>

                            </div>
                        </div>

                    </div>

                    {{-- ================= RIGHT: BOOK ================= --}}
                    <div class="col-md-6">

                        <div class="card">
                            <div class="card-header">
                                <strong>Book Summary</strong>
                            </div>

                            <div class="card-body">

                                <ul class="p-0 m-0">

                                    @foreach($daily_book_summary as $book)

                                        <li class="d-flex mb-4 justify-content-between align-items-center">

                                            <div class="d-flex align-items-center gap-2">

                                                <span class="avatar avatar-sm">
                                                    <span class="avatar-initial rounded-circle bg-label-warning">
                                                        <i class="bx bx-book"></i>
                                                    </span>
                                                </span>

                                                <div>
                                                    <p class="mb-0 lh-1">{{ $book['name'] }}</p>

                                                    <small class="text-muted">
                                                        <i class="bx bx-trending-up text-success"></i>
                                                        {{ $book['income'] }}
                                                    </small>
                                                </div>

                                            </div>

                                            @if($book['balance'] > 0)

                                                @if(add(Auth::user()->role_ids,$active_menu_id))
                                                <button
                                                    class="btn btn-sm btn-primary"
                                                    wire:click="openBranchToCentralTransferModal(
                                                        {{ $book['id'] }},
                                                        {{ $book['balance'] }},
                                                        'book',
                                                    )">

                                                    <i class="bx bx-transfer-alt"></i>
                                                    Transfer

                                                </button>
                                                @endif

                                            @endif

                                            <div class="fw-bold  {{ $book['balance'] > 0 ? 'text-success' : ( $book['balance'] < 0 ? 'text-danger' : 'text-warning') }}">
                                                <i class="bx bx-wallet me-1"></i>
                                                {{ $book['balance'] }}
                                            </div>

                                        </li>

                                    @endforeach

                                </ul>

                                <hr>
                               
                                <div class="d-flex justify-content-between align-items-center">

                                    <strong>Total Book Sale</strong>

                                    <span class="fs-5 fw-bold text-success  {{ $daily_book_total > 0 ? 'text-success' : ( $daily_book_total < 0 ? 'text-danger' : 'text-warning') }}">
                                        <i class="bx bx-wallet me-1"></i>
                                        {{ $daily_book_total }}
                                    </span>

                                </div>

                            </div>
                        </div>

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
                     <a class="btn btn-secondary d-flex align-items-center gap-2"
                    href="#"
                        wire:click.prevent="print">
                        <i class="fa fa-print"></i>
                        {{ __('label.print') }}
                    </a>
                </div>
            </div>
        </div>
        <hr>
        <div class="table-responsive text-nowrap">
 
            <div class="mb-3 px-3">
                <form wire:submit.prevent="applySearch" class="row g-3 align-items-end">
                      @if(!auth()->user()->branch_id)
                    <div class="col-md-4">
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
                    <div class="col-md-4">
                        <label class="form-label" >{{ __('label.section') }}</label>
                        <select wire:model="search.section_id" class="form-control">
                            <option value="">{{ __('label.all') }}</option>

                            @foreach($sections as $sec)

                                <option value="{{ $sec->id }}">{{ $sec->name }}</option>

                            @endforeach

                        </select>
                    </div>

                   

                    <div class="col-md-4">
                        <label class="form-label" >{{ __('label.transfer_type') }}</label>
                        <select wire:model="search.transfer_type" class="form-control">
                            <option value="">{{ __('label.all') }}</option>

                            <option value="transfer_in">{{ __('label.transfer_in') }}</option>
                            <option value="transfer_out">{{ __('label.transfer_out') }}</option>

                        </select>
                    </div>

                     <div class="col-md-4">
                        <label class="form-label" >{{ __('label.module_type') }}</label>
                        <select wire:model="search.module_type" class="form-control">
                            <option value="">{{ __('label.all') }}</option>

                            <option value="finance">{{ __('label.finance_module') }}</option>
                            <option value="book">{{ __('label.book_module') }}</option>

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
                        <option value="">{{ __('label.all') }}</option>
                    </select>
                    <span>{{ __('label.entries') }}</span>
                </div>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead class="table-dark">
                        <tr>
                            <th>{{ __('label.NO') }}</th>
                           
                            <th>{{ __('label.section') }}</th>
                            @if(!Auth::user()->branch_id)
                            <th>{{ __('label.branch') }}</th>
                            @endif
                            <th>{{ __('label.module_type') }}</th>
                             <th>{{ __('label.amount') }}</th>
                            <th>{{ __('label.transfer_type') }}</th>
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
                            
                            
                            <td>{{ $ef->section?->name }}</td>
                       
                            @if(!Auth::user()->branch_id)
                            <td>
                                @if($ef->transfer_type==='transfer_out')

                                    {{ $ef->toAccount?->branch?->name }}

                                @elseif($ef->transfer_type==='transfer_in')

                                    {{ $ef->fromAccount?->branch?->name }}

                                @endif
                            </td>
                            @endif

                            <td>{{ ucfirst($ef->module_type) }}</td>
                            <td>{{ $ef->amount }}</td>
                            <td>
                                @if($ef->transfer_type == 'transfer_out')

                                <span class="badge bg-danger">
                                    <i class="bx bx-up-arrow-alt"></i>
                                     Out
                                </span>

                            @elseif($ef->transfer_type == 'transfer_in')

                                <span class="badge bg-success">
                                    <i class="bx bx-down-arrow-alt"></i>
                                     In
                                </span>

                            @endif
                            </td>

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

                                            @if($ef->status === 'pending' && $ef->transfer_type ==='transfer_in')
                                            
                                            <a
                                                class="dropdown-item"
                                                href="javascript:void(0);"
                                                wire:click="openApproveModal({{ $ef->id }})">

                                                <i class="bx bx-check-circle me-1 text-success"></i>
                                                {{ __('label.approve') }}

                                            </a>

                                            @if(Auth::user()->branch_id == null || Auth::user()->branch_id =='')
                                                <a
                                                    class="dropdown-item"
                                                    href="javascript:void(0);"
                                                    wire:click="openRejectModal({{ $ef->id }})">

                                                    <i class="bx bx-x-circle me-1 text-danger"></i>
                                                    {{ __('label.reject') }}

                                                </a>

                                                @endif

                                            @endif
                                        @endif

                                        @if(delete(Auth::user()->role_ids,$active_menu_id))
                                            @if((Auth::user()->id === $ef->created_by && $ef->created_at->format('Y/m/d') == now()->format('Y/m/d')) || Auth::user()->isAdmin() || Auth::user()->isDeveloper())
                                                @if($ef->status !='approved')
                                                <!-- <a
                                                    class="dropdown-item"
                                                    href="javascript:void(0);"
                                                    onclick="confirmDelete({{ $ef->id }},'{{$table_name}}')">

                                                    <i class="bx bx-trash me-1 text-danger"></i>
                                                    {{ __('label.delete') }}

                                                </a> -->

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

    @php
        $total_transfer_in = $external_funds->sum(function ($ef) {
            return $ef->transfer_type === 'transfer_in' ? $ef->amount : 0;
        });

        $total_transfer_out = $external_funds->sum(function ($ef) {
            return $ef->transfer_type === 'transfer_out' ? $ef->amount : 0;
        });
    @endphp

    <div id="printArea" style="display:none">

        <!-- Logo -->
        <div style="text-align:center;margin-bottom:10px;">
            <img src="{{ asset('logo.png') }}" style="height:70px;">
        </div>

        <!-- Title -->
        <h2 style="text-align:center;">
            {{ __('label.account_transfer_report') }}
        </h2>

        <!-- Date -->
        <div style="margin-bottom:5px; font-size:12px;">
            {{ __('label.from_date') }}: {{ $search['from'] ?? '---' }}
            &nbsp; {{ __('label.to_date') }}: {{ $search['to'] ?? '---' }}
        </div>

        <table class="table table-bordered">

            <thead>
                <tr>
                    <th>{{ __('label.NO') }}</th>
                   
                    <th>{{ __('label.section') }}</th>
                   
                    @if(!Auth::user()->branch_id)
                        <th>{{ __('label.branch') }}</th>
                    @endif
                    <th>{{ __('label.module_type') }}</th>
                    <th>{{ __('label.amount') }}</th>
                    <th>{{ __('label.transfer_type') }}</th>
                    <th>{{ __('label.status') }}</th>
                    <th>{{ __('label.transaction_date') }}</th>
                    <th>{{ __('label.note') }}</th>
                </tr>
            </thead>

            <tbody>

                @foreach($external_funds as $i => $ef)

                    <tr>

                        <td>
                            {{ ($external_funds->currentPage() - 1) * $external_funds->perPage() + $i + 1 }}
                        </td>

                       

                        <td>{{ $ef->section?->name }}</td>

                        @if(!Auth::user()->branch_id)
                            <td>
                                @if($ef->transfer_type === 'transfer_out')
                                    {{ $ef->toAccount?->branch?->name }}
                                @elseif($ef->transfer_type === 'transfer_in')
                                    {{ $ef->fromAccount?->branch?->name }}
                                @endif
                            </td>
                        @endif

                        <td>{{ ucfirst($ef->module_type) }}</td>

                         <td>{{ $ef->amount }}</td>

                        <td>
                            @if($ef->transfer_type == 'transfer_out')
                                <span class="badge bg-danger">Out</span>
                            @elseif($ef->transfer_type == 'transfer_in')
                                <span class="badge bg-success">In</span>
                            @endif
                        </td>

                        <td>{{ ucfirst($ef->status) }}</td>

                        <td>{{ $ef->transaction_date->format('Y/m/d') }}</td>

                        <td>{{ $ef->note }}</td>

                    </tr>

                @endforeach

            </tbody>

            <!-- TOTAL -->
           
                @if($total_transfer_in > 0)
                <tr>
                    <td colspan="{{ Auth::user()->branch_id ? 3 : 4 }}" class="text-end fw-bold">
                        Total Transfer In
                    </td>
                    <td colspan="" class="text-success fw-bold">
                        {{ number_format($total_transfer_in) }}
                    </td>
                      <td colspan="4" class="text-danger fw-bold">
                       &nbsp;
                    </td>
                </tr>
                @endif

                @if($total_transfer_out > 0)
                <tr>
                    <td colspan="{{ Auth::user()->branch_id ? 3 : 4 }}" class="text-end fw-bold">
                        Total Transfer Out
                    </td>
                    <td colspan="" class="text-danger fw-bold">
                        {{ number_format($total_transfer_out) }}
                    </td>
                    <td colspan="4" class="text-danger fw-bold">
                       &nbsp;
                    </td>
                </tr>
                @endif
           

        </table>

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
                        {{ $transfer_direction == 'central_to_branch'
                            ? 'Transfer to Branch Treasury'
                            : 'Transfer to Central Treasury' }}
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>
                </div>

                <div class="modal-body">

                    <div class="alert alert-danger">

                        @if($transfer_direction == 'central_to_branch')

                            Are you sure you want to transfer

                            <strong>{{ number_format($show_amount) }}</strong>

                            to {{ $branch_account?->name }} treasury?

                        @else

                            Are you sure you want to transfer

                            <strong>{{ number_format($show_amount) }}</strong>

                            to the central treasury?

                        @endif

                    </div>

                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label">{{ __('label.amount') }} <span style="color:red;">*</span></label>
                        <input type="number" min="1" id="nameBasic" class="form-control @error('amount') is-invalid @enderror" wire:model.lazy="amount">
                        @error('amount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
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


<script>
    window.addEventListener('show-print-preview', () => {

        $('#printArea').show();

        printDiv('printArea');

        setTimeout(function () {

            $('#printArea').hide();

        }, 3500);

    });


</script>
