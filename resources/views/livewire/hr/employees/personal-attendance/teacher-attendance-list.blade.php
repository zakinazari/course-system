
<div>

    <div class="">
       
       <div class="card-header">
            
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="card-title mb-2 mb-md-0">
                    {{ __('label.teacher_attendance') }}
                </h5>

                <!-- <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
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
                            </li>
                        </ul>
                    </div>

     
                </div> -->

            </div>
        </div>
        <hr>
        <div class=" text-nowrap">
 
            <div class="mb-3 px-3 mb-5">
                <form wire:submit.prevent="applySearch" class="row g-3 align-items-end">
                    <div class="col-md-3 d-flex flex-column">
                        <label class="form-label">{{ __('label.year') }}</label>
                        <select  class="form-select" wire:model.lazy ="year">

                           @foreach($years as $year)
                                 <option value="{{ $year->year }}"  wire:key="year-search-{{ $year->year }}">
                                    {{ $year->year }}
                                 </option>
                           @endforeach
                        </select>
                        
                    </div>
                    <div class="col-md-3 d-flex flex-column">
                        <label class="form-label">{{ __('label.month') }}</label>
                        <select  class="form-select" wire:model.lazy ="month">
                           @foreach($months as $month)
                                 <option value="{{ $month->number }}"  wire:key="month-search-{{ $month->number }}">
                                    {{ $month->name }}
                                 </option>
                           @endforeach
                        </select>
                    </div>
                    
                    <div class="col col-md-6  d-flex flex-column">
                        <label class="form-label">{{ __('label.courses') }}</label>

                        <select class="form-select" wire:model.lazy="search.course_id" id="search_course_id">
                            <option value="">{{ __('label.select') }}</option>
                            @foreach($teacher_courses as $course)
                            <option value="{{ $course->id }}"   wire:key="student-course-option-{{ $course->id }}">{{ $course?->name }}</option>
                            @endforeach
                        </select>

                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('label.from_date') }}</label>
                        <div class="input-group input-daterange" id="bs-datepicker-daterange">
                            <input type="date" id="dateRangePicker" class="form-control" wire:model.lazy="search.from">
                            <span class="input-group-text">{{ __('label.to_date') }}</span>
                            <input type="date"  class="form-control" wire:model.lazy="search.to">
                        </div>
                    </div>
                
                    <!-- <div class="col-md-1">
                        <button type="submit" class="btn btn-primary">
                            {{ __('label.search') }}
                        </button>
                    </div> -->
                </form>

                <!-- perPage -->
                <div class="d-flex align-items-center gap-1 mt-3 justify-content-end">
                    <span>{{ __('label.show') }}</span>
                    <select class="form-select w-auto" wire:model.live="perPage">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="">{{ __('label.all') }}</option>
                    </select>
                    <span>{{ __('label.entries') }}</span>
                </div>
            </div>
            <!--  contents --> 
           
               <div class="table-responsive text-nowrap">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>{{ __('label.NO') }}</th>
                            <th>{{ __('label.book') }}</th>
                            <th>{{ __('label.status') }}</th>
                            <th>{{ __('label.date') }}</th>
                            <th style="">{{ __('label.note') }}</th>
                            <th>{{ __('label.actions') }}</th>
                        </tr>

                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($attendances as $i => $attendance)
                        <tr>
                            <td>{{ ($attendances->currentPage() - 1) * $attendances->perPage() + $i + 1 }}</td>
                            
                            <td>{{ $attendance->course?->book?->name }}</td>
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
                                @elseif($attendance->status === 'leave')
                                    @if($attendance->leaveType?->is_paid)
                                    <span class="badge bg-label-primary me-1" style="font-size:10px;">
                                        {{ $attendance?->leaveType?->name }}
                                    </span>
                                    @else
                                        <span class="badge bg-label-danger me-1" style="font-size:10px;">
                                        {{ $attendance?->leaveType?->name }}
                                    </span>
                                    @endif
                                @endif
                            </td>
                            <td>{{ $attendance->attendance_date?->format('Y/m/d') }}</td>
                            <td style="">{{ $attendance->note }}</td>
                            <td>
                                <div class="dropdown position-static">
                                    <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if(edit(Auth::user()->role_ids,$active_menu_id))
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="edit({{ $attendance->id }})"
                                            ><i class="bx bx-edit-alt me-1 text-success"></i>{{ __('label.edit') }}</a>
                                        @endif

                                        @if(delete(Auth::user()->role_ids,$active_menu_id))
                                            <a class="dropdown-item " href="javascript:void(0);"  onclick="confirmDelete({{ $attendance->id }},'{{$table_name}}')"
                                            ><i class="bx bx-trash me-1 text-danger"></i>{{ __('label.delete') }}</a>
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
                {{ $attendances->links() }}
            </div>
            <!--  contents --> 
        </div>
    </div>


    <div class="modal fade" id="{{$modalId}}" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog" branch="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@if($editMode) {{ __('label.editing') }}  @else {{ __('label.adding') }} @endif {{ __('label.staff_attendance') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <form @if($editMode) wire:submit.prevent="update" @else wire:submit.prevent="store" @endif>
                    <div class="modal-body">
                     
                    <div class="row">
                        <div class="col mb-3">
                            <label class="form-label">{{ __('label.status') }} <span style="color:red;">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" wire:model.lazy="status" id ="" >
                            <option value="">{{ __('label.select') }}</option>
                                <option value="present">{{ __('label.present') }}</option>
                                <option value="absent">{{ __('label.absent') }}</option>
                                <option value="excused">{{ __('label.excused') }}</option>
                                <option value="late">{{ __('label.late') }}</option>
                            </select>
                            @error('status') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label" >{{ __('label.reason') }} <span style="color:red;">*</span></label>
                            <textarea type="text" id="nameBasic" class="form-control @error('note') is-invalid @enderror" wire:model.lazy="note"></textarea>
                            @error('note') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" >{{ __('label.close') }}</button>
                        <button type="submit" class="btn btn-primary">@if($editMode) {{ __('label.update') }}  @else {{ __('label.save') }} @endif</button>
                    </div>
                </form>
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

        $('#search_course_id').off('change').on('change', function () {
            @this.set('search.course_id', $(this).val());
        });

    }

    initSelect2();

     Livewire.hook('morphed', () => {
        initSelect2();
    });

   
    Livewire.hook('message.processed', function (message, component) {
        const $modal = $('#{{$modalId}}');
        if ($modal.is(':visible')) {
            initSelect2();
        }
    });


    $(document).on('shown.bs.modal', function () {
        initSelect2();
    });

});
</script>
@endscript



