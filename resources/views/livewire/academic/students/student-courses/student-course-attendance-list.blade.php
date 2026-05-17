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

   
        <div class="table-responsive text-nowrap">
 
            <div class="mb-3 px-3">
                <form wire:submit.prevent="applySearch" class="row g-3 align-items-end">
                   
                        <div class="col col-md-6  d-flex flex-column">
                            <label class="form-label">{{ __('label.student_course') }}</label>
                            <select class="form-select select2 @error('status') is-invalid @enderror" id="search_course_id">
                                <option value="">{{ __('label.select') }}</option>
                                @foreach($student_courses as $course)
                                <option value="{{ $course->id }}"  wire:model.lazy="search.course_id" wire:key="student-course-option-{{ $course->id }}">{{ $course?->name }}</option>
                                @endforeach
                            </select>
                            @error('target_course_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    
                        <div class="col-md-6">
                            <label class="form-label">{{ __('label.from_date') }}</label>
                            <div class="input-group input-daterange" id="bs-datepicker-daterange">
                                <input type="date" id="dateRangePicker" class="form-control" wire:model.lazy="search.from">
                                <span class="input-group-text">{{ __('label.to_date') }}</span>
                                <input type="date"  class="form-control" wire:model.lazy="search.to">
                            </div>
                        </div>
                        <!-- <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">
                                {{ __('label.search') }}
                            </button>
                        </div> -->
                </form>
                <hr>
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
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>{{ __('label.NO') }}</th>
                            <th>{{ __('label.book') }}</th>
                            <th>{{ __('label.time') }}</th>
                            <th>{{ __('label.status') }}</th>
                            <th>{{ __('label.date') }}</th>
                        </tr>

                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($attendances as $i => $attendance)
                        <tr>
                            <td>{{ ($attendances->currentPage() - 1) * $attendances->perPage() + $i + 1 }}</td>
                            <td>{{ $attendance->course?->book?->name }}</td>
                            <td>{{ $attendance->course?->time?->start_time?->format('h:i A') }} - {{ $attendance?->course->time?->end_time?->format('h:i A') }}</td>
                            <td>
                                 @if($attendance->status === 'present')
                                    <span class="badge bg-label-success me-1" style="font-size:10px;">
                                        {{ ucfirst($attendance->status) }}
                                    </span>
                                @elseif($attendance->status === 'late')
                                    <span class="badge bg-label-warning me-1" style="font-size:10px;">
                                        {{ ucfirst($attendance->status) }}
                                    </span>
                                @elseif($attendance->status === 'excused')
                                    <span class="badge bg-label-info me-1" style="font-size:10px;">
                                        {{ ucfirst($attendance?->status) }}
                                    </span>
                                @elseif($attendance->status === 'absent')
                                    <span class="badge bg-label-danger me-1" style="font-size:10px;">
                                        {{ ucfirst($attendance?->status) }}
                                    </span>
                                @endif
                            </td>
                            <td>{{ $attendance->attendance_date?->format('Y/m/d') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 justify-content-end px-3">
                {{ $attendances->links() }}
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

        $('#search_course_id').off('change').on('change', function () {
            @this.set('search.course_id', $(this).val());
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