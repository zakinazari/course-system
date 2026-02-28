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
    @if(App::getLocale()=='en') {{ $active_menu?->name_en }} @else {{ $active_menu?->name }}  @endif / {{ __('label.course_details') }}
    </h4>
    <!-- end header -->

      <div class="card academy-content">

                <div class="card-body">
                     <div class="d-flex align-items-center">
                        <h5 class="mb-0">
                            {{ $course->name }}
                        </h5>

                        <a href="{{ url()->previous() }}" class="btn btn btn-secondary ms-auto">
                            ← {{ __('label.return_back') }}
                        </a>
                    </div>

                <hr class="my-4">
                <div class="d-flex flex-wrap">
                    <div class="me-5">
                        <p class="text-nowrap">
                            <i class="bx bx-book-open bx-sm me-2"></i>{{ __('label.book') }}: {{ $course->book?->name }}
                        </p>
                        <p class="text-nowrap">
                            <i class="bx bx-code-alt bx-sm me-2"></i>{{ __('label.program') }}: {{ $course->program?->name }}
                        </p>
                        <p class="text-nowrap">
                            <i class="bx bx-briefcase bx-sm me-2"></i>{{ __('label.course_type') }}: {{ $course->courseType?->name }}
                        </p>
                        <p class="text-nowrap">
                            <i class="bx bx-buildings bx-sm me-2"></i>{{ __('label.branch') }}: {{ $course->branch?->name }}
                        </p>
                    </div>

                    <div class="me-5">
                        <p class="text-nowrap">
                            <i class="bx bx-calendar-event bx-sm me-2"></i>{{ __('label.total_teaching_days') }}: {{ $course->total_teaching_days }}
                        </p>
                        <p class="text-nowrap">
                            <i class="bx bx-time-five bx-sm me-2"></i>{{ __('label.shift') }}: {{ $course->shift->name }}
                        </p>
                        <p class="text-nowrap">
                            <i class="bx bxs-calendar-check bx-sm me-2"></i>{{ __('label.start_date') }}: {{ $course->start_date->format('Y/m/d') }}
                        </p>
                        <p class="text-nowrap">
                            <i class="bx bxs-calendar-check bx-sm me-2"></i>{{ __('label.end_date') }}: {{ $course->end_date->format('Y/m/d') }}
                        </p>
                    </div>

                    <div class="me-5">
                        <p class="text-nowrap">
                            <i class="bx bxs-calendar bx-sm me-2"></i>{{ __('label.mid_exam_date') }}: {{ $course->mid_exam_date->format('Y/m/d') }}
                        </p>
                        <p class="text-nowrap">
                            <i class="bx bxs-calendar bx-sm me-2"></i>{{ __('label.final_exam_date') }}: {{ $course->final_exam_date->format('Y/m/d') }}
                        </p>
                        <p class="text-nowrap d-flex align-items-center">
                            <i class="bx bx-task bx-sm me-2"></i>{{ __('label.status') }}: 
                            <span class="badge  {{ $course->status_badge_class }}">
                                {{ __('label.' . $course->status) }}
                            </span>
                        </p>
                        
                    </div>
                    <div class="me-5">
                         <p class="text-nowrap mb-1">
                            <i class="bx bx-down-arrow-circle bx-sm me-2 text-success"></i>
                            {{ __('label.min_capacity') }}:
                            <strong>{{ $course->min_capacity ?? 0 }}</strong>
                        </p>

                        <p class="text-nowrap mb-1">
                            <i class="bx bx-up-arrow-circle bx-sm me-2 text-danger"></i>
                            {{ __('label.max_capacity') }}:
                            <strong>{{ $course->max_capacity ?? 0 }}</strong>
                        </p>

                        <p class="text-nowrap mb-1">
                            <i class="bx bx-group bx-sm me-2 text-primary"></i>
                            {{ __('label.registered_students') }}:
                            <strong>{{ $course->students()->count() }}</strong>
                        </p>

                        <p class="text-nowrap">
                            <i class="bx bx-chair bx-sm me-2 text-warning"></i>
                            {{ __('label.available_seats') }}:
                            <strong>
                                {{ max(($course->max_capacity ?? 0) - $course->students()->count(), 0) }}
                            </strong>
                        </p>
                    </div>

                   

                </div>
                
                <hr class="my-4">
                <h5>{{__('label.instractor')}}</h5>

                    @foreach($course->teachers as $teacher)
                        <div class="d-flex justify-content-start align-items-center user-name mb-2">
                            <div class="avatar-wrapper">
                                <div class="avatar avatar-sm me-2">
                                    <img 
                                        src="{{ $teacher->photo?->thumbnail_url ?? asset('default.png') }}" 
                                        alt="Avatar" 
                                        class="rounded-circle"
                                    >
                                </div>
                            </div>

                            <div class="d-flex flex-column">
                                {{ $teacher->name }} {{ $teacher->last_name }}
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>

    <div class="card mt-3">
       
        <div class="card-header d-flex justify-content-between align-items-center">

            <h5 class="card-title mb-0">{{ __('label.course_students') }}</h5>

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
                        </li>
                    </ul>
                </div>
                @if(add(Auth::user()->role_ids,$active_menu_id))
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#{{$modalId}}" wire:click="openModal">
                            <i class="bi bi-plus-lg"></i> {{ __('label.add_student_to_course') }} 
                        </button>
                    </div>
                @endif
            </div>

        </div>
        <hr>
        <div class="table-responsive text-nowrap">
 
            <div class="mb-3 px-3">
                <form wire:submit.prevent="applySearch" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('label.student_code') }} / {{ __('label.name') }} </label>
                        <input type="text" class="form-control" placeholder="" wire:model="search.identity">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">{{ __('label.status') }}</label>
                        <select class="form-select" wire:model.defer="search.status" id ="">
                           <option value="">{{ __('label.all') }}</option>
                            <option value="active">{{ __('label.active') }}</option>
                            <option value="completed">{{ __('label.completed') }}</option>
                            <option value="dropped">{{ __('label.dropped') }}</option>
                            <option value="failed">{{ __('label.failed') }}</option>
                            <option value="pending">{{ __('label.pending') }}</option>
                        </select>
                    </div>
                  
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary">
                            {{ __('label.search') }}
                        </button>
                    </div>

                </form>

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
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="no">
                                {{ __('label.NO') }}
                            </th>

                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="student_code">
                                {{ __('label.student_code') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="name">
                                {{ __('label.name') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="last_name">
                                {{ __('label.last_name') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="father_name">
                                {{ __('label.father_name') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="enrolled_at">
                                {{ __('label.enrolled_date') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="status">
                                {{ __('label.status') }}
                            </th>
                            <th>
                                {{ __('label.actions') }}
                            </th>
                        </tr>

                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($students as $i => $student)
                        <tr>
                            <td>{{ ($students->currentPage() - 1) * $students->perPage() + $i + 1 }}</td>
                            <td>{{ $student->student_code }}</td>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->last_name }}</td>
                            <td>{{ $student->father_name }}</td>
                            <td>{{ $student->pivot->enrolled_at }}</td>
                            <td>
                                {{ ucfirst($student->pivot->status) }}
                            </td>

                            <td>
                                <div class="dropdown position-static">
                                    <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if(edit(Auth::user()->role_ids,$active_menu_id))
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="edit({{ $student->pivot->id }})"
                                            ><i class="bx bx-edit-alt me-1 text-success"></i>{{ __('label.edit') }}</a>
                                        @endif
                                        @if(delete(Auth::user()->role_ids,$active_menu_id))
                                            <a class="dropdown-item " href="javascript:void(0);"  onclick="confirmDelete({{ $student->pivot->id }},'{{$table_name}}')"
                                            ><i class="bx bx-trash me-1 text-danger"></i>{{ __('label.delete') }}</a>
                                        @endif
                                    </div>
                                    @if($editMode)

                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 justify-content-end px-3">
                {{ $students->links() }}
            </div>
        </div>
    </div>
    
    
    <div class="modal fade" id="{{$modalId}}" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog modal-lg" branch="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('label.add_student_to_course') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <form @if($editMode) wire:submit.prevent="update" @else wire:submit.prevent="store" @endif>
                    <div class="modal-body">

                        @if(!$editMode)
                            <div class="row" wire:ignore>
                                <div class="col mb-3">
                                    <label for="nameBasic" class="form-label">{{ __('label.student') }} </label>
                                    <select id="student_id" class="form-select select2 @error('student_id') is-invalid @enderror" wire:model.lazy ="student_id">
                                        <option value="">{{ __('label.select') }}</option>
                                    </select>
                                   
                                </div>
                            </div>
                             @error('student_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        @endif

                        @if($editMode)
                        <div class="row">
                            <div class="col mb-3">
                                <label class="form-label">{{ __('label.status') }}</label>
                                <select class="form-select @error('status') is-invalid @enderror" wire:model.lazy="status" id ="" >
                                <option value="">{{ __('label.select') }}</option>
                                    <option value="active">{{ __('label.active') }}</option>
                                    <option value="completed">{{ __('label.completed') }}</option>
                                    <option value="dropped">{{ __('label.dropped') }}</option>
                                    <option value="failed">{{ __('label.failed') }}</option>
                                    <option value="pending">{{ __('label.pending') }}</option>
                                </select>
                                @error('status') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        @endif
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
                url: '/search-students' + menuId,
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

    initStudentSelect();

    Livewire.hook('morphed', () => {
        initStudentSelect();
    });


    $(document).on('shown.bs.modal', function () {
        initStudentSelect();
    });

});

</script>
@endscript
