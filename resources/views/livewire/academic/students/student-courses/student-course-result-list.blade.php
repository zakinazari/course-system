<div>

 <style>
    .print-page {
        position: relative;
        width: 297mm;
        height: 210mm;
        margin: 0 auto;
        padding: 25px;
        box-sizing: border-box;

        background-image: url('{{ asset("assets/images/certificates/" . ($result_card?->course?->branch?->code ?? 'default') . ".jpg") }}');
        background-size: 100% 100%;
        background-repeat: no-repeat;
        background-position: center;
    }

    @media print {

        @page {
            /* size: A4 landscape; */
            margin: 7mm 10mm 0 10mm;
        }

        .print-page {
            width: 297mm;
            height: 210mm;
            padding: 7mm 10mm 0 10mm;
            box-sizing: border-box;
            page-break-after: always;
            overflow: hidden;
        }
    }


    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        padding: 120px 80px; /* تنظیم موقعیت متن‌ها */
        box-sizing: border-box;
    }

    .student-id{
        position:absolute;
        top:175px;
        left:390px;
        color:#000 !important;
    }

    .score{
        position:absolute;
        top:175px;
        right:420px;
        color:#000 !important;
    }

    .book{
        position:absolute;
        top:203px;
        left:438px;
        color:#000 !important;
    }

    .grade{
        position:absolute;
        top:203px;
        right:420px;
        color:#000 !important;
    }

    .start_date{
        position:absolute;
        top:256px;
        left:160px;
        color:#000 !important;
    }
    .end_date{
        position:absolute;
        top:254px;
        right:70px;
        color:#000 !important;
    }

.student_name{
        position: absolute;
        top: 360px;
        left: 50%;
        font-size:30px;
        font-style: italic;
        font-weight: bold;

        color:#000 !important;
        transform: translateX(-50%);
    }

