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
                                {{ __('label.active_students_by_branch') }}
                            </h5>

              
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

                    <!-- BODY (main stats only) -->
                    @if(!auth()->user()->branch_id)

                    <div class="card-body mt-3">

                    @foreach($branch_student_stats as $item)

                        <div class="d-flex justify-content-between align-items-center mb-1">

                            <small class="text-{{ $item['color'] }}">
                                {{ $item['label'] }}
                            </small>

                            <small>
                                {{ number_format($item['count']) }} ({{ $item['percent'] }}%)
                            </small>

                        </div>

                        <div class="progress mb-3" style="height:10px;">
                            <div class="progress-bar bg-{{ $item['color'] }}"
                                style="width: {{ $item['percent'] }}%">
                            </div>
                        </div>

                    @endforeach

                    <hr>

                    <div class="d-flex justify-content-between align-items-center">

                        <strong>{{ __('label.total') }}</strong>

                        <strong class="text-primary">
                            {{ number_format(collect($branch_student_stats)->sum('count')) }}
                        </strong>

                    </div>

                </div>

                <hr>
                    <!-- ========================= -->  
                    <!-- Central Warehouses  -->
                    <!-- ========================= -->
                    <div class="card-body pt-4">

                        <div class="mb-4">
                            <h5 class="fw-bold mb-0">
                                <i class="bx bx-layer me-2"></i>
                                {{ __('label.active_students_by_section') }}
                            </h5>
                        </div>

                        <div class="row g-3">

                        @foreach($section_student_stats as $item)

                            <div class="col-md-4">

                                <div class="card border-0 shadow-sm h-100">

                                    {{-- HEADER --}}
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">

                                        <div class="d-flex align-items-center">

                                            <i class="bx bx-layer fs-5 text-primary me-2"></i>

                                            <strong class="mb-0">
                                                {{ $item['label'] }}
                                            </strong>

                                        </div>

                                        <span class="badge bg-primary">
                                            {{ __('label.section') }}
                                        </span>

                                    </div>

                                    {{-- BODY --}}
                                    <div class="card-body text-center py-4">

                                        <i class="bx bx-group fs-1 text-primary mb-2"></i>

                                        <h1 class="fw-bold mb-1">
                                            {{ number_format($item['count']) }}
                                        </h1>

                                        <small class="text-muted">
                                            {{ __('label.active_students') }}
                                        </small>

                                    </div>

                                </div>

                            </div>

                        @endforeach

                        </div>

                    </div>

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
                                {{ __('label.active_students_by_branch') }}
                            </h5>
                        </div>
                        <div class="row g-3">

                            @foreach($branch_student_stats as $item)

                                <div class="col-md-4">

                                    <div class="card border-0 shadow-sm h-100">

                                        {{-- HEADER --}}
                                        <div class="card-header d-flex align-items-center"
                                            style="background-color:#e7f1ff;">

                                            <i class="bx bx-buildings me-2 text-primary"></i>

                                            <strong class="mb-0">
                                                {{ $item['label'] }}
                                            </strong>

                                        </div>

                                        {{-- BODY --}}
                                        <div class="card-body text-center py-4">

                                            <i class="bx bx-group fs-1 text-primary mb-2"></i>

                                            <h1 class="fw-bold mb-1">
                                                {{ number_format($item['count']) }}
                                            </h1>

                                            <small class="text-muted">
                                                {{ __('label.active_students') }}
                                            </small>

                                        </div>
                                        <div class="card-footer d-flex justify-content-end align-items-center">

                                            <button class="btn btn-sm btn-outline-primary"
                                                    wire:click="openBranchSectionStudent( {{ $item['branch_id'] }} )">

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
                    <!-- ========================= -->  
                    <!-- Branch Section Student   -->
                    <!-- ========================= -->
                    @if($view_mode === 'branch_section_student')
                    <div class="card-body pt-4">

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

                        <div class="row g-3">

                        @foreach($branch_section_student_stats as $section)

                            <div class="col-md-4">

                                <div class="card border-0 shadow-sm h-100">

                                    {{-- HEADER --}}
                                    <div class="card-header d-flex justify-content-between align-items-center"
                                        style="background-color: #eaf4ff;">

                                        <div class="d-flex align-items-center">

                                            <i class="bx bx-layer fs-5 text-primary me-2"></i>

                                            <strong class="mb-0 text-primary">
                                                {{ $section['label'] }}
                                            </strong>

                                        </div>

                                        <span class="badge bg-primary">
                                            {{ __('label.section') }}
                                        </span>

                                    </div>

                                    {{-- BODY --}}
                                    <div class="card-body text-center py-4">

                                        <i class="bx bx-group fs-1 text-primary mb-2"></i>

                                        <h1 class="fw-bold mb-1">
                                            {{ number_format($section['count']) }}
                                        </h1>

                                        <small class="text-muted">
                                            {{ __('label.active_students') }}
                                        </small>

                                    </div>

                                    <div class="card-footer d-flex justify-content-end align-items-center">

                                        <button class="btn btn-sm btn-outline-primary"
                                                wire:click="openBranchSectionProgaramStudent( {{ $section['section_id'] }} )">

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

                      <!-- ========================= -->  
                    <!-- Branch Section Program Student   -->
                    <!-- ========================= -->
                    @if($view_mode === 'branch_section_program_student')
                    <div class="card-body pt-4">

                         <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">

                            <!-- LEFT: TITLE + CONTEXT -->
                          <div>

                            <h5 class="mb-1">
                                {{ __('label.program_breakdown') }}
                            </h5>

                            <small class="text-muted">

                                <span class="text-primary fw-semibold">
                                    {{ __('label.branch') }}:
                                </span>

                                {{ $selected_branch_name }}

                                <span class="mx-2">|</span>

                                <span class="text-primary fw-semibold">
                                    {{ __('label.section') }}:
                                </span>

                                {{ $selected_section_name }}

                            </small>

                        </div>

                            <!-- RIGHT: BACK BUTTON -->
                            <div>

                                <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1"
                                        wire:click="backToBranchSectionStudent">

                                    <i class="bx bx-arrow-back"></i>
                                    {{ __('label.back') }}

                                </button>

                            </div>

                        </div>

                        <div class="row g-3">

                        @foreach($branch_section_program_student_stats as $program)

                            <div class="col-md-4">

                                <div class="card border-0 shadow-sm h-100">

                                    {{-- HEADER --}}
                                    <div class="card-header d-flex justify-content-between align-items-center"
                                        style="background-color: #eaf4ff;">

                                        <div class="d-flex align-items-center">

                                            <i class="bx bx-book-open fs-5 text-primary me-2"></i>

                                            <strong class="mb-0 text-primary">
                                                {{ $program['label'] }}
                                            </strong>

                                        </div>

                                        <span class="badge bg-primary">
                                            {{ __('label.program') }}
                                        </span>

                                    </div>

                                    {{-- BODY --}}
                                    <div class="card-body text-center py-4">

                                        <i class="bx bx-group fs-1 text-primary mb-2"></i>

                                        <h1 class="fw-bold mb-1">
                                            {{ number_format($program['count']) }}
                                        </h1>

                                        <small class="text-muted">
                                            {{ __('label.active_students') }}
                                        </small>

                                    </div>

                                    <div class="card-footer d-flex justify-content-end align-items-center">

                                        <button class="btn btn-sm btn-outline-primary"
                                                wire:click="openBranchSectionProgaramBookStudent( {{ $program['program_id'] }} )">

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

                     <!-- ========================= -->  
                    <!-- Branch Section Program Student   -->
                    <!-- ========================= -->
                    @if($view_mode === 'branch_section_program_book_student')
                    <div class="card-body pt-4">

                         <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">

                            <!-- LEFT: TITLE + CONTEXT -->
                          <div>

                            <h5 class="mb-1">
                                {{ __('label.book_breakdown') }}
                            </h5>

                            <small class="text-muted">

                                <span class="text-primary fw-semibold">
                                    {{ __('label.branch') }}:
                                </span>

                                {{ $selected_branch_name }}

                                <span class="mx-2">|</span>

                                <span class="text-secondary fw-semibold">
                                    {{ __('label.section') }}:
                                </span>

                                {{ $selected_section_name }}

                                <span class="mx-2">|</span>

                                <span class="text-success fw-semibold">
                                    {{ __('label.program') }}:
                                </span>

                                {{ $selected_program_name }}

                            </small>

                        </div>

                            <!-- RIGHT: BACK BUTTON -->
                            <div>

                                <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1"
                                        wire:click="backToBranchSectionProgramStudent">

                                    <i class="bx bx-arrow-back"></i>
                                    {{ __('label.back') }}

                                </button>

                            </div>

                        </div>

                        <div class="row g-3">

                        @foreach($branch_section_program_book_student_stats as $book)

                            <div class="col-md-4">

                                <div class="card border-0 shadow-sm h-100">

                                    {{-- HEADER --}}
                                    <div class="card-header d-flex justify-content-between align-items-center"
                                        style="background-color: #eaf4ff;">

                                        <div class="d-flex align-items-center">

                                            <i class="bx bx-book fs-5 text-primary me-2"></i>

                                            <strong class="mb-0 text-primary">
                                                {{ $book['label'] }}
                                            </strong>

                                        </div>

                                        <span class="badge bg-primary">
                                            {{ __('label.book') }}
                                        </span>

                                    </div>

                                    {{-- BODY --}}
                                    <div class="card-body text-center py-4">

                                        <i class="bx bx-group fs-1 text-primary mb-2"></i>

                                        <h1 class="fw-bold mb-1">
                                            {{ number_format($book['count']) }}
                                        </h1>

                                        <small class="text-muted">
                                            {{ __('label.active_students') }}
                                        </small>

                                    </div>

                                    <div class="card-footer d-flex justify-content-end align-items-center">

                                        <button class="btn btn-sm btn-outline-primary"
                                                wire:click="openBookStudents( {{ $book['book_id'] }} )">

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

                    <!-- ===============لیست کورس های دانشجویان ======================= -->

                      @if($view_mode === 'course_students')

                        <div class="card">

                            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">

                                <!-- LEFT -->
                                <div>

                                    <small class="text-muted">

                                        <span class="text-primary fw-semibold">
                                            {{ __('label.branch') }}:
                                        </span>

                                        {{ $selected_branch_name }}

                                        <span class="mx-2">|</span>

                                        <span class="text-secondary fw-semibold">
                                            {{ __('label.section') }}:
                                        </span>

                                        {{ $selected_section_name }}

                                        <span class="mx-2">|</span>

                                        <span class="text-info fw-semibold">
                                            {{ __('label.program') }}:
                                        </span>

                                        {{ $selected_program_name }}

                                        <span class="mx-2">|</span>

                                        <span class="text-success fw-semibold">
                                            {{ __('label.book') }}:
                                        </span>

                                        {{ $selected_book_name }}

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
                                        wire:click="backToBranchSectionProgramBookStudent">

                                        <i class="bx bx-arrow-back"></i>
                                        {{ __('label.back') }}

                                    </button>

                                </div>

                            </div>

                            <div class="card-body">

                                <div class="table-responsive text-nowrap">

                                    <div class="table-responsive">

                                        <table class="table table-hover mb-0 table-bordered">

                                            <thead>

                                                <tr>
                                                    <th>#</th>
                                                    <th>{{ __('label.shift') }}</th>
                                                    <th>{{ __('label.course') }}</th>
                                                    <th class="text-center">{{ __('label.active_students') }}</th>
                                                </tr>

                                            </thead>

                                            <tbody>

                                                @foreach($branch_section_program_book_course_students as $index => $course)

                                                    <tr>

                                                        <td>{{ $index + 1 }}</td>

                                                        <td>{{ $course['shift_name'] }}</td>
                                                        <td>{{ $course['course_name'] }}</td>

                                                        <td class="text-center">
                                                            <span class="badge bg-primary">
                                                                {{ number_format($course['student_count']) }}
                                                            </span>
                                                        </td>

                                                    </tr>

                                                @endforeach

                                            </tbody>

                                        </table>

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


