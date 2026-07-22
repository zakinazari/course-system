
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
       
        <div class="card-header">
      
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="card-title mb-2 mb-md-0">
                    {{ $active_menu?->name }}
                </h5>
            
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <!-- Export Button -->
                    <!-- <div class="btn-group">
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
                            </li>
                        </ul>
                    </div> -->

                    <!-- Add New Record Button -->
                 <a class="btn btn-secondary d-flex align-items-center gap-2"
                    href="#"
                        wire:click.prevent="print">
                        <i class="fa fa-print"></i>
                        {{ __('label.print') }}
                    </a>
                </div>
            </div>
        </div>
        <hr>
        <div class="table-responsive text-nowrap">
 
            <div class="mb-3 px-3">
                <form wire:submit.prevent="applySearch" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('label.name') }}/{{ __('label.student_code') }}</label>
                        <input type="text" class="form-control" placeholder="" wire:model="search.identity">
                    </div>
                    @if(!auth()->user()->branch_id)
                    <div class="col-md-3">
                        <label class="form-label">{{ __('label.branch') }}</label>
                        <select class="form-select" wire:model="search.branch_id">
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
                        <select class="form-select select2" wire:model.defer="search.program_id" id ="search_program_id">
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
                        <select wire:ignore.self class="form-select select2" wire:model.defer="search.book_id" id ="search_book_id">
                           <option value="">{{ __('label.all') }}</option>
                           @foreach($books as $book)
                                 <option value="{{ $book->id }}"  wire:key="book-search-{{ $book->id }}">
                                    {{ $book->name }}
                                 </option>
                           @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('label.shift') }}</label>
                        <select class="form-select" wire:model="search.shift_id">
                           <option value="">{{ __('label.all') }}</option>
                           @foreach($shifts as $shift)
                                 <option value="{{ $shift->id }}"  wire:key="shift-search-{{ $shift->id }}">
                                    {{ $shift->name }}
                                 </option>
                           @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('label.status') }}</label>
                        <select class="form-select" wire:model.defer="search.status" id ="">
                           <option value="">{{ __('label.all') }}</option>
                            <option value="placement">{{ __('label.placement') }}</option>
                            <option value="passed">{{ __('label.passed') }}</option>
                            <option value="failed">{{ __('label.failed') }}</option>
                            <option value="makeup">{{ __('label.makeup') }}</option>
                            <option value="dropped">{{ __('label.dropped') }}</option>
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

                <!-- perPage -->
            
            </div>


            <div class="table-responsive text-nowrap mb-5">
                   @if(!empty($waiting_students))
                  <hr>
                  <div id="printArea">
                        <!-- Logo -->
                        <div style="text-align:center;margin-bottom:10px;">
                            <img src="{{ asset('logo.png') }}" alt="Logo" style="height:70px;">
                        </div>
                        <!-- Title -->
                        <h2 style="text-align:center;">
                            {{ __('label.waiting_student_report') }}
                        </h2>
                    
                        <!-- Date range, aligned left, close to table -->
                        <div style="margin-bottom:3px; font-size:12px; text-align:left;">
                        {{ __('label.from_date') }}: {{ $search['from'] ?? '---' }} &nbsp;&nbsp; {{ __('label.to_date') }}: {{ $search['to'] ?? '---' }}
                        </div>

                      
                        <table class="table table-bordered">

                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('label.student_code') }}</th>
                                <th>{{ __('label.name') }}</th>
                                <th>{{ __('label.father_name') }}</th>
                                <th>{{ __('label.program') }}</th>
                                <th>{{ __('label.book') }}</th>
                                <th>{{ __('label.shift') }}</th>
                                <th>{{ __('label.status') }}</th>
                                <th>{{ __('label.comment') }}</th>
                                <th>{{ __('label.date') }}</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($waiting_students as $index => $waiting)

                                <tr>

                                    <td>{{ $index + 1 }}</td>

                                    <td>
                                        {{ $waiting->waitingList?->student?->student_code ?? '-' }}
                                    </td>
                                    <td>
                                        {{ $waiting->waitingList?->student?->name}} {{ $waiting->waitingList?->student?->last_name}}
                                    </td>
                                    <td>{{ $waiting->waitingList?->student?->father_name ?? '-' }}</td>

                                    <td>{{ $waiting->waitingList?->program?->name ?? '-' }}</td>
                                    <td>{{ $waiting->waitingList?->book?->name ?? '-' }}</td>
                                    <td>{{ $waiting->waitingList?->shift?->name ?? '-' }}</td>

                                    <td>
                                       {{ $waiting->waitingList?->status ? ucfirst($waiting->waitingList->status) : '-' }}
                                    </td>
                                    <td>
                                        {{ $waiting?->comment ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $waiting->created_at?->format('Y/m/d') }}
                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                        </table>
                      
                    </div>
                    @else
                    <div class="alert alert-warning text-center mt-3">
                        🚫 No data found .
                    </div>
                    @endif
            </div>

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
        });

        $('#search_book_id').off('change').on('change', function () {
            $wire.set('search.book_id', $(this).val());
        });
       
        // ---------student----------
        let $student = $('#student_id');

        if (!$student.length) return;

        if ($student.hasClass('select2-hidden-accessible')) {
            $student.select2('destroy');
        }

        let modalId = @js($modalId);
        let menuId = @json($active_menu_id);
        $student.select2({
            dropdownParent: $('#' + modalId),
            width: '100%',
            minimumInputLength: 2,
            ajax: {
                url: '/search-students',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                }
            }
        });

        $student.off('select2:select').on('select2:select', function (e) {
            let data = e.params.data;
            $wire.set('student_id', data.id);
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
