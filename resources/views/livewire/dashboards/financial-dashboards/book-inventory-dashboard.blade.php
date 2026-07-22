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
                                {{ __('label.book_inventory_dashboard') }}
                            </h5>

                            <!-- RIGHT: FILTERS -->
                            <div class="d-flex justify-content-md-end align-items-end gap-3 flex-wrap">

                                <div>
                                    <label class="form-label small mb-1">
                                        {{ __('label.from') }}
                                    </label>
                                    <input type="date"
                                        class="form-control form-control-sm"
                                        style="width:160px;"
                                        wire:model.lazy="from_date">
                                </div>

                                <div>
                                    <label class="form-label small mb-1">
                                        {{ __('label.to') }}
                                    </label>
                                    <input type="date"
                                        class="form-control form-control-sm"
                                        style="width:160px;"
                                        wire:model.lazy="to_date">
                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- BODY (main stats only) -->
                    @if(!auth()->user()->branch_id)

                    <div class="card-body mt-3">

                        @foreach($financial_stats as $item)

                            <div class="d-flex justify-content-between align-items-center mb-1">

                                <small class="text-{{ $item['color'] }}">
                                    {{ $item['label'] }}
                                </small>

                                <small>
                                    {{ number_format($item['count']) }}
                                </small>

                            </div>

                            <div class="progress mb-2" style="height:6px;">
                                <div class="progress-bar bg-{{ $item['color'] }}"
                                    style="width: {{ $item['percent'] }}%">
                                </div>
                            </div>

                        @endforeach

                    </div>
                     <!-- ========================= -->
                    <!-- GLOBAL TOTAL (FIXED FOOTER) -->
                    <!-- ========================= -->
                    <div class="card-footer bg-white border-bottom">

                        <div class="d-flex justify-content-between align-items-center">

                            <!-- LEFT: REFRESH BUTTON -->
                            <button class="btn btn-sm btn-primary"
                                    wire:click="backToDashboard">

                                <i class="bx bx-refresh me-1"></i>
                                {{ __('label.refresh') }}
                            </button>

                            <!-- RIGHT: TOTAL -->
                
                        </div>

                    </div>

                  
                    <!-- ========================= -->
                    <!-- Central Warehouses  -->
                    <!-- ========================= -->
                    @if($view_mode_general === 'dashboard')

                    <div class="card-body pt-4">

                        <div class="mb-4">
                            <h5 class="fw-bold mb-0">
                                <i class="bx bx-buildings me-2"></i>
                                {{ __('label.central_warehouse') }}
                            </h5>
                        </div>

                        <div class="row g-4">

                            @foreach($central_warehouses as $warehouse)

                                <div class="col-md-6 col-xl-4">

                                    <div class="card border-0 shadow-sm h-100">

                                        <!-- HEADER -->
                                        <div class="card-header bg-label-primary border-0">

                                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">

                                                <h6 class="mb-0 fw-bold">
                                                    <i class="bx bx-buildings me-1"></i>
                                                    {{ $warehouse->section?->name }}
                                                </h6>

                                                <span class="badge bg-primary align-self-start align-self-sm-center">
                                                    {{ __('label.warehouse') }}
                                                </span>

                                            </div>

                                        </div>

                                        <!-- BODY -->
                                        <div class="card-body text-center py-4">

                                            <div class="mb-3">
                                                <i class="bx bx-package display-5 text-primary"></i>
                                            </div>

                                            <p class="text-muted mb-0">
                                                {{ $warehouse->name }}
                                            </p>

                                        </div>

                                        <!-- FOOTER -->
                                        <div class="card-footer bg-transparent border-top-0 text-end">

                                            <button class="btn btn-primary rounded-pill px-4"
                                                    wire:click="openCentralWarehouseDetails({{ $warehouse->id }})">

                                                <i class="bx bx-folder-open me-1"></i>
                                               {{ __('label.inventory') }}

                                            </button>

                                        </div>

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    </div>
                    @endif

                    <!-- ========================= -->
                    <!-- BRANCH CARDS (BELOW FOOTER) -->
                    <!-- ========================= -->

                    @if($view_mode_general === 'central_warehouse_inventory')
                    <div class="card">

                        <div class="card-header d-flex justify-content-between align-items-center">

                            <div>

                                <h5>
                                    {{ __('label.inventory') }}
                                </h5>

                                <small class="text-muted">

                                    {{ __('label.warehouse') }}: {{ $selected_central_warehouse_name }} 

                                </small>

                            </div>

                            <button class="btn btn-sm btn-outline-secondary"
                                    wire:click="backToDashboard">
                                <i class="bx bx-arrow-back"></i>
                                {{ __('label.back') }}

                            </button>

                        </div>

                        <div class="card-body">

                            <table class="table table-bordered">

                                <thead>
                                    <tr>
                                        <th>#</th>

                                        <th>{{ __('label.physical_book') }}</th>
            
                                        <th>{{ __('label.available_quantity') }}</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @foreach($central_warehouse_inventory_data as $index => $intentory_data)

                                        <tr>

                                            <td>{{ $index + 1 }}</td>

                                            <td>
                                                {{ $intentory_data?->book?->name}}
                                            </td>

                                            <td>
                                            <span class="px-3 py-2 rounded-pill bg-success-subtle text-success fw-bold"> {{ number_format($intentory_data->quantity) }} </span>
                                            </td>
                                            
                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    </div>
                    @endif

                    <hr>
                  @endif
                    <!-- ========================= -->
                    <!-- BRANCH CARDS (BELOW FOOTER) -->
                    <!-- ========================= -->
                    @if($view_mode === 'dashboard')
                    <div class="card-body pt-4">

                        <div class="mb-4">
                            <h5 class="fw-bold mb-0">
                                <i class="bx bx-buildings me-2"></i>
                                {{ __('label.branches') }}
                            </h5>
                        </div>

                        <div class="row g-4">

                            @foreach($branch_financial_stats as $stat)

                                <div class="col-md-6 col-xl-4">

                                    <div class="card border shadow-sm h-100">

                                        <!-- HEADER -->
                                        <div class="card-header bg-light">

                                            <div class="d-flex justify-content-between align-items-center">

                                                <strong>
                                                    {{ $stat['branch_name'] }}
                                                </strong>

                                                <span class="badge bg-label-success">
                                                    <i class="bx bx-trending-up"></i>
                                                </span>

                                            </div>

                                        </div>

                                        <!-- BODY -->
                                        <div class="card-body text-center py-4">

                                            <small class="text-muted d-block mb-2">
                                                {{ __('label.total_income') }}
                                            </small>

                                            <h2 class="fw-bold text-success mb-0">
                                                {{ number_format($stat['income']) }}
                                            </h2>

                                        </div>

                                        <!-- FOOTER -->
                                        

                                            <div class="card-footer d-flex justify-content-end">

                                                <button class="btn btn-sm btn-outline-primary"
                                                        wire:click="openBranchFinancialDetails({{ $stat['branch_id'] }})">

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
                    @if($view_mode === 'branch_financial_section')

                    <div class="card">

                        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">

                            <!-- LEFT: TITLE + CONTEXT -->
                            <div>

                                <h5 class="mb-1">
                                    {{ __('label.section_breakdown') }}
                                </h5>

                                <small class="text-muted">

                                    <span class="text-primary fw-semibold">
                                        {{ __('label.branch') }}:
                                    </span>

                                    {{ $selected_branch_name }}

                                </small>

                            </div>

                            <!-- RIGHT: BACK BUTTON -->
                            <div>

                                <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1"
                                        wire:click="backToDashboard">

                                    <i class="bx bx-arrow-back"></i>
                                    {{ __('label.back') }}

                                </button>

                            </div>

                        </div>

                        <div class="card-body">

                            <div class="row g-3">

                                @foreach($branch_financial_stats as $stat)

                                    <div class="col-md-4">

                                        <div class="card border shadow-sm h-100">

                                            <!-- HEADER -->
                                            <div class="card-header bg-light">
                                                <strong class="fs-5">
                                                    {{ $stat['section_name'] }}
                                                </strong>
                                            </div>

                                            <!-- BODY -->
                                            <div class="card-body text-center py-4">

                                                <div class="text-muted mb-2">
                                                    {{ __('label.total_income') }}
                                                </div>

                                                <h2 class="fw-bold text-success mb-0">
                                                    {{ number_format($stat['income']) }}
                                                </h2>

                                            </div>

                                            <!-- FOOTER -->
                                            <div class="card-footer d-flex justify-content-end align-items-center">

                                                <button class="btn btn-sm btn-outline-primary"
                                                        wire:click="openSectionFinancialDetails( {{ $stat['section_id'] }} )">

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


                <!-- ==========section financial details------------------------------------- -->
                @if($view_mode === 'section_financial_details')

                <div class="card">

                    <div class="card-header d-flex justify-content-between align-items-center">

                        <div>

                            <h5>
                                {{ __('label.inventory') }}
                            </h5>

                            <small class="text-muted">

                                {{ __('label.branch') }}: {{ $selected_branch_name }}
                                |
                                {{ __('label.section') }}: {{ $selected_section_name }}


                            </small>

                        </div>

                        <button class="btn btn-sm btn-outline-secondary"
                                wire:click="backToBranchFinancialSection">
                            <i class="bx bx-arrow-back"></i>
                            {{ __('label.back') }}

                        </button>

                    </div>

                    <div class="card-body">

                        <table class="table table-bordered">

                            <thead>
                                <tr>
                                    <th>#</th>

                                    <th>{{ __('label.physical_book') }}</th>
                                    <th>{{ __('label.transfer_in') }}</th>
                                    <th>{{ __('label.transfer_out') }}</th>
                                    <th>{{ __('label.sold') }}</th>
                                    <th>{{ __('label.available_quantity') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($section_financial_details as $index => $record)

                                    <tr>

                                        <td>{{ $index + 1 }}</td>

                                        <td>
                                            {{ $record?->book?->name}}
                                        </td>

                                        <td>
                                            {{ abs(number_format($record->transfer_in_quantity)) }}
                                        </td>

                                        <td>
                                            {{ abs(number_format($record->transfer_out_quantity)) }}
                                        </td>

                                        <td>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                                wire:click="openFinancialRecordDetails({{ $record->warehouse?->section_id }}, {{ $record->book_id }})">

                                                <i class="bx bx-receipt me-1"></i>
                                                {{ abs(number_format($record->sold_quantity)) }}
                                            </button>
                                        </td>

                                        <td>
                                           <span class="px-3 py-2 rounded-pill bg-success-subtle text-success fw-bold"> {{ number_format($record->quantity) }} </span>
                                        </td>
                                        
                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

                @endif
                <!-- ==========section financial details------------------------------------- -->
          
                <!-- -------------financial recoreds-------------------------- -->

                @if($view_mode === 'financial_records')

                <div class="card">

                    <div class="card-header d-flex justify-content-between align-items-center">

                        <div>

                            <h5>
                                {{ __('label.financial_records') }}
                            </h5>

                            <small class="text-muted">

                                {{ __('label.branch') }}: {{ $selected_branch_name }}
                                |
                                {{ __('label.section') }}: {{ $selected_section_name }}
                                |
                                {{ ucfirst(str_replace('_', ' ', $selected_category_key)) }}

                            </small>

                        </div>

                        <button class="btn btn-sm btn-outline-secondary"
                                wire:click="backToSectionFinancialDetails">
                            <i class="bx bx-arrow-back"></i>
                            {{ __('label.back') }}

                        </button>

                    </div>

                    <div class="card-body">

                        @includeIf('livewire.dashboards.financial-dashboards.financial-recoreds.' . $records_view, [
                            'records' => $financial_records
                        ])

                    </div>

                </div>

                @endif

                <!-- -------------------each category recoreds-------------------------------- -->

            </div>
            </div>

        </div>

    </div>

</div>


