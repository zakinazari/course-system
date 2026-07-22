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
                                {{ __('label.student_attendance_overview') }}
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

                        @foreach($attendance_stats as $item)

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
                                Refresh
                            </button>

                            <!-- RIGHT: TOTAL -->
                            <small class="text-muted">
                                {{ __('label.overall_total') }}:
                                <strong>
                                    {{
                                        ($attendance_stats[0]['count'] ?? 0) +
                                        ($attendance_stats[1]['count'] ?? 0) +
                                        ($attendance_stats[2]['count'] ?? 0) +
                                        ($attendance_stats[3]['count'] ?? 0)
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
                            <h6 class="text-muted mb-0">{{ __('label.branch_attendance') }}</h6>
                        </div>

                        <div class="row g-3">

                            @foreach($branch_attendance_stats as $stat)

                                @php
                                    $total = $stat['total'] ?: 1;

                                    $present_percent = round(($stat['present'] / $total) * 100, 1);
                                    $absent_percent  = round(($stat['absent'] / $total) * 100, 1);
                                    $late_percent    = round(($stat['late'] / $total) * 100, 1);
                                    $excused_percent = round(($stat['excused'] / $total) * 100, 1);
                                @endphp

                                <div class="col-12 col-md-6 col-lg-4 col-xl-3">

                                    <div class="card border shadow-sm h-100">

                                        <!-- HEADER (ONLY NAME) -->
                                        <div class="card-header bg-light">
                                            <strong>{{ $stat['branch_name'] }}</strong>
                                        </div>

                                        <!-- BODY (STATISTICS + PROGRESS) -->
                                        <div class="card-body">

                                            <small class="text-success">
                                                {{ __('label.present') }} ({{ $stat['present'] }})
                                            </small>
                                            <div class="progress mb-2" style="height:6px;">
                                                <div class="progress-bar bg-success"
                                                    style="width: {{ $present_percent }}%"></div>
                                            </div>

                                            <small class="text-danger">
                                                {{ __('label.absent') }} ({{ $stat['absent'] }})
                                            </small>
                                            <div class="progress mb-2" style="height:6px;">
                                                <div class="progress-bar bg-danger"
                                                    style="width: {{ $absent_percent }}%"></div>
                                            </div>

                                            <small class="text-warning">
                                                {{ __('label.late') }} ({{ $stat['late'] }})
                                            </small>
                                            <div class="progress mb-2" style="height:6px;">
                                                <div class="progress-bar bg-warning"
                                                    style="width: {{ $late_percent }}%"></div>
                                            </div>

                                            <small class="text-secondary">
                                                {{ __('label.excused') }} ({{ $stat['excused'] }})
                                            </small>
                                            <div class="progress mb-2" style="height:6px;">
                                                <div class="progress-bar bg-info"
                                                    style="width: {{ $excused_percent }}%"></div>
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
                                                        wire:click="openBranchShiftDetails({{ $stat['branch_id'] }})">

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
                    @if($view_mode === 'branch_shift')

                    <div class="card">

                        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">

                            <!-- LEFT: TITLE + CONTEXT -->
                            <div>

                                <h5 class="mb-1">
                                    {{ __('label.shift_breakdown') }}
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

                                @foreach($branch_attendance_stats as $stat)

                                    <div class="col-md-4">

                                        <div class="card border shadow-sm">

                                            <div class="card-header bg-light">
                                                <strong>{{ $stat['shift_name'] }}</strong>
                                            </div>

                                            <div class="card-body">

                                                <small class="text-success">{{ __('label.present') }}: {{ $stat['present'] }}</small><br>
                                                <small class="text-danger">{{ __('label.absent') }}: {{ $stat['absent'] }}</small><br>
                                                <small class="text-warning">{{ __('label.late') }}: {{ $stat['late'] }}</small><br>
                                                <small class="text-secondary">{{ __('label.excused') }}: {{ $stat['excused'] }}</small>

                                            </div>

                                            <div class="card-footer d-flex justify-content-between align-items-center">

                                                <small>{{ __('label.total') }}: {{ $stat['total'] }}</small>

                                                <button class="btn btn-sm btn-outline-primary"
                                                        wire:click="openShiftCourseDetails({{ $stat['shift_id'] }})">
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

                    <!-- ==============shift courses ----------------------- -->

                    @if($view_mode === 'shift_course')

                        <div class="card">

                            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">

                                <div>

                                    <h5>
                                        {{ __('label.course_breakdown') }}
                                    </h5>

                                    <small class="text-muted">
                                        {{ __('label.branch') }}: {{ $selected_branch_name }}
                                        |
                                        {{ __('label.shift') }}: {{ $selected_shift_name }}
                                    </small>

                                </div>

                                <button
                                    class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1"
                                    wire:click="backToBranchShift">

                                    <i class="bx bx-arrow-back"></i>

                                    {{ __('label.back') }}

                                </button>

                            </div>

                            <div class="card-body">

                                <div class="row g-3">

                                    @foreach($shift_course_stats as $stat)

                                        @php
                                            $midtermDays = !empty($stat['mid_exam_date'])
                                                ? now()->startOfDay()->diffInDays(
                                                    \Carbon\Carbon::parse($stat['mid_exam_date'])->startOfDay(),
                                                    false
                                                )
                                                : null;

                                            $finalDays = !empty($stat['final_exam_date'])
                                                ? now()->startOfDay()->diffInDays(
                                                    \Carbon\Carbon::parse($stat['final_exam_date'])->startOfDay(),
                                                    false
                                                )
                                                : null;
                                        @endphp

                                        <div class="col-md-4">

                                            <div class="card border shadow-sm h-100">

                                                <div class="card-header bg-light">
                                                    <strong>{{ $stat['course_name'] }}</strong>
                                                </div>

                                                <div class="card-body">

                                                    <small class="text-success">
                                                        {{ __('label.present') }}:
                                                        {{ $stat['present'] }}
                                                    </small><br>

                                                    <small class="text-danger">
                                                        {{ __('label.absent') }}:
                                                        {{ $stat['absent'] }}
                                                    </small><br>

                                                    <small class="text-warning">
                                                        {{ __('label.late') }}:
                                                        {{ $stat['late'] }}
                                                    </small><br>

                                                    <small class="text-secondary">
                                                        {{ __('label.excused') }}:
                                                        {{ $stat['excused'] }}
                                                    </small>

                                                    {{-- ATTENDANCE --}}
                                                    <div class="mt-1">

                                                        @if($stat['attendance_taken_today'])

                                                            <span class="badge bg-success-subtle text-success border"
                                                                style="font-size:9px;">
                                                                ✓ {{ __('label.attendance_taken') }}
                                                            </span>

                                                        @else

                                                            <span class="badge bg-danger-subtle text-danger border"
                                                                style="font-size:9px;">
                                                                ✗ {{ __('label.attendance_not_taken') }}
                                                            </span>

                                                        @endif
                                                        </div>
                                                        

                                                    <hr class="my-2">
                                                    

                                                        @if(!is_null($midtermDays))

                                                            <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">

                                                                <div>
                                                                    <small class="fw-semibold text-primary">
                                                                        <i class="bx bx-book-open"></i>
                                                                        {{ __('label.midterm_exam') }}
                                                                    </small>
                                                                    <br>

                                                                    <small class="text-muted" style="font-size:11px;">
                                                                        {{ \Carbon\Carbon::parse($stat['mid_exam_date'])->format('Y-m-d') }}
                                                                    </small>
                                                                </div>

                                                                <span class="badge rounded-pill px-2 py-1
                                                                    @if($midtermDays < 0)
                                                                        bg-secondary
                                                                    @elseif($midtermDays <= 3)
                                                                        bg-danger
                                                                    @elseif($midtermDays <= 7)
                                                                        bg-warning text-dark
                                                                    @else
                                                                        bg-info
                                                                    @endif"
                                                                    style="font-size:10px;font-weight:500;">

                                                                    @if($midtermDays > 0)
                                                                        {{ $midtermDays }} {{ __('label.days_left') }}
                                                                    @elseif($midtermDays == 0)
                                                                        {{ __('label.today') }}
                                                                    @else
                                                                        {{ __('label.finished') }}
                                                                    @endif

                                                                </span>

                                                            </div>

                                                        @endif

                                                        @if(!is_null($finalDays))

                                                            <div class="d-flex justify-content-between align-items-center">

                                                                <div>
                                                                    <small class="fw-semibold text-success">
                                                                        <i class="bx bx-award"></i>
                                                                        {{ __('label.final_exam') }}
                                                                    </small>
                                                                    <br>

                                                                    <small class="text-muted" style="font-size:11px;">
                                                                        {{ \Carbon\Carbon::parse($stat['final_exam_date'])->format('Y-m-d') }}
                                                                    </small>
                                                                </div>

                                                                <span class="badge rounded-pill px-2 py-1
                                                                    @if($finalDays < 0)
                                                                        bg-secondary
                                                                    @elseif($finalDays <= 3)
                                                                        bg-danger
                                                                    @elseif($finalDays <= 7)
                                                                        bg-warning text-dark
                                                                    @else
                                                                        bg-success
                                                                    @endif"
                                                                    style="font-size:10px;font-weight:500;">

                                                                    @if($finalDays > 0)
                                                                        {{ $finalDays }} {{ __('label.days_left') }}
                                                                    @elseif($finalDays == 0)
                                                                        {{ __('label.today') }}
                                                                    @else
                                                                        {{ __('label.finished') }}
                                                                    @endif

                                                                </span>

                                                            </div>

                                                        @endif

                                                </div>

                                                <div class="card-footer d-flex justify-content-between align-items-center">

                                                    <div>
                                                        <small>{{ __('label.total') }}</small>
                                                        <strong>{{ $stat['total'] }}</strong>
                                                    </div>

                                                    <button
                                                        class="btn btn-sm btn-outline-primary"
                                                        wire:click="openCourseStudents({{ $stat['course_id'] }})">

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
                                        {{ __('label.student_attendance_details') }}
                                    </h5>

                                    <small class="text-muted">

                                        <span class="text-primary fw-semibold">
                                            {{ __('label.branch') }}:
                                        </span>
                                        {{ $selected_branch_name }}

                                        |

                                        <span class="text-success fw-semibold">
                                            {{ __('label.shift') }}:
                                        </span>
                                        {{ $selected_shift_name }}

                                        |

                                        <span class="text-dark fw-semibold">
                                            {{ __('label.course') }}:
                                        </span>
                                        {{ $selected_course_name }}

                                    </small>

                                </div>

                                <!-- RIGHT -->
                                <div>

                                    <button
                                        class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1"
                                        wire:click="backToShiftCourse">

                                        <i class="bx bx-arrow-back"></i>

                                        {{ __('label.back') }}

                                    </button>

                                </div>

                            </div>

                            <div class="card-body">

                            <div class="table-responsive text-nowrap">
                                <table class="table table-bordered ">

                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('label.student_code') }}</th>
                                                <th>{{ __('label.name') }}</th>
                                                <th class="text-success">{{ __('label.present') }}</th>
                                                <th class="text-danger">{{ __('label.absent') }}</th>
                                                <th class="text-warning">{{ __('label.late') }}</th>
                                                <th class="text-info">{{ __('label.excused') }}</th>
                                                <th class="text-info">{{ __('label.details') }}</th>
                                            </tr>
                                        </thead>

                                        <tbody>

                                            @foreach($course_students as $i => $student)

                                                <tr>

                                                    <td>{{ $i + 1 }}</td>

                                                    <td>
                                                        {{ $student->student?->student_code }}
                                                    </td>

                                                    <td>
                                                        {{ $student->student?->name }}
                                                    </td>

                                                    <td class="text-success fw-bold">
                                                        {{ $student->present }}
                                                    </td>

                                                    <td class="text-danger fw-bold">
                                                        {{ $student->absent }}
                                                    </td>

                                                    <td class="text-warning fw-bold">
                                                        {{ $student->late }}
                                                    </td>

                                                    <td class="text-info fw-bold">
                                                        {{ $student->excused }}
                                                    </td>

                                                    <td>

                                                        <button
                                                            class="btn btn-sm btn-outline-primary"
                                                            wire:click="openStudentAttendanceDetails({{ $student->student_id }})">

                                                            <i class="bx bx-detail"></i>
                                                            {{ __('label.details') }}

                                                        </button>

                                                    </td>

                                                </tr>

                                            @endforeach

                                        </tbody>

                                    </table>
                                    
                                </div>
                            </div>

                        </div>
                        @endif

                        <!-- ===================ریکاردهای حاضری دانشجو ================================= -->

                        @if($view_mode === 'student_attendance')

                            <div class="card">

                                <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">

                                    <div>

                                        <h5 class="mb-1">
                                            {{ __('label.student_attendance_history') }}
                                        </h5>

                                        <small class="text-muted">

                                            {{ $selected_branch_name }}

                                            |

                                            {{ $selected_shift_name }}

                                            |

                                            {{ $selected_course_name }}

                                            |

                                             <span class="text-info fw-semibold">
                                                {{ __('label.student_code') }}:
                                                </span>
                                                {{ $selected_student_code }}

                                                |

                                                <span class="fw-semibold">
                                                    {{ __('label.name') }}:
                                                </span>
                                                {{ $selected_student_name }}

                                        </small>

                                    </div>

                                    <button
                                        class="btn btn-sm btn-outline-secondary"
                                        wire:click="backToCourseStudents">

                                        <i class="bx bx-arrow-back"></i>

                                        {{ __('label.back') }}

                                    </button>

                                </div>

                                <div class="card-body">

                                    <div class="table-responsive">

                                        <table class="table table-bordered">

                                            <thead>

                                                <tr>
                                                    <th>#</th>
                                                    <th>{{ __('label.date') }}</th>
                                                    <th>{{ __('label.status') }}</th>
                                                    <th>{{ __('label.comment') }}</th>
                                                </tr>

                                            </thead>

                                            

                                              @foreach($student_attendance_records as $i => $record)

                                                <tr
                                                    @if(
                                                        $record->status !== 'present'
                                                        && empty(trim($record->note ?? ''))
                                                    )
                                                        class="table-danger"
                                                    @endif
                                                >

                                                    <td>{{ $i + 1 }}</td>

                                                    <td>
                                                        {{ $record->attendance_date?->format('Y-m-d') }}
                                                    </td>

                                                    <td>

                                                        @if($record->status === 'present')
                                                            <span class="badge bg-success">{{ __('label.present') }}</span>

                                                        @elseif($record->status === 'absent')
                                                            <span class="badge bg-danger">{{ __('label.absent') }}</span>

                                                        @elseif($record->status === 'late')
                                                            <span class="badge bg-warning">{{ __('label.late') }}</span>

                                                        @else
                                                            <span class="badge bg-info">{{ __('label.excused') }}</span>
                                                        @endif

                                                    </td>

                                                    <td>
                                                        {{ $record->note }}
                                                    </td>

                                                </tr>

                                                @endforeach

                                            </tbody>

                                        </table>

                                    </div>

                                </div>

                            </div>

                            @endif
                </div>

            </div>

        </div>

    </div>

</div>