.main_text{
        position: absolute;
        top: 430px;
        left: 50%;
        font-size:18px;
        font-style: italic;
        text-align:center;
        white-space:nowrap;
        line-height:1;
        color:#000 !important;
        transform: translateX(-50%);
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
                            <th>{{ __('label.result_card') }}</th>
                            <th>{{ __('label.action') }}</th>
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
                                @elseif($result?->status === 'in_progress')
                                    <span class="badge bg-label-info me-1" style="font-size:10px;">
                                        {{ ucfirst($result?->status) }}
                                    </span>
                                @endif
                            </td>
                            <td>{{ $result?->course?->teacher?->name }} {{ $result?->course?->teacher?->last_name }}</td>
                            <td>
                                <a class="btn btn-success btn-icon rounded-pill"
                                href="javascript:void(0);"
                                wire:click="resultCard({{ $result->id }})">
                                    <i class="bx bx-book-content text-white"></i>
                                </a>
                            </td>
                            <td>
                                <div class="dropdown position-static">
                                    <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if(edit(Auth::user()->role_ids,$active_menu_id))
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="edit({{ $result->id }})"
                                            ><i class="bx bx-edit-alt me-1 text-success"></i>{{ __('label.edit') }}</a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 justify-content-end px-3">
                {{ $course_results->links() }}
            </div>
    </div>
    

    <div class="modal fade" id="{{$modalId}}" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog modal-lg" branch="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@if($editMode) {{ __('label.editing') }}  @else {{ __('label.adding') }} @endif @if(App::getLocale() =='en') {{ $active_menu?->name_en }} @elseif(App::getLocale()=='fa') {{ $active_menu?->name }} @endif</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <form wire:submit.prevent="update">
                    <div class="modal-body" wire:ignore.self>
                        <div class="col md-2 mb-3">
                        <label for="course_type_id" class="form-label">{{ __('label.exam_period') }}</label>
                        <select class="form-select @error('type') is-invalid @enderror" wire:model.lazy="exam_period" id ="exam_period">
                            <option value="midterm"  wire:key="type-key-medterm">{{ __('label.midterm') }}</option>
                            <option value="final"  wire:key="type-key-final">{{ __('label.final') }}</option>
                            <option value="all">{{ __('label.all') }}</option>
                        </select>
                    </div>

                        <div class="alert {{ $this->total > 100 ? 'alert-danger' : 'alert-info' }} d-flex justify-content-between">
                            <span>{{ __('label.total_score') }}</span>
                            <strong>{{ number_format($this->total, 1) }} / 100</strong>
                        </div>

                        @foreach($scores as $index => $row)

                            <div class="border rounded p-3 mb-2"
                                wire:key="score-{{ $index }}">

                                <div class="d-flex justify-content-between align-items-center mb-2">

                                    <span class="fw-bold">
                                        {{ $row['exam_type_name'] }}
                                    </span>

                                    <span class="badge bg-primary">
                                        {{ $row['percentage'] }}%
                                    </span>

                                </div>

                                <input
                                    type="number"
                                    class="form-control"
                                    min="0"
                                    max="{{ $row['percentage'] }}"
                                    step="0.1"
                                    wire:model.live="scores.{{ $index }}.score"
                                >

                                @if(($row['score'] ?? 0) > $row['percentage'])
                                    <small class="text-danger">
                                        {{ __('label.max_allowed') }} {{ $row['percentage'] }}
                                    </small>
                                @endif

                            </div>

                        @endforeach

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" >{{ __('label.close') }}</button>
                        <button type="submit" class="btn btn-primary">@if($editMode) {{ __('label.update') }}  @else {{ __('label.save') }} @endif</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="resultCardModal" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog modal-xl" branch="document">
            <div class="modal-content">
               <div class="modal-header d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-2">

                    <!-- TITLE -->
                    <h5 class="modal-title mb-0 flex-grow-1">
                        {{ __('label.result_card') }}
                    </h5>

                    <!-- ACTIONS -->
                    <div class="d-flex gap-2 align-items-center">

                        <a href="#"
                        wire:click.prevent="print"
                        class="btn btn-secondary btn-sm d-flex align-items-center gap-1">

                            <i class="fa fa-print"></i>
                            <span class="d-none d-sm-inline">
                                {{ __('label.print') }}
                            </span>

                        </a>

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Close">
                        </button>

                    </div>

                </div>
                
                <div class="modal-body">
                    
        
                    @if(!empty($result_card))
                        <div id="printArea" dir="ltr">
                            
                            <div class="print-page">

                                <!-- ===============================
                                    Table Information
                                =============================== -->

                                <div class="student-id">
                                    {{ $result_card->student?->student_code }}
                                </div>

                                <div class="score">
                                    {{ $result_card->total }}
                                </div>

                                <div class="book">
                                    {{ $result_card->course?->book?->name }}
                                </div>

                                <div class="grade">
                                    {{ $result_card->grade }}
                                </div>

                                <div class="start_date">
                                    {{ $result_card->course?->start_date?->format('Y/m/d') }}
                                </div>
                                <div class="end_date">
                                    {{ $result_card->course?->end_date?->format('Y/m/d') }}
                                </div>

                                <div class="student_name">
                                    @if($result_card->student?->gender_id ==1) Mr. @elseif($result_card->student?->gender_id ==2) Miss. @endif
                                    
                                    {{ $result_card->student?->name }}  {{ $result_card->student?->last_name }}

                                </div>

                                <div class="main_text">
                                    <p> @if($result_card->student?->gender_id ==1) Son of @elseif($result_card->student?->gender_id ==2) Daughter of @endif
                                    
                                    {{ $result_card->student?->father_name }}  for the successful completion of the aforementioned course book. This result card is </p>
                                    <p>  issued as evidence for the next step toward further achievements. </p>

                                </div>
                            </div>

                        </div>
                    @endif

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('label.close') }}</button>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    window.addEventListener('show-print-preview', () => {
        printDiv('printArea');
    });
</script>

