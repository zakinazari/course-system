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
            {{ __('label.result_sheet') }} 
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
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>{{ __('label.NO') }}</th>
                            <th>{{ __('label.book') }}</th>
                            <th>{{ __('label.exam_scores') }}</th>
                            <th>{{ __('label.total') }}</th>
                            <th>{{ __('label.status') }}</th>
                            <th>{{ __('label.teacher') }}</th>
                        </tr>

                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($course_results as $i => $result)
                        <tr>
                            <td>{{ ($course_results->currentPage() - 1) * $course_results->perPage() + $i + 1 }}</td>
                            <td>{{ $result->course?->book?->name }}</td>
                            <td style="padding:0;">
                                <table class="table table-sm table-bordered mb-0">
                                    <tbody>
                                        @foreach($result->examScores as $score)
                                            <tr>
                                                <td>{{ $score->examType?->name ?? 'Exam' }}</td>
                                                <td>{{ $score->score }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                            <td>{{ $result->total }}</td>
    
                            <td>
                                 @if($result?->status === 'passed')
                                    <span class="badge bg-label-success me-1" style="font-size:10px;">
                                        {{ ucfirst($result?->status) }}
                                    </span>
                                @elseif($result?->status === 'makeup')
                                    <span class="badge bg-label-warning me-1" style="font-size:10px;">
                                        {{ ucfirst($result?->status) }}
                                    </span>
                                @elseif($result?->status === 'failed')
                                    <span class="badge bg-label-danger me-1" style="font-size:10px;">
                                        {{ ucfirst($result?->status) }}
                                    </span>
                                @endif
                            </td>
                            <td>{{ $result?->course?->teacher?->name }} {{ $result?->course->teacher?->last_name }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 justify-content-end px-3">
                {{ $course_results->links() }}
            </div>
    </div>
    
</div>



