<div>
<style>
@media print {
    body, html {
        background: #fff !important;
        -webkit-print-color-adjust: exact;
    }

    body * {
        visibility: hidden;
    }

    #printArea, #printArea * {
        visibility: visible;
    }

    #printArea {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        display: block !important;
        direction: ltr !important;
    }
}

/* Logo */
.logo-container {
    text-align: center;
    margin-bottom: 15px;
}

.logo-container .logo {
    max-width: 120px;
}

/* Table Styling */
.bill-table {
    width: 100%;
}

.bill-table td {
    padding: 8px;
    border: 1px solid #000;
}

</style>

<div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">

    <!-- عنوان کارت -->
    <h5 class="card-title mb-0">
            {{ __('label.student_courses') }} 
    </h5>

    <!-- دکمه‌ها -->
        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-2">

            <!-- دکمه Export -->
            <!-- <div class="btn-group mb-2 mb-md-0">
                <button type="button" class="btn btn-secondary">
                    <i class="fa fa-file-export"></i> {{ __('label.export') }}
                </button>

                <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="visually-hidden">Toggle Dropdown</span>
                </button>

                <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                    <li class="px-3 py-2">
                        <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-3 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" id="portraitRadio" wire:model="pdfOrientation" value="portrait">
                                <label class="form-check-label" for="portraitRadio">(Portrait)</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" id="landscapeRadio" wire:model="pdfOrientation" value="landscape">
                                <label class="form-check-label" for="landscapeRadio">(Landscape)</label>
                            </div>
                        </div>

                        <a class="dropdown-item d-flex align-items-center gap-2" href="#" wire:click.prevent="exportPdf">
                            <i class="fa fa-file-pdf text-danger"></i> {{ __('label.export_to_pdf') }}
                        </a>
                    </li>
                </ul>
            </div> -->

            <!-- دکمه Add New Record -->
            @if(add(Auth::user()->role_ids,$active_menu_id))
                <!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#{{$modalId}}" wire:click="openModal">
                    <i class="bi bi-plus-lg"></i> {{ __('label.add_new_record') }} 
                </button> -->
            @endif

            </div>
        </div>
        <hr>
        <div class="table-responsive text-nowrap">
 
            <div class="mb-3 px-3">
                <!-- <form wire:submit.prevent="applySearch" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('label.name') }} / {{ __('label.student_code') }} </label>
                        <input type="text" class="form-control" placeholder="" wire:model="search.identity">
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">
                            {{ __('label.search') }}
                        </button>
                    </div>
                </form> -->

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
                            <th>{{ __('label.name') }}</th>
                            <th>{{ __('label.program') }}</th>
                            <th>{{ __('label.book') }}</th>
                            <th>{{ __('label.status') }}</th>
                            <th>{{ __('label.change_time') }}</th>
                            <th>{{ __('label.time') }}</th>
                            <th>{{ __('label.classroom') }}</th>
                            <th>{{ __('label.teacher') }}</th>
                        </tr>

                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($student_courses as $i => $course)
                        <tr>
                            <td>{{ ($student_courses->currentPage() - 1) * $student_courses->perPage() + $i + 1 }}</td>
                            <td>{{ $course->name }}</td>
                            <td>{{ $course->program?->name }}</td>
                            <td>{{ $course->book?->name }}</td>
    
                            <td>
                                 <span class="badge rounded-pill {{ $course->status_badge_class }}">
                                    {{ __('label.' . $course->status) }}
                                </span>

                            </td>
                            <td>
                                @if(edit(Auth::user()->role_ids,$active_menu_id) && $course->status==='ongoing')
                                <button
                                    class="btn btn-primary btn-sm rounded-pill"
                                    wire:click="changeTime({{ $course->id }})">
                                    {{ __('label.change_time') }}
                                </button>
                                @endif
                            </td>
                            <td>{{ $course->time?->start_time?->format('h:i A') }} - {{ $course->time?->end_time?->format('h:i A') }}</td>
                            <td>{{ $course->classroom?->name }}</td>
                            <td>{{ $course->teacher?->name }} {{ $course->teacher?->last_name }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 justify-content-end px-3">
                {{ $student_courses->links() }}
            </div>
    </div>
    

    <div class="modal fade" id="changeTimeModal" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog modal-lg" branch="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('label.change_time') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <form wire:submit.prevent="changeTimeStore">
                    <div class="modal-body">

                        <div class="row">
                            <div class="col mb-3">
                                <label class="form-label">{{ __('label.target_course') }}</label>
                                <select class="form-select select2 @error('status') is-invalid @enderror" id="change_time_target_course_id">
                                    <option value="">{{ __('label.select') }}</option>
                                    @foreach($target_courses as $t_course)
                                    <option value="{{ $t_course->id }}"  wire:key="change-time-target-course-option-{{ $t_course->id }}">{{ $t_course->name }}</option>
                                    @endforeach
                                </select>
                                @error('target_course_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
    
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" >{{ __('label.close') }}</button>
                        <button type="submit" class="btn btn-success">{{ __('label.change') }} </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@script
<script>
document.addEventListener("livewire:initialized", function () {

    function initStudentSelect() {

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

        $('#change_time_target_course_id').off('change').on('change', function () {
            $wire.set('target_course_id', $(this).val());
        });
        
    }


    initStudentSelect();

    Livewire.hook('morphed', () => {
        initStudentSelect();
    });


    $(document).on('shown.bs.modal', function () {
        initStudentSelect();
    });

    Livewire.on('reset-select2', () => {
        $('#change_time_target_course_id').val(null).trigger('change');
    });
});

</script>
@endscript
