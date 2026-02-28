
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
                        </li>
                    </ul>
                </div>
                @if(add(Auth::user()->role_ids,$active_menu_id))
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#{{$modalId}}" wire:click="openModal">
                            <i class="bi bi-plus-lg"></i> {{ __('label.add_new_record') }} 
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
                        <label class="form-label">{{ __('label.course_type') }}</label>
                        <select class="form-select" wire:model="search.course_type_id">
                           <option value="">{{ __('label.all') }}</option>
                           @foreach($course_types as $type)
                                 <option value="{{ $type->id }}"  wire:key="type-search-{{ $type->id }}">
                                    {{ $type->name }}
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
                            <option value="waiting">{{ __('label.waiting') }}</option>
                            <option value="enrolled">{{ __('label.enrolled') }}</option>
                            <option value="cancelled">{{ __('label.cancelled') }}</option>
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
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="phone_no">
                                {{ __('label.phone_no') }}
                            </th>

                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="program_id">
                                {{ __('label.program') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="book_id">
                                {{ __('label.book') }}
                            </th>

                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="shift_id">
                                {{ __('label.shift') }}
                            </th>
                         
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="status">
                                {{ __('label.status') }}
                            </th>

                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="course_type_id">
                                {{ __('label.course_type') }}
                            </th>
                            <th>
                                {{ __('label.enrollment') }}
                            </th>
                            @if(!auth()->user()->branch_id)
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="branch_id">
                                {{ __('label.branch') }}
                            </th>
                            @endif
                            <th>
                                {{ __('label.actions') }}
                            </th>
                        </tr>

                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($waiting_students as $i => $waiting)
                        <tr>
                            <td>{{ ($waiting_students->currentPage() - 1) * $waiting_students->perPage() + $i + 1 }}</td>
                            <td>{{ $waiting->student?->student_code }}</td>
                            <td>{{ $waiting->student?->name }}</td>
                            <td>{{ $waiting->student?->last_name }}</td>
                            <td>{{ $waiting->student?->father_name }}</td>
                            <td>{{ $waiting->student?->phone_no }}</td>
                            <td>{{ $waiting->program?->name }}</td>
                            <td>{{ $waiting->book?->name }}</td>
                            <td>{{ $waiting->shift?->name }}</td>

                            <td>
                                <span class="badge rounded-pill {{ $waiting->status_badge_class }}">
                                    {{ __('label.' . $waiting->status) }}
                                </span>
                            </td>

                            <td>{{ $waiting->courseType?->name }}</td>
                            <td>
                                <a class="btn btn-success"
                                    href="{{ route('special-course-list', [
                                            'menu_id'   => $this->active_menu_id,
                                            'student_id'   => $waiting->student_id,
                                            'program_id'=> $waiting->program_id,
                                            'book_id'   => $waiting->book_id,
                                            'shift_id'   => $waiting->shift_id,
                                            'branch_id' => $waiting->branch_id,
                                            'wl_id' => encrypt($waiting->id),
                                        ]) }}">
                                    {{ __('label.add_to_course') }}
                                </a>
                            </td>
                            @if(!auth()->user()->branch_id)
                            <td>{{ $waiting->branch?->name }}</td>
                            @endif
                            <td>

                                <div class="dropdown position-static">
                                    <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if(edit(Auth::user()->role_ids,$active_menu_id))
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="edit({{ $waiting->id }})"
                                            ><i class="bx bx-edit-alt me-1 text-success"></i>{{ __('label.edit') }}</a>
                                        @endif
                                        @if(delete(Auth::user()->role_ids,$active_menu_id))
                                            <a class="dropdown-item " href="javascript:void(0);"  onclick="confirmDelete({{ $waiting->id }},'{{$table_name}}')"
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
                {{ $waiting_students->links() }}
            </div>
        </div>
    </div>
    
    
    <div class="modal fade" id="{{$modalId}}" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog modal-lg" branch="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@if($editMode) {{ __('label.editing') }}  @else {{ __('label.adding') }} @endif @if(App::getLocale() =='en') {{ $active_menu?->name_en }} @elseif(App::getLocale()=='fa') {{ $active_menu?->name }} @endif</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <form @if($editMode) wire:submit.prevent="update" @else wire:submit.prevent="store" @endif>
                    <div class="modal-body">

                        <div class="row">
                            @if(!$editMode)
                             <div class="row" wire:ignore>
                                <div class="col mb-3">
                                    <label for="nameBasic" class="form-label">{{ __('label.student') }} </label>
                                    <select id="student_id" class="form-select select2 @error('student_id') is-invalid @enderror" wire:model.lazy ="student_id">
                                        <option value="">{{ __('label.select') }}</option>
                                    </select>
                                   
                                </div>
                            </div>
                            @endif
                            @error('student_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                            @if(!auth()->user()->branch_id)
                            <div class="col mb-3">
                              <label class="form-label">{{ __('label.branch') }} <span style="color:red;">*</span></label>
                              <select class="form-select @error('branch_id') is-invalid @enderror"
                               wire:model.lazy="branch_id" id ="branch_id">
                                 <option value="">{{ __('label.select') }}</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}"  wire:key="branch-{{ $branch->id }}">
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                              </select>
                                @error('branch_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                           </div>
                           @endif
                          <div class="col mb-3">
                                <label for="course_type_id" class="form-label">{{ __('label.course_type') }} <span style="color:red;">*</span></label>
                                <select class="form-select @error('course_type_id') is-invalid @enderror" wire:model.lazy="course_type_id" id ="waiting_type_id">
                                 <option value="">{{ __('label.select') }}</option>
                                  @foreach($course_types as $type)
                                        <option value="{{ $type->id }}"  wire:key="type-{{ $type->id }}">
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('course_type_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                              <label class="form-label">{{ __('label.program') }} <span style="color:red;">*</span></label>
                              <select class="form-select select2 @error('program_id') is-invalid @enderror"
                               wire:model.lazy="program_id" id ="program_id"
                                wire:change="loadProgramBook($event.target.value)">
                                 <option value="">{{ __('label.select') }}</option>
                                    @foreach($programs as $program)
                                        <option value="{{ $program->id }}"  wire:key="program-{{ $program->id }}">
                                            {{ $program->name }}
                                        </option>
                                    @endforeach
                              </select>
                                @error('program_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                           </div>
                            <div class="col mb-3" wire:key="book-select-{{ $program_id }}">
                                <label for="book_id" class="form-label">{{ __('label.book') }} <span style="color:red;">*</span></label>
                                <select class="form-select select2 @error('book_id') is-invalid @enderror" wire:model.lazy="book_id" id ="book_id">
                                 <option value="">{{ __('label.select') }}</option>
                                  @foreach($books as $book)
                                        <option value="{{ $book->id }}"  wire:key="book-{{ $book->id }}">
                                            {{ $book->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('book_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">

                            <div class="col mb-3">
                              <label class="form-label">{{ __('label.shift') }} <span style="color:red;">*</span></label>
                              <select class="form-select @error('shift_id') is-invalid @enderror" wire:model.lazy="shift_id" id ="shift_id">
                                 <option value="">{{ __('label.select') }}</option>
                                    @foreach($shifts as $shift)
                                        <option value="{{ $shift->id }}"  wire:key="shift-{{ $shift->id }}">
                                            {{ $shift->name }}
                                        </option>
                                    @endforeach
                              </select>
                                @error('shift_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
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


