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

            <h5 class="card-title mb-0">{{ $active_menu?->name }}</h5>

            <div class="d-flex align-items-center gap-2">
                <div class="btn-group">
                        <button type="button" class="btn btn-secondary">
                            <i class="fa fa-file-export"></i> {{ __('label.export') }}
                        </button>

                        <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Toggle Dropdown</span>
                        </button>

                        <ul class="dropdown-menu">
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
                                <a class="dropdown-item d-flex align-items-center gap-2" href="#" wire:click.prevent="print">
                                    <i class="fa fa-print text-secondary" ></i> {{ __('label.print') }}
                                </a>
                            </li>
                        </ul>
                    </div>
            </div>

        </div>
        <hr>
        <div class="table-responsive text-nowrap ">
 
            <div class="mb-3 px-3 mb-5">
                <form wire:submit.prevent="applySearch" class="row g-3 align-items-end">
                  
                    @if(!auth()->user()->branch_id)
                    <div class="col-md-2">
                        <label class="form-label">{{ __('label.branch') }}</label>
                        <select class="form-select" wire:model.defer="search.branch_id" id ="">
                           <option value="">{{ __('label.all') }}</option>
                           @foreach($branches as $branch)
                                 <option value="{{ $branch->id }}"  wire:key="branch-search-{{ $branch->id }}">
                                    {{ $branch->name }}
                                 </option>
                           @endforeach
                        </select>
                     </div>
                     @endif
                     <div class="col-md-2" wire:ignore>
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

                    <div class="@if(!auth()->user()->branch_id) col-md-6 @else col-md-3  @endif d-flex flex-column">
                        <label class="form-label">{{ __('label.course') }}</label>
                        <select class="form-select select2" wire:model="search.course_id" id="search_course_id">
                           <option value="">{{ __('label.all') }}</option>
                           @foreach($courses as $course)
                                 <option value="{{ $course->id }}"  wire:key="course-search-{{ $course->id }}">
                                    {{ $course->name }}
                                 </option>
                           @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('label.payment_type') }}</label>
                        <select class="form-select" wire:model.defer="search.payment_type" id ="">
                           <option value="">{{ __('label.all') }}</option>
                            <option value="full">{{ __('label.full') }}</option>
                            <option value="installment">{{ __('label.installment') }}</option>
                        </select>
                     </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('label.from_date') }}</label>
                        <div class="input-group input-daterange" id="bs-datepicker-daterange">
                            <input type="date" id="dateRangePicker" class="form-control" wire:model="search.from">
                            <span class="input-group-text">{{ __('label.to_date') }}</span>
                            <input type="date"  class="form-control" wire:model="search.to">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary">
                            {{ __('label.search') }}
                        </button>
                    </div>
                
                </form>
            </div>
            <div class="table-responsive text-nowrap">
                @if(count($fees) > 0)

                <!-- Title -->
               
                @if(!empty($search['payment_type']))
                <h5 style="text-align:center;">
                {{ __('label.payment_type') }}: {{ ucfirst($search['payment_type']) }}
                </h5>
                @endif
        
                <table class="table">
                    <thead class="table-dark">
                        <tr>
                            <th style="width:40px;">
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="no">
                                {{ __('label.NO') }}
                            </th>
                            @if(!auth()->user()->branch_id)
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="branch">
                                {{ __('label.branch') }}
                            </th>
                            @endif
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="program">
                                {{ __('label.program') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="course">
                                {{ __('label.course') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="amount">
                                {{ __('label.amount') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="payment_date">
                                {{ __('label.payment_date') }}
                            </th>
                        </tr>
                    </thead>

                    <tbody class="table-border-bottom-0">
                         @foreach($fees as $i => $fee)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            @if(!auth()->user()->branch_id)
                            <td>{{ $fee->studentCourseFee->branch?->name }}</td>
                            @endif
                            <td>{{ $fee->studentCourseFee->course?->program?->name }}</td>
                            <td>{{ $fee->studentCourseFee->course?->name }}</td>
                            <td>{{ $fee->amount }}</td>
                            <td>{{ $fee->payment_date->format('Y/m/d') }}</td>
                        
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-dark">
                        <tr>
                            <th colspan="4">{{ __('label.total') }}</th>
                            <th colspan="2">{{ $total_payments }}</th>
                        </tr>
                    </tfoot>
                </table>
                @endif
            </div>
        </div>
    </div>

   <div id="printArea" style="display:none;">
      <!-- Logo -->
        <div style="text-align:center;margin-bottom:10px;">
            <img src="{{ asset('logo.png') }}" alt="Logo" style="height:70px;">
        </div>
        <!-- Title -->
        <h2 style="text-align:center;">
            {{ __('label.student_course_fee_report') }}
        </h2>
        @if(!empty($search['payment_type']))
        <h2 style="text-align:center;">
           {{ __('label.payment_type') }}: {{ ucfirst($search['payment_type']) }}
        </h2>
        @endif

        <!-- Date range, aligned left, close to table -->
        <div style="margin-bottom:3px; font-size:12px; text-align:left;">
        {{ __('label.from_date') }}: {{ $search['from'] ?? '---' }} &nbsp;&nbsp; {{ __('label.to_date') }}: {{ $search['to'] ?? '---' }}
        </div>

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    @if(in_array('no', $selectedFields)) <th>{{ __('label.no') }}</th> @endif
                    @if(in_array('branch', $selectedFields)) <th>{{ __('label.branch') }}</th> @endif
                    @if(in_array('program', $selectedFields)) <th>{{ __('label.program') }}</th> @endif
                    @if(in_array('course', $selectedFields)) <th>{{ __('label.course') }}</th> @endif
                    @if(in_array('amount', $selectedFields)) <th>{{ __('label.amount') }}</th> @endif
                    @if(in_array('payment_date', $selectedFields)) <th>{{ __('label.payment_date') }}</th> @endif
                </tr>
            </thead>
            <tbody>
                @foreach($fees as $i => $fee)
                    <tr>
                        @if(in_array('no', $selectedFields)) <td>{{ $i + 1 }}</td> @endif
                        @if(in_array('branch', $selectedFields)) <td>{{ $fee->studentCourseFee?->branch?->name }}</td> @endif
                        @if(in_array('program', $selectedFields)) <td>{{ $fee->studentCourseFee->course?->program?->name }}</td> @endif
                        @if(in_array('course', $selectedFields)) <td>{{ $fee->studentCourseFee->course?->name }}</td> @endif
                        @if(in_array('amount', $selectedFields)) <td>{{ $fee->amount }}</td> @endif
                        @if(in_array('payment_date', $selectedFields)) <td>{{ $fee->payment_date->format('Y/m/d') }}</td> @endif
                    </tr>
                @endforeach
            </tbody>
            @if(in_array('amount', $selectedFields))
            <tfoot class="table-dark">
                <tr>
                    <th colspan="{{ count($selectedFields) - 2 }}">{{ __('label.total') }}</th>
                    <th>{{ $total_payments }}</th>
                    <th>&nbsp;</th>
                </tr>
            </tfoot>
            @endif
        </table>
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

        $('#search_program_id').off('change').on('change', function () {
            $wire.set('search.program_id', $(this).val());
            // $wire.call('loadProgramBook', $(this).val());
        });

        $('#search_book_id').off('change').on('change', function () {
            $wire.set('search.book_id', $(this).val());
        });
        $('#search_course_id').off('change').on('change', function () {
            $wire.set('search.course_id', $(this).val());
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

<script>
    window.addEventListener('show-print-preview', () => {
        printDiv('printArea');
    });
</script>