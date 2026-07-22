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
                                {{ __('label.student_results_overview') }}
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

                                <div>
                                    <label class="form-label small mb-1">
                                        {{ __('label.gender') }}
                                    </label>
                                    <select class="form-select form-select-sm"
                                            style="width:160px;"
                                            wire:model.lazy="gender">

                                        <option value="">
                                            {{ __('label.all') }}
                                        </option>

                                        @foreach($genders as $gender)
                                            <option value="{{ $gender->id }}">
                                                {{ $gender->name }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- BODY (main stats only) -->
                     @if(!auth()->user()->branch_id)
                    <div class="card-body mt-3">

                        @foreach($results_stats as $item)

                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-{{ $item['color'] }}">
                                    {{ $item['label'] }}
                                </small>

                                <small>
                                    {{ $item['count'] }} ({{ $item['percent'] }}%)
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
                            <small class="text-muted">
                                {{ __('label.overall_total') }}:
                                <strong>
                                    {{
                                        ($results_stats[0]['count'] ?? 0) +
                                        ($results_stats[1]['count'] ?? 0) +
                                        ($results_stats[2]['count'] ?? 0) +
                                        ($results_stats[3]['count'] ?? 0) +
                                        ($results_stats[4]['count'] ?? 0) +
                                        ($results_stats[5]['count'] ?? 0) 
                                    }}
                                </strong>
                            </small>

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

                            @foreach($branch_results_stats as $stat)

                                @php
                                    $total = $stat['total'] ?: 1;

                                    $placement_percent = round(($stat['placement'] / $total) * 100, 1);
                                    $passed_percent = round(($stat['passed'] / $total) * 100, 1);
                                    $failed_percent  = round(($stat['failed'] / $total) * 100, 1);
                                    $makeup_percent    = round(($stat['makeup'] / $total) * 100, 1);
                                    $dropped_percent    = round(($stat['dropped'] / $total) * 100, 1);
                                  
                                @endphp

                                <div class="col-12 col-md-6 col-lg-4 col-xl-3">

                                    <div class="card border shadow-sm h-100">

                                        <!-- HEADER (ONLY NAME) -->
                                        <div class="card-header bg-light">
                                            <strong>{{ $stat['branch_name'] }}</strong>
                                        </div>

                                        <!-- BODY (STATISTICS + PROGRESS) -->
                                        <div class="card-body">

                                            <small class="text-info">
                                                {{ __('label.placement') }} ({{ $stat['placement'] }})
                                            </small>
                                            <div class="progress mb-2" style="height:6px;">
                                                <div class="progress-bar bg-info"
                                                    style="width: {{ $placement_percent }}%"></div>
                                            </div>
                                            <small class="text-success">
                                                {{ __('label.passed') }} ({{ $stat['passed'] }})
                                            </small>
                                            <div class="progress mb-2" style="height:6px;">
                                                <div class="progress-bar bg-success"
                                                    style="width: {{ $passed_percent }}%"></div>
                                            </div>

                                            <small class="text-danger">
                                                {{ __('label.failed') }} ({{ $stat['failed'] }})
                                            </small>
                                            <div class="progress mb-2" style="height:6px;">
                                                <div class="progress-bar bg-danger"
                                                    style="width: {{ $failed_percent }}%"></div>
                                            </div>

                                            <small class="text-warning">
                                                {{ __('label.makeup') }} ({{ $stat['makeup'] }})
                                            </small>
                                            <div class="progress mb-2" style="height:6px;">
                                                <div class="progress-bar bg-warning"
                                                    style="width: {{ $makeup_percent }}%"></div>
                                            </div>

                                            <small class="text-secondary">
                                                {{ __('label.dropped') }} ({{ $stat['dropped'] }})
                                            </small>
                                            <div class="progress mb-2" style="height:6px;">
                                                <div class="progress-bar bg-secondary"
                                                    style="width: {{ $dropped_percent }}%"></div>
                                            </div>

                                        </div>

                                        <!-- FOOTER (TOTAL ONLY) -->
                                        <div class="card-footer bg-white d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mt-0">

                                            <!-- LEFT: TOTAL -->
                                            <div class="text-center text-sm-start">
                                                <small class="text-muted">{{ __('label.total') }}</small>
                                                <strong class="ms-1">{{ $stat['total'] }}</strong>
                                            </div>

                                            <!-- RIGHT: DETAILS BUTTON -->
                                            <div class="text-center text-sm-end">

                                                <button class="btn btn-sm btn-outline-primary"
                                                        wire:click="openBranchProgramDetails({{ $stat['branch_id'] }})">

                                                    <i class="bx bx-detail me-1"></i>
                                                    {{ __('label.details') }}

                                                </button>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    </div>
                    @endif

                    <!-- ----------branch shifts--------------------------- -->
    

                    <!-- ==============shift courses ----------------------- -->

                    @if($view_mode === 'shift_course')

                        <div class="card">

                            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">

                                <div>

                                    <h5>
                                        {{ __('label.program_breakdown') }}
                                    </h5>

                                    <small class="text-muted">
                                        {{ __('label.branch') }}: {{ $selected_branch_name }}
                                    </small>

                                </div>

                                <button
                                    class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1"
                                    wire:click="backToDashboard">

                                    <i class="bx bx-arrow-back"></i>

                                    {{ __('label.back') }}

                                </button>

                            </div>

                            <div class="card-body">

                                <div class="row g-3">

                                    @foreach($shift_course_stats as $stat)

                                        <div class="col-md-4">

                                            <div class="card border shadow-sm">

                                                <div class="card-header bg-light">
                                                    <strong>{{ $stat['program_name'] }}</strong>
                                                </div>

                                                <div class="card-body">

                                                    <small class="text-info">
                                                        {{ __('label.placement') }}: {{ $stat['placement'] }}
                                                    </small><br>

                                                    <small class="text-success">
                                                        {{ __('label.passed') }}: {{ $stat['passed'] }}
                                                    </small><br>

                                                    <small class="text-danger">
                                                        {{ __('label.failed') }}: {{ $stat['failed'] }}
                                                    </small><br>

                                                    <small class="text-warning">
                                                        {{ __('label.makeup') }}: {{ $stat['makeup'] }}
                                                    </small><br>

                                                    <small class="text-secondary">
                                                        {{ __('label.dropped') }}: {{ $stat['dropped'] }}
                                                    </small><br>

                                                </div>

                                                <div class="card-footer d-flex justify-content-between align-items-center">

                                                    <div>
                                                        <small>{{ __('label.total') }}</small>
                                                        <strong>{{ $stat['total'] }}</strong>
                                                    </div>

                                                    <button
                                                        class="btn btn-sm btn-outline-primary"
                                                        wire:click="openProgramStudents({{ $stat['program_id'] }})">

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

                    <!-- ==============نظر به کتاب  ======================= -->
                    @if($view_mode === 'program_book')

                    <div class="card">

                        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">

                            <div>

                                <h5>
                                    {{ __('label.book_breakdown') }}
                                </h5>

                                <small class="text-muted">
                                    {{ __('label.branch') }}: {{ $selected_branch_name }}
                                    |

                                    {{ __('label.program') }}: {{ $selected_program_name }}
                                </small>

                            </div>

                            <button
                                class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1"
                                wire:click="backToProgramResults">

                                <i class="bx bx-arrow-back"></i>

                                {{ __('label.back') }}

                            </button>

                        </div>

                        <div class="card-body">

                            <div class="row g-3">

                                @foreach($program_book_stats as $stat)

                                    <div class="col-md-4">

                                        <div class="card border shadow-sm">

                                            <div class="card-header bg-light">
                                                <strong>{{ $stat['book_name'] }}</strong>
                                            </div>

                                            <div class="card-body">

                                                <small class="text-info">
                                                    {{ __('label.placement') }}:
                                                    {{ $stat['placement'] }}
                                                </small>
                                                <br>

                                                <small class="text-success">
                                                    {{ __('label.passed') }}:
                                                    {{ $stat['passed'] }}
                                                </small>
                                                <br>

                                                <small class="text-danger">
                                                    {{ __('label.failed') }}:
                                                    {{ $stat['failed'] }}
                                                </small>
                                                <br>

                                                <small class="text-warning">
                                                    {{ __('label.makeup') }}:
                                                    {{ $stat['makeup'] }}
                                                </small>
                                                <br>

                                                <small class="text-secondary">
                                                    {{ __('label.dropped') }}:
                                                    {{ $stat['dropped'] }}
                                                </small>

                                            </div>

                                            <div class="card-footer d-flex justify-content-between align-items-center">

                                                <div>
                                                    <small>{{ __('label.total') }}</small>
                                                    <strong>{{ $stat['total'] }}</strong>
                                                </div>

                                                <button
                                                    class="btn btn-sm btn-outline-primary"
                                                    wire:click="openBookStudents({{ $stat['book_id'] }})">

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



                    <!-- ===============لیست کورس های دانشجویان ======================= -->

                      @if($view_mode === 'course_students')

                        <div class="card">

                            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">

                                <!-- LEFT -->
                                <div>

                                    <h5 class="mb-1">
                                        {{ __('label.student_results') }}
                                    </h5>

                                    <small class="text-muted">

                                        <span class="text-primary fw-semibold">
                                            {{ __('label.branch') }}:
                                        </span>
                                        {{ $selected_branch_name }}

                                        |
                                        <span class="text-info fw-semibold">
                                        {{ __('label.program') }}: 
                                        </span>
                                        
                                        {{ $selected_program_name }}
        
                                        |


                                        <span class="text-dark fw-semibold">
                                            {{ __('label.book') }}:
                                        </span>
                                        {{ $selected_book_name }}

                                        |

                                        <span class="text-success fw-semibold">
                                            {{ __('label.shift') }}:
                                        </span>
                                        @if(!empty($selected_shift_name))
                                            {{ $selected_shift_name }} 
                                        @else
                                            {{ __('label.all') }}
                                        @endif

                                    </small>

                                </div>

                                <!-- RIGHT -->
                                <div class="d-flex align-items-center gap-2">

                                    <div style="width:160px;">
                                        <select
                                            class="form-select form-select-sm"
                                            wire:model.lazy="selected_shift_id">
                                            <option value="">{{ __('label.all') }}</option>
                                            @foreach($shifts as $shift)
                                                <option value="{{ $shift->id }}">
                                                    {{ $shift->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <button
                                        class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1"
                                        wire:click="backToBook">

                                        <i class="bx bx-arrow-back"></i>
                                        {{ __('label.back') }}

                                    </button>

                                </div>

                            </div>

                            <div class="card-body">

                                <div class="table-responsive text-nowrap">

                                    <table class="table table-bordered">

                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('label.shift') }}</th>
                                                <th>{{ __('label.student_code') }}</th>
                                                <th>{{ __('label.name') }}</th>
                                                <th>{{ __('label.father_name') }}</th>
                                                <th>{{ __('label.phone_no') }}</th>
                                                <th>{{ __('label.father_no') }}</th>
                                                <th>{{ __('label.status') }}</th>
                                                <th>{{ __('label.comments') }}</th>
                                            </tr>
                                        </thead>

                                        <tbody>

                                            @foreach($course_students as $i => $cs)

                                                <tr>

                                                    <td>{{ $i + 1 }}</td>

                                                    <td>{{ $cs->shift?->name }}</td>
                                                    <td>{{ $cs->student?->student_code }}</td>

                                                    <td>{{ $cs->student?->name }} {{ $cs->student?->last_name }}</td>
                                                    <td>{{ $cs->student?->father_name }}</td>
                                                    <td>{{ $cs->student?->phone }}</td>
                                                    <td>{{ $cs->student?->father_whats_app }}</td>
                                                    

                                                    <td>

                                                        @if($cs->status === 'placement')
                                                            <span class="badge bg-info">{{ __('label.placement') }}</span>

                                                        @elseif($cs->status === 'passed')
                                                            <span class="badge bg-success">{{ __('label.passed') }}</span>

                                                        @elseif($cs->status === 'failed')
                                                            <span class="badge bg-danger">{{ __('label.failed') }}</span>

                                                        @elseif($cs->status === 'makeup')
                                                            <span class="badge bg-warning">{{ __('label.makeup') }}</span>

                                                        @elseif($cs->status === 'dropped')
                                                            <span class="badge bg-secondary">{{ __('label.dropped') }}</span>

                                                        @else
                                                            <span class="badge bg-dark">-</span>
                                                        @endif

                                                    </td>

                                                    <td>
                                                        @if(count($cs->comments) > 0)

                                                            <a class="btn btn-primary btn-icon rounded-pill"
                                                            href="javascript:void(0);"
                                                            wire:click="showComments({{ $cs->id }})">

                                                                <i class="bx bx-message-detail text-white"></i>

                                                            </a>

                                                        @else

                                                            <span class="badge bg-light text-secondary border">
                                                                <i class="bx bx-phone-off me-1"></i>
                                                               No Contact Yet
                                                            </span>

                                                        @endif
                                                    </td>

                                                </tr>

                                            @endforeach

                                        </tbody>

                                    </table>

                                </div>

                            </div>

                        </div>
                        
                        <div class="modal fade" id="show_comments_modal" tabindex="-1" aria-hidden="true" wire:ignore.self> 
                            <div class="modal-dialog modal-lg" branch="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        
                                        <h5 class="modal-title">{{__('label.comments')}}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                                        
                                    </div>
                                    
                                    <div class="modal-body">
                                        
                                        <div class=" table-responsive text-nowrap">
                                            <table class="table">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th>
                                                            {{ __('label.NO') }}
                                                        </th>

                                                        <th>
                                                            {{ __('label.comment') }}
                                                        </th>
                                                        <th>
                                                            {{ __('label.date') }}
                                                        </th>
                                                    </tr>

                                                </thead>
                                                <tbody class="table-border-bottom-0">
                                                    @if(count($show_comments) > 0)
                                                    @foreach($show_comments as $i => $comm)
                                                    <tr>
                                                        <td>{{ $i + 1 }}</td>

                                                        <td>{{ $comm->comment }}</td>
                                                        <td>{{ $comm->created_at->format('Y/m/d') }}</td>
                                        
                                                    </tr>
                                                    @endforeach
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('label.close') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>

            </div>

        </div>

    </div>

</div>
