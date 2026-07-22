
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

    <div class="card">
       
        <div class="card-header d-flex justify-content-between align-items-center">

            <h5 class="card-title mb-0">@if(App::getLocale()=='en') {{ $active_menu?->name_en }} @else {{ $active_menu?->name }}  @endif</h5>

            <div class="d-flex align-items-center gap-2">
                <div class="btn-group">

                    <button type="button" class="btn btn-secondary">
                        <i class="fa fa-file-export"></i> {{ __('label.export') }}
                    </button>

                    <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="visually-hidden">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                        <li class="px-3 py-2">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="portraitRadio" wire:model="pdfOrientation" value="portrait">
                                    <label class="form-check-label" for="portraitRadio">{{ __('label.portrait') }}</label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="landscapeRadio" wire:model="pdfOrientation" value="landscape">
                                    <label class="form-check-label" for="landscapeRadio">{{ __('label.landscape') }}</label>
                                </div>
                            </div>

                            <a class="dropdown-item d-flex align-items-center gap-2" href="#" wire:click.prevent="exportPdf">
                                <i class="fa fa-file-pdf text-danger"></i> {{ __('label.export_to_pdf') }}
                            </a>

                            <a class="dropdown-item d-flex align-items-center gap-2" href="#" wire:click.prevent="exportExcel">
                                <i class="fa fa-file-excel text-success"></i> {{ __('label.export_to_excel') }}
                            </a>

                        </li>
                    </ul>
                </div>

            </div>

        </div>
        <hr>
        <div class="table-responsive text-nowrap">
 
            <div class="mb-3 px-3">
                <form wire:submit.prevent="applySearch" class="row g-3 align-items-end">
                    @if(!auth()->user()->branch_id)
                    <div class="col-md-3">
                        <label class="form-label">{{ __('label.branch') }}</label>
                        <select class="form-select" wire:model.lazy="search.branch_id" 
                        wire:change="loadClassroomAndTeacher($event.target.value)">
                           <option value="">{{ __('label.all') }}</option>
                           @foreach($branches as $branch)
                                 <option value="{{ $branch->id }}"  wire:key="branch-search-{{ $branch->id }}">
                                    {{ $branch->name }}
                                 </option>
                           @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-md-3" wire:ignore>
                        <label class="form-label">{{ __('label.program') }}</label>
                        <select class="form-select select2" wire:model.lazy="search.program_id" id ="search_program_id">
                           <option value="">{{ __('label.all') }}</option>
                           @foreach($programs as $program)
                                 <option value="{{ $program->id }}"  wire:key="program-search-{{ $program->id }}">
                                    {{ $program->name }}
                                 </option>
                           @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 d-flex flex-column">
                        <label class="form-label">{{ __('label.book') }}</label>
                        <select wire:ignore.self class="form-select select2" wire:model.lazy="search.book_id" id ="search_book_id">
                           <option value="">{{ __('label.all') }}</option>
                           @foreach($books as $book)
                                 <option value="{{ $book->id }}"  wire:key="book-search-{{ $book->id }}">
                                    {{ $book->name }}
                                 </option>
                           @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-md-2 d-flex flex-column" wire:ignore>
                        <label class="form-label">{{ __('label.teacher') }}</label>
                        <select class="form-select select2" wire:model.lazy="search.teacher_id" id ="search_teacher_id">
                           <option value="">{{ __('label.all') }}</option>
                           @foreach($teachers as $teacher)
                                 <option value="{{ $teacher->id }}"  wire:key="teacher-search-{{ $teacher->id }}">
                                    {{ $teacher->name }} {{ $teacher->last_name }}
                                 </option>
                           @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('label.course_type') }}</label>
                        <select class="form-select" wire:model.lazy="search.course_type_id">
                           <option value="">{{ __('label.all') }}</option>
                           @foreach($course_types as $type)
                                 <option value="{{ $type->id }}"  wire:key="type-search-{{ $type->id }}">
                                    {{ $type->name }}
                                 </option>
                           @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('label.shift') }}</label>
                        <select class="form-select" wire:model.lazy="search.shift_id">
                           <option value="">{{ __('label.all') }}</option>
                           @foreach($shifts as $shift)
                                 <option value="{{ $shift->id }}"  wire:key="shift-search-{{ $shift->id }}">
                                    {{ $shift->name }}
                                 </option>
                           @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label">{{ __('label.course') }}</label>
                        <select class="form-select select2" id="search_course_id">
                           <option value="">{{ __('label.all') }}</option>
                           @foreach($courses as $course)
                                 <option value="{{ $course->id }}"  wire:key="course-search-{{ $course->id }}">
                                    {{ $course->name }}
                                 </option>
                           @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">{{ __('label.date') }}</label>
                        <input type="date" class="form-control @error('attendance_date') is-invalid @enderror" placeholder="" wire:model.lazy="attendance_date">
                    </div>
           
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary">
                            {{ __('label.search') }}
                        </button>
                    </div>
                    @error('attendance_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </form>
            </div>
            <br>
            @if($course_id && count($students) > 0)
            
              
                <!-- ==============start teacher attendance------------------------------- -->
                 @if(Auth::user()->isDeveloper() || Auth::user()->isAdmin() || ($attendance_date === now()->format('Y-m-d')))
                 <div class="card mb-3 shadow-md">

                    <div class="card-body">

                        <div class="row">

                            {{-- ================= LEFT SIDE (UNIT) ================= --}}
                            <div class="col-md-6 border-end">

                                <div class="d-flex justify-content-between align-items-center mb-3">

                                    <div>
                                        <h6 class="mb-0">
                                            Day: <strong>{{ $total_days ?? 0 }}</strong>
                                        </h6>
                                        <small class="text-muted">
                                            Unit: {{ $current_unit_number ?? 1 }}
                                        </small>
                                    </div>
                                     <div class="d-flex gap-2 flex-wrap mt-3">

                                    <div class="form-check form-check-success">
                                        <label class="d-flex align-items-center gap-1">
                                            <input class="form-check-input"
                                                type="radio"
                                                wire:model="unit_status"
                                                value="ongoing">
                                            <span class="badge bg-success">Ongoing</span>
                                        </label>
                                    </div>

                                    <div class="form-check form-check-secondary">
                                        <label class="d-flex align-items-center gap-1">
                                            <input class="form-check-input"
                                                type="radio"
                                                wire:model="unit_status"
                                                value="finished">
                                            <span class="badge bg-secondary">Finished</span>
                                        </label>
                                    </div>

                                </div>
                                </div>

                                {{-- UNIT STATUS (NEW) --}}
                               
                                 <textarea class="form-control"
                                        rows="2"
                                        wire:model="unit_note"
                                        placeholder="Unit note (optional)">
                                </textarea>
                            </div>

                            {{-- ================= RIGHT SIDE (TEACHER) ================= --}}
                            <div class="col-md-6">
                                @php
                                    $leave = $teacher_leave;
                                @endphp
                                <div class="fw-bold mb-2">
                                    Teacher:
                                    {{ $this->course?->teacher?->name }}
                                    {{ $this->course?->teacher?->last_name }}
                                </div>

                                {{-- Teacher Status --}}
                                <div class="d-flex gap-2 flex-wrap mb-2">

                                    <div class="form-check form-check-success">
                                        <label class="d-flex align-items-center gap-1">
                                            <input class="form-check-input"
                                                type="radio"
                                                wire:model="teacher_status"
                                                value="present" @disabled($leave)>
                                            <span class="badge bg-success">Pr</span>
                                        </label>
                                    </div>

                                    <div class="form-check form-check-danger">
                                        <label class="d-flex align-items-center gap-1">
                                            <input class="form-check-input"
                                                type="radio"
                                                wire:model="teacher_status"
                                                value="absent" @disabled($leave)>
                                            <span class="badge bg-danger">Ab</span>
                                        </label>
                                    </div>
                                    <div class="form-check form-check-danger">
                                        <label class="d-flex align-items-center gap-1">
                                            <input class="form-check-input"
                                                type="radio"
                                                wire:model="teacher_status"
                                                value="excused" @disabled($leave)>
                                            <span class="badge bg-info">Exc</span>
                                        </label>
                                    </div>

                                    <div class="form-check form-check-warning">
                                        <label class="d-flex align-items-center gap-1">
                                            <input class="form-check-input"
                                                type="radio"
                                                wire:model="teacher_status"
                                                value="late" @disabled($leave)>
                                            <span class="badge bg-warning">Lt</span>
                                        </label>
                                    </div>

                                    @if($leave)

                                        <div class="form-check form-check-primary">

                                            <label class="d-flex align-items-center gap-1">

                                                <input
                                                    class="form-check-input"
                                                    type="radio"
                                                    wire:model="teacher_status"
                                                    value="leave"
                                                    checked
                                                    disabled
                                                >

                                                <span class="badge bg-primary">

                                                    {{ $leave->leaveType?->name }}

                                                </span>

                                            </label>

                                        </div>

                                        @endif

                                </div>

                                {{-- Note --}}
                                <textarea class="form-control"
                                        rows="2"
                                        wire:model="teacher_note"
                                        placeholder="Teacher note (optional)">
                                </textarea>

                            </div>

                        </div>

                    </div>
                </div>
                @endif
                <!-- ==============end teacher attendance--------------------------------- -->
                <div class="table-responsive">
              
                <table class="table table-sm table-bordered align-middle"
                    style="font-size:13px; width:100%; table-layout:auto;">

                    <thead class="table-dark">
                        <tr>

                            <th style="width:45px;">NO</th>

                            <th style="width:130px;">
                                {{ __('label.payment_status') }}
                            </th>

                            <th>{{ __('label.student_code') }}</th>
                            <th>{{ __('label.name') }}</th>
                            <th>{{ __('label.father_name') }}</th>

                            <th style="min-width:170px;">
                                {{ __('label.status') }}
                            </th>

                            <th style="width:90px;">
                                {{ __('label.absent_days') }}
                            </th>

                            <th style="min-width:230px;">
                                {{ __('label.comment') }}
                            </th>

                            <th style="width:100px;">
                                {{ __('label.date') }}
                            </th>

                        </tr>
                    </thead>

                    <tbody>

                    @foreach($students as $i => $cs)

                        @php
                            $status = $attendances[$cs->student_id]['status'] ?? null;

                            $absent = $cs->absent_days ?? 0;
                            $limit = $cs->course?->book?->drop_days ?? 0;

                            $textClass = '';

                            if ($limit > 0 && $absent >= $limit) {
                                $textClass = 'text-danger fw-bold';
                            } elseif ($limit > 0 && $absent == ($limit - 1)) {
                                $textClass = 'text-warning fw-bold';
                            }
                        @endphp

                        <tr wire:key="student-{{ $cs->student_id }}">

                            <td>{{ $i + 1 }}</td>

                            {{-- PAYMENT --}}
                            <td>
                                @if($cs->payment_status === 'not_registered')
                                    <span class="text-warning">Not Reg</span>
                                @elseif($cs->payment_status === 'paid')
                                    <span class="text-success">Paid</span>
                                @else
                                    <span class="text-danger">
                                        Due {{ number_format($cs->remaining_amount) }}
                                    </span>
                                @endif
                            </td>

                            <td>
                                <span class="{{ $textClass }}">
                                    {{ $cs->student?->student_code }}
                                </span>
                            </td>

                            <td>
                                <span class="{{ $textClass }}">
                                    {{ $cs->student?->name }}
                                </span>
                            </td>

                            <td>
                                <span class="{{ $textClass }}">
                                    {{ $cs->student?->father_name }}
                                </span>
                            </td>

                            {{-- STATUS --}}
                            <td>

                            @if(Auth::user()->isDeveloper() || Auth::user()->isAdmin() || ($attendance_date === now()->format('Y-m-d')))

                                <div class="d-flex gap-2 flex-wrap">

                                    <label class="d-flex align-items-center gap-1">
                                        <input
                                            class="form-check-input"
                                            type="radio"
                                            wire:model="attendances.{{ $cs->student_id }}.status"
                                            value="present">

                                        <span class="badge bg-success"
                                            style="font-size:11px;padding:4px 8px;">
                                            pr
                                        </span>
                                    </label>

                                    <label class="d-flex align-items-center gap-1">
                                        <input
                                            class="form-check-input"
                                            type="radio"
                                            wire:model="attendances.{{ $cs->student_id }}.status"
                                            value="absent">

                                        <span class="badge bg-danger"
                                            style="font-size:11px;padding:4px 8px;">
                                            ab
                                        </span>
                                    </label>

                                    <label class="d-flex align-items-center gap-1">
                                        <input
                                            class="form-check-input"
                                            type="radio"
                                            wire:model="attendances.{{ $cs->student_id }}.status"
                                            value="late">

                                        <span class="badge bg-warning text-dark"
                                            style="font-size:11px;padding:4px 8px;">
                                            lt
                                        </span>
                                    </label>

                                    <label class="d-flex align-items-center gap-1">
                                        <input
                                            class="form-check-input"
                                            type="radio"
                                            wire:model="attendances.{{ $cs->student_id }}.status"
                                            value="excused">

                                        <span class="badge bg-info"
                                            style="font-size:11px;padding:4px 8px;">
                                            exc
                                        </span>
                                    </label>

                                </div>

                            @endif

                            </td>

                            {{-- ABSENT DAYS --}}
                            <td>
                                <span class="{{ $textClass }}">
                                    {{ $cs->absent_days > 0 ? $cs->absent_days : '' }}
                                </span>
                            </td>

                            {{-- COMMENT --}}
                            <td>
                                <textarea
                                    class="form-control form-control-sm"
                                    rows="2"
                                    style="font-size:13px;"
                                    wire:model="attendances.{{ $cs->student_id }}.note">
                                </textarea>
                            </td>

                            {{-- DATE --}}
                            <td>
                                {{ $cs?->attendance_date?->format('Y/m/d') }}
                            </td>

                        </tr>

                    @endforeach

                    </tbody>

                </table>


            </div>
             @if(add(Auth::user()->role_ids,$active_menu_id) && count($students) > 0)
                  @if(Auth::user()->isDeveloper() || Auth::user()->isAdmin() || ($attendance_date === now()->format('Y-m-d')))
                    <div class="d-flex justify-content-end mt-4 mb-3 px-3">
                        <button type="button" class="btn btn-primary" wire:click="saveAttendance">
                            <i class="bi bi-save me-1"></i> {{ __('label.save_attendance') }}
                        </button>
                    </div>
                    @endif
                @endif

             @else

                <div class="alert alert-warning text-center mt-3">
                    🚫 No students found in this course.
                </div>

    
            @endif
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

        

        $('#teacher_ids').off('change').on('change', function () {
             $wire.set('teacher_ids', $(this).val());
        });

        $('#program_id').off('change').on('change', function () {
            $wire.set('program_id', $(this).val());
            $wire.call('loadProgramBook', $(this).val());
        });

        $('#search_program_id').off('change').on('change', function () {
            $wire.set('search.program_id', $(this).val());
            $wire.call('loadProgramBook', $(this).val());
        });

        $('#book_id').off('change').on('change', function () {
            $wire.set('book_id', $(this).val());
        });

        $('#branch_id').off('change').on('change', function () {
            $wire.set('branch_id', $(this).val());
            $wire.call('loadClassroomAndTeacher', $(this).val());
        });

        $('#search_book_id').off('change').on('change', function () {
            $wire.set('search.book_id', $(this).val());
        });
        $('#search_teacher_id').off('change').on('change', function () {
            $wire.set('search.teacher_id', $(this).val());
        });
        $('#search_course_id').off('change').on('change', function () {
            $wire.set('course_id', $(this).val());
        });
    }

    initSelect2();

    Livewire.hook('morphed', () => {
        initSelect2();
    });

    $(document).on('shown.bs.modal', function () {
        initSelect2();
    });

    Livewire.on('reset-select2', () => {
        $('#search_course_id').val(null).trigger('change');        
    });
});
</script>
@endscript