<div>

    @section('title', (($active_menu?->parent?->name ?? '') ? $active_menu?->parent?->name . '-' : '')
        . $active_menu?->name . ' | ' . __('label.app_name'))

    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="row">

            <div class="col-12">

                <!-- ========================= -->
                <!-- MAIN CARD -->
                <!-- ========================= -->
                <div class="card shadow-sm border-0">

                    <!-- HEADER -->
                    <div class="card-header bg-light py-3">

                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 w-100">

                            <!-- LEFT: TITLE -->
                            <h5 class="mb-0 fw-semibold text-start">
                                {{ __('label.expense_budge_dashboard') }}
                            </h5>

                           <div class="d-flex justify-content-md-end align-items-end gap-2 flex-wrap">

                            <div>
                                <label class="form-label small mb-1">
                                    {{ __('label.year') }}
                                </label>

                                <select
                                    class="form-select form-select-sm"
                                    style="width:110px;"
                                    wire:model.lazy="year">

                                    @foreach($years as $year)

                                        <option value="{{ $year->year }}">
                                            {{ $year->year }}
                                        </option>

                                    @endforeach

                                </select>
                            </div>

                            <div>
                                <label class="form-label small mb-1">
                                    {{ __('label.month') }}
                                </label>

                                <select
                                    class="form-select form-select-sm"
                                    style="width:140px;"
                                    wire:model.lazy="month">

                                    @foreach($months as $month)

                                        <option value="{{ $month->number }}">
                                            {{ $month->name }}
                                        </option>

                                    @endforeach

                                </select>
                            </div>

                        </div>

                        </div>

                    </div>

                    <!-- BODY (main stats only) -->
                
                    <!-- ========================= -->
                    <!-- BRANCH CARDS (BELOW FOOTER) -->
                    <!-- ========================= -->

                   @if($view_mode === 'dashboard')

                   <div class="card-body pt-4">

                        <div class="mb-3">
                            <h6 class="text-muted mb-0">
                                {{ __('label.branch_expense_budget') }}
                            </h6>
                        </div>

                        <div class="row g-3">

                            @foreach($branch_expense_budget_stats as $stat)

                            <div class="col-12 col-md-6 col-lg-4 col-xl-3">

                                <div class="card border shadow-sm h-100">

                                    <!-- HEADER -->
                                    <div class="card-header bg-light">

                                        <strong>

                                            {{ $stat['branch_name'] }}

                                        </strong>

                                    </div>

                                    <!-- BODY -->
                                    <div class="card-body">

                                        {{-- Budget --}}
                                        <small class="text-primary">

                                            {{ __('label.budget') }}

                                            ({{ number_format($stat['budget']) }})

                                        </small>

                                        <div class="progress mb-2" >

                                            <div class="progress-bar bg-primary"
                                                style="width:100%">

                                            </div>

                                        </div>


                                       {{-- Paid --}}
                                        <div class="d-flex justify-content-between">

                                            <small class="text-success">

                                                {{ __('label.paid') }}

                                                ({{ number_format($stat['paid']) }})

                                            </small>

                                            <small class="fw-bold text-success">

                                                {{ $stat['actual_paid_percent'] }}%

                                            </small>

                                        </div>

                                        <div class="progress mb-2">

                                            <div class="progress-bar bg-success"
                                                style="width: {{ min($stat['paid_percent'], 100) }}%">

                                            </div>

                                        </div>


                                        {{-- Remaining --}}
                                        <div class="d-flex justify-content-between">

                                            <small class="text-danger">

                                                {{ __('label.remaining') }}

                                                ({{ number_format($stat['remaining']) }})

                                            </small>

                                            <small class="fw-bold text-danger">

                                                {{ $stat['remaining_percent'] }}%

                                            </small>

                                        </div>

                                        <div class="progress">

                                            <div class="progress-bar bg-danger"
                                                style="width: {{ min($stat['remaining_percent'], 100) }}%">

                                            </div>

                                        </div>
                                    </div>

                                    <!-- FOOTER -->
                                    <div class="card-footer bg-white d-flex justify-content-between align-items-center">

                                        <div>


                                        </div>

                                        <button
                                            class="btn btn-sm btn-outline-primary"
                                            wire:click="openBranchExpenseBudgetDetails({{ $stat['branch_id'] }})">

                                            <i class="bx bx-detail me-1"></i>

                                            {{ __('label.details') }}

                                        </button>

                                    </div>

                                </div>

                            </div>

                            @endforeach

                        </div>

                    </div>


                    @endif

                    <!-- ----------branch_financial_section --------------------------- -->
                        @if($view_mode === 'branch_expense_section')

                        @if($view_mode === 'branch_expense_section')

                            <div class="card">

                                <!-- HEADER -->
                                <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">

                                    <div>

                                        <h5 class="mb-1">
                                            {{ __('label.section_breakdown') }}
                                        </h5>

                                        <small class="text-muted">

                                            <span class="text-primary fw-semibold">
                                                {{ __('label.branch') }} :
                                            </span>

                                            {{ $selected_branch_name }}

                                        </small>

                                    </div>


                                    <div>

                                        <button
                                            class="btn btn-sm btn-outline-secondary"
                                            wire:click="$set('view_mode','dashboard')">

                                            <i class="bx bx-arrow-back me-1"></i>

                                            {{ __('label.back') }}

                                        </button>

                                    </div>

                                </div>


                                <!-- BODY -->
                                <div class="card-body">

                                    <div class="row g-3">

                                        @foreach($branch_expense_section_stats as $stat)

                                        <div class="col-12 col-md-6 col-lg-4 col-xl-3">

                                            <div class="card border shadow-sm h-100">


                                                <!-- HEADER -->
                                                <div class="card-header bg-light">

                                                    <strong>

                                                        {{ $stat['section_name'] }}

                                                    </strong>

                                                </div>



                                                <!-- BODY -->
                                                <div class="card-body">


                                                    <!-- Budget -->

                                                    <small class="text-primary">

                                                        {{ __('label.budget') }}

                                                        ({{ number_format($stat['budget']) }})

                                                    </small>


                                                    <div class="progress mb-2" >

                                                        <div class="progress-bar bg-primary"
                                                            style="width: {{ $stat['budget_percent'] }}%">

                                                        </div>

                                                    </div>




                                                    <!-- Paid -->

                                                    <div class="d-flex justify-content-between">

                                                        <small class="text-success">

                                                            {{ __('label.paid') }}

                                                            ({{ number_format($stat['paid']) }})

                                                        </small>


                                                        <small class="fw-bold text-success">

                                                            {{ $stat['actual_paid_percent'] }}%

                                                        </small>


                                                    </div>


                                                    <div class="progress mb-2" >

                                                        <div class="progress-bar 
                                                           bg-success"
                                                            
                                                            style="width: {{ $stat['paid_percent'] }}%">

                                                        </div>

                                                    </div>

                                                    <!-- Remaining -->

                                                    <div class="d-flex justify-content-between">

                                                        <small class="text-danger">

                                                            {{ __('label.remaining') }}

                                                            ({{ number_format($stat['remaining']) }})

                                                        </small>


                                                        <small class="fw-bold text-danger">

                                                            {{ $stat['remaining_percent'] }}%

                                                        </small>


                                                    </div>


                                                    <div class="progress" >

                                                        <div class="progress-bar bg-danger"

                                                            style="width: {{ $stat['remaining_percent'] }}%">

                                                        </div>

                                                    </div>


                                                </div>




                                                <!-- FOOTER -->

                                                <div class="card-footer bg-white d-flex justify-content-between align-items-center">


                                                    <div>

                                                       


                                                    </div>



                                                    <button
                                                        class="btn btn-sm btn-outline-primary"

                                                        wire:click="openSectionExpenseBudgetDetails({{ $stat['section_id'] }})">


                                                        <i class="bx bx-detail me-1"></i>

                                                        {{ __('label.details') }}


                                                    </button>


                                                </div>


                                            </div>

                                        </div>


                                        @endforeach


                                    </div>


                                </div>


                            </div>

                            @endif

                        @endif



                    <!-- ==============section_financial_details ----------------------- -->



                    <!-- -------------------each category recoreds-------------------------------- -->
                        @if($view_mode === 'section_expense_category')
                            <div class="card">

                                <!-- HEADER -->
                                <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">

                                    <div>

                                        <h5 class="mb-1">
                                            {{ __('label.expense_budget_breakdown') }}
                                        </h5>

                                        <small class="text-muted">

                                            <span class="text-primary fw-semibold">
                                                {{ __('label.branch') }} :
                                            </span>

                                            {{ $selected_branch_name }}

                                            |

                                            <span class="text-primary fw-semibold">
                                                {{ __('label.section') }} :
                                            </span>

                                            {{ $selected_section_name }}

                                        </small>

                                    </div>

                                    <button
                                        class="btn btn-sm btn-outline-secondary"
                                        wire:click="$set('view_mode','branch_expense_section')">

                                        <i class="bx bx-arrow-back me-1"></i>

                                        {{ __('label.back') }}

                                    </button>

                                </div>


                                <!-- BODY -->
                                <div class="card-body">

                                    <div class="row g-3">

                                        @foreach($section_expense_category_stats as $stat)

                                        <div class="col-12 col-md-6 col-lg-4 col-xl-3">

                                            <div class="card border shadow-sm h-100">

                                                <!-- HEADER -->
                                                <div class="card-header bg-light">

                                                    <strong>

                                                        {{ $stat['category_name'] }}

                                                    </strong>

                                                </div>


                                                <!-- BODY -->
                                                <div class="card-body">

                                                    {{-- Budget --}}

                                                    <small class="text-primary">

                                                        {{ __('label.budget') }}

                                                        ({{ number_format($stat['budget']) }})

                                                    </small>

                                                    <div class="progress mb-2" >

                                                        <div class="progress-bar bg-primary"
                                                            style="width: {{ $stat['budget_percent'] }}%">

                                                        </div>

                                                    </div>


                                                    {{-- Paid --}}

                                                    <small class="text-success">

                                                        {{ __('label.paid') }}

                                                        ({{ number_format($stat['paid']) }})

                                                    </small>

                                                    <div class="progress mb-2" >

                                                        <div class="progress-bar bg-success"
                                                            style="width: {{ $stat['paid_percent'] }}%">

                                                        </div>

                                                    </div>


                                                    {{-- Remaining --}}

                                                    <small class="text-danger">

                                                        {{ __('label.remaining') }}

                                                        ({{ number_format($stat['remaining']) }})

                                                    </small>

                                                    <div class="progress mb-2" >

                                                        <div class="progress-bar bg-danger"
                                                            style="width: {{ $stat['remaining_percent'] }}%">

                                                        </div>

                                                    </div>

                                                </div>


                                                <!-- FOOTER -->

                                                <div class="card-footer bg-white d-flex justify-content-between align-items-center">

                                                    <small class="text-muted">

                                                        {{ __('label.paid') }}

                                                    </small>

                                                    <strong>

                                                        {{ $stat['paid_percent'] }}%

                                                    </strong>

                                                </div>

                                            </div>

                                        </div>

                                        @endforeach

                                    </div>

                                </div>

                            </div>
                        @endif
                    
                    <!-- -------------------each category recoreds-------------------------------- -->

                </div>
            </div>

        </div>

    </div>

</div>

