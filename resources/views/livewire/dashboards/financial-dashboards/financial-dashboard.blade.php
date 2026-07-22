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
                                {{ __('label.financial_dashboard') }}
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

                    @endif
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
                    <!-- BRANCH CARDS (BELOW FOOTER) -->
                    <!-- ========================= -->
                    @if($view_mode === 'dashboard')
                    <div class="card-body pt-4">

                        <div class="mb-3">
                            <h6 class="text-muted mb-0">{{ __('label.branch_results') }}</h6>
                        </div>

                        <div class="row g-3">

                            @foreach($branch_financial_stats as $stat)

                                @php
                                    $total = $stat['income'] + $stat['expense'] + $stat['asset'];
                                    $total = $total ?: 1;

                                    $income_percent  = round(($stat['income'] / $total) * 100, 1);
                                    $expense_percent = round(($stat['expense'] / $total) * 100, 1);
                                    $asset_percent   = round(($stat['asset'] / $total) * 100, 1);
                                    $profit_percent  = round((abs($stat['profit']) / $total) * 100, 1);
                                @endphp

                                <div class="col-12 col-md-6 col-lg-4 col-xl-3">

                                    <div class="card border shadow-sm h-100">

                                        <!-- HEADER -->
                                        <div class="card-header bg-light">
                                            <strong>{{ $stat['branch_name'] }}</strong>
                                        </div>

                                        <!-- BODY -->
                                        <div class="card-body">

                                            <!-- INCOME -->
                                            <small class="text-success">
                                                {{ __('label.income') }} ({{ $stat['income'] }})
                                            </small>
                                            <div class="progress mb-2" style="height:6px;">
                                                <div class="progress-bar bg-success"
                                                    style="width: {{ $income_percent }}%"></div>
                                            </div>

                                            <!-- EXPENSE -->
                                            <small class="text-danger">
                                                {{ __('label.expense') }} ({{ $stat['expense'] }})
                                            </small>
                                            <div class="progress mb-2" style="height:6px;">
                                                <div class="progress-bar bg-danger"
                                                    style="width: {{ $expense_percent }}%"></div>
                                            </div>

                                            <!-- ASSET -->
                                            <small class="text-warning">
                                                {{ __('label.asset') }} ({{ $stat['asset'] }})
                                            </small>
                                            <div class="progress mb-2" style="height:6px;">
                                                <div class="progress-bar bg-warning"
                                                    style="width: {{ $asset_percent }}%"></div>
                                            </div>

                                            <!-- PROFIT -->
                                            <small class="text-primary">
                                                {{ __('label.profit') }} ({{ $stat['profit'] }})
                                            </small>
                                            <div class="progress mb-2" style="height:6px;">
                                                <div class="progress-bar {{ $stat['profit'] >= 0 ? 'bg-primary' : 'bg-danger' }}"
                                                    style="width: {{ $profit_percent }}%"></div>
                                            </div>

                                        </div>

                                        <!-- FOOTER -->
                                        <div class="card-footer bg-white d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">

                                            <!-- LEFT -->
                                            <div class="text-center text-sm-start">
                                                <small class="text-muted">{{ __('label.current_cash') }}</small>
                                                <strong class="ms-1">{{ $stat['current_cash'] }}</strong>
                                            </div>

                                            <!-- RIGHT -->
                                            @if(!$stat['is_general'])
                                            <div class="text-center text-sm-end">

                                                <button class="btn btn-sm btn-outline-primary"
                                                        wire:click="openBranchFinancialDetails({{ $stat['branch_id'] }})">

                                                    <i class="bx bx-detail me-1"></i>
                                                    {{ __('label.details') }}

                                                </button>

                                            </div>
                                            @endif

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

                                    <div class="card border shadow-sm">

                                        <!-- HEADER -->
                                        <div class="card-header bg-light">
                                            <strong>{{ $stat['section_name'] }}</strong>
                                        </div>

                                        <!-- BODY -->
                                        <div class="card-body">

                                            <small class="text-success">
                                                {{ __('label.income') }}: {{ $stat['income'] }}
                                            </small><br>

                                            <small class="text-danger">
                                                {{ __('label.expense') }}: {{ $stat['expense'] }}
                                            </small><br>

                                            <small class="text-warning">
                                                {{ __('label.asset') }}: {{ $stat['asset'] }}
                                            </small><br>

                                            <small class="text-primary">
                                                {{ __('label.profit') }}: {{ $stat['profit'] }}
                                            </small><br>

                                            <small class="text-info">
                                                {{ __('label.current_cash') }}: {{ $stat['current_cash'] }}
                                            </small><br>

                                        </div>

                                        <!-- FOOTER -->
                                        <div class="card-footer d-flex justify-content-between align-items-center">

                                            <small>
                                                {{ __('label.current_cash') }}:
                                                {{ $stat['current_cash'] }}
                                            </small>

                                            <button class="btn btn-sm btn-outline-primary"
                                                    wire:click="openSectionFinancialDetails({{ $stat['section_id'] }})">

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

                    <!-- ==============section_financial_details ----------------------- -->

                    @if($view_mode === 'section_financial_details')

                    <div class="card">

                        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">

                            <div>

                                <h5>
                                    {{ __('label.financial_breakdown') }}
                                </h5>

                                <small class="text-muted">
                                    {{ __('label.branch') }}: {{ $selected_branch_name }}
                                    |
                                    {{ __('label.section') }}: {{ $selected_section_name }}
                                </small>

                            </div>

                            <button
                                class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1"
                                wire:click="backToBranchFinancialSection">

                                <i class="bx bx-arrow-back"></i>

                                {{ __('label.back') }}

                            </button>

                        </div>

                        <div class="card-body">

                            <div class="row g-3">

                                {{-- Income --}}
                                <div class="col-md-6">

                                    <div class="card border-success shadow-sm h-100">

                                        <div class="card-header bg-success text-white">
                                            <strong>{{ __('label.income') }}</strong>
                                        </div>

                                        <div class="card-body">

                                            <div class="d-flex justify-content-between">
                                                <span>{{ __('label.course_fee') }}</span>
                                                <strong>{{ number_format($section_financial_details['course_income']) }}</strong>
                                            </div>

                                           

                                            <div class="d-flex justify-content-between">
                                                <span>{{ __('label.makeup_fee') }}</span>
                                                <strong>{{ number_format($section_financial_details['makeup_fee_income']) }}</strong>
                                            </div>

                                            <div class="d-flex justify-content-between">
                                                <span>{{ __('label.exam_fine') }}</span>
                                                <strong>{{ number_format($section_financial_details['exam_fine_income']) }}</strong>
                                            </div>

                                            <div class="d-flex justify-content-between">
                                                <span>{{ __('label.other_fee') }}</span>
                                                <strong>{{ number_format($section_financial_details['other_fee_income']) }}</strong>
                                            </div>

                                           

                                        </div>

                                        <div class="card-footer d-flex justify-content-between align-items-center">

                                            <strong class="text-success">
                                                {{ number_format($section_financial_details['income']) }}
                                            </strong>

                                            <button
                                                class="btn btn-sm btn-outline-success"
                                                wire:click="openFinancialCategoryDetails('income')">

                                                <i class="bx bx-detail me-1"></i>
                                                {{ __('label.details') }}

                                            </button>

                                        </div>

                                    </div>

                                </div>

                                {{-- Expense --}}
                                <div class="col-md-6">

                                    <div class="card border-danger shadow-sm h-100">

                                        <div class="card-header bg-danger text-white">
                                            <strong>{{ __('label.expense') }}</strong>
                                        </div>

                                        <div class="card-body">

                                            <div class="d-flex justify-content-between">
                                                <span>{{ __('label.expense') }}</span>
                                                <strong>{{ number_format($section_financial_details['expense']) }}</strong>
                                            </div>

                                            <div class="d-flex justify-content-between">
                                                <span>{{ __('label.salary_advance') }}</span>
                                                <strong>{{ number_format($section_financial_details['advance']) }}</strong>
                                            </div>

                                            <div class="d-flex justify-content-between">
                                                <span>{{ __('label.temporary_payroll') }}</span>
                                                <strong>{{ number_format($section_financial_details['temporary_payroll']) }}</strong>
                                            </div>

                                            <div class="d-flex justify-content-between">
                                                <span>{{ __('label.permanent_payroll') }}</span>
                                                <strong>{{ number_format($section_financial_details['permanent_payroll']) }}</strong>
                                            </div>

                                        </div>

                                        <div class="card-footer d-flex justify-content-between align-items-center">

                                            <strong class="text-danger">
                                                {{ number_format($section_financial_details['total_expense']) }}
                                            </strong>

                                            <button
                                                class="btn btn-sm btn-outline-danger"
                                                wire:click="openFinancialCategoryDetails('expense')">

                                                <i class="bx bx-detail me-1"></i>
                                                {{ __('label.details') }}

                                            </button>

                                        </div>

                                    </div>

                                </div>

                                {{-- Asset --}}
                                <div class="col-md-6">

                                    <div class="card border-warning shadow-sm h-100">

                                        <div class="card-header bg-warning">
                                            <strong>{{ __('label.asset') }}</strong>
                                        </div>

                                        <div class="card-body">

                                            <div class="d-flex justify-content-between">
                                                <span>{{ __('label.asset_purchase') }}</span>
                                                <strong>
                                                    {{ number_format($section_financial_details['asset']) }}
                                                </strong>
                                            </div>

                                        </div>

                                        <div class="card-footer d-flex justify-content-between align-items-center">

                                            <strong class="text-warning">
                                                {{ number_format($section_financial_details['asset']) }}
                                            </strong>

                                            <button
                                                class="btn btn-sm btn-outline-warning"
                                                wire:click="openFinancialCategoryDetails('asset')">

                                                <i class="bx bx-detail me-1"></i>
                                                {{ __('label.details') }}

                                            </button>

                                        </div>

                                    </div>

                                </div>

    
                                {{-- Summary --}}
                                <div class="col-12">

                                    <div class="card border-primary shadow-sm">

                                        <div class="card-header bg-primary text-white">
                                            <strong>{{ __('label.summary') }}</strong>
                                        </div>

                                        <div class="card-body">

                                            <div class="row text-center">

                                                <div class="col-md-6">

                                                    <small class="text-muted">
                                                        {{ __('label.profit') }}
                                                    </small>

                                                    <h4 class="{{ $section_financial_details['profit'] >= 0 ? 'text-success' : 'text-danger' }}">

                                                        {{ number_format($section_financial_details['profit']) }}

                                                    </h4>

                                                </div>

                                                <div class="col-md-6">

                                                    <small class="text-muted">
                                                        {{ __('label.current_cash') }}
                                                    </small>

                                                    <h4 class="text-info">

                                                        {{ number_format($section_financial_details['current_cash']) }}

                                                    </h4>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                @endif

                    <!-- ==============نظر به کتگوی  ======================= -->
                @if($view_mode === 'financial_category_details')

                <div class="card">

                    <div class="card-header d-flex justify-content-between align-items-center">

                        <div>

                        <h5>
                            {{ __('label.financial_category_breakdown') }}
                        </h5>

                        <small class="text-muted">

                            {{ __('label.branch') }}:
                            {{ $selected_branch_name }}

                            |

                            {{ __('label.section') }}:
                            {{ $selected_section_name }}

                            |

                            {{ __('label.category') }}:
                            {{ ucfirst($financial_detail_type) }}

                        </small>

                    </div>

                        <button
                            class="btn btn-sm btn-outline-secondary"
                            wire:click="backToSectionFinancialDetails">

                            <i class="bx bx-arrow-back"></i>

                            {{ __('label.back') }}

                        </button>

                    </div>

                    <div class="card-body">

                        <table class="table table-bordered">

                            <thead>

                                <tr>

                                    <th>{{ __('label.category') }}</th>

                                    <th>{{ __('label.amount') }}</th>
                                    <th>{{ __('label.details') }}</th>

                                </tr>

                            </thead>

                            <tbody>

                                @forelse($financial_category_details as $item)

                                    <tr>

                                        <td>{{ $item['name'] }}</td>

                                        <td>{{ number_format($item['amount']) }}</td>

                                        <td class="text-end">

                                            <button
                                                class="btn btn-sm btn-outline-primary"
                                                wire:click="openFinancialRecordDetails('{{ $item['key'] }}')">

                                                <i class="bx bx-detail me-1"></i>

                                                {{ __('label.details') }}

                                            </button>

                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td colspan="2" class="text-center">

                                            {{ __('label.no_data_found') }}

                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

                @endif

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
                                wire:click="backToFinancialCategory">
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

