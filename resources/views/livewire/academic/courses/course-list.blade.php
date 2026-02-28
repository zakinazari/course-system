
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
                        <label class="form-label">{{ __('label.name') }}</label>
                        <input type="text" class="form-control" placeholder="" wire:model="search.name">
                    </div>
                    @if(!auth()->user()->branch_id)
                    <div class="col-md-3">
                        <label class="form-label">{{ __('label.branch') }}</label>
                        <select class="form-select" wire:model="search.branch_id" 
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
                    <div class="col-md-3 col-md-2 d-flex flex-column" >
                        <label class="form-label">{{ __('label.teacher') }}</label>
                        <select class="form-select select2" wire:model.defer="search.teacher_id" id ="search_teacher_id">
                           <option value="">{{ __('label.all') }}</option>
                           @foreach($teachers as $teacher)
                                 <option value="{{ $teacher->id }}"  wire:key="teacher-search-{{ $teacher->id }}">
                                    {{ $teacher->name }}
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
                            <option value="draft">{{ __('label.draft') }}</option>
                            <option value="scheduled">{{ __('label.scheduled') }}</option>
                            <option value="ongoing">{{ __('label.ongoing') }}</option>
                            <option value="completed">{{ __('label.completed') }}</option>
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
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="name">
                                {{ __('label.name') }}
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
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="start_time">
                                {{ __('label.start_time') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="end_time">
                                {{ __('label.end_time') }}
                            </th>

                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="min_capacity">
                                {{ __('label.min_capacity') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="max_capacity">
                                {{ __('label.max_capacity') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="total_teaching_days">
                                {{ __('label.total_teaching_days') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="start_date">
                                {{ __('label.start_date') }}
                            </th>

                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="end_date">
                                {{ __('label.end_date') }}
                            </th>

                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="mid_exam_date">
                                {{ __('label.mid_exam_date') }}
                            </th>

                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="final_exam_date">
                                {{ __('label.final_exam_date') }}
                            </th>

                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="classroom_id">
                                {{ __('label.classroom') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="teacher_ids">
                                {{ __('label.teacher') }}
                            </th>

                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="status">
                                {{ __('label.status') }}
                            </th>

                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="course_type_id">
                                {{ __('label.course_type') }}
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
                        @foreach($courses as $i => $course)
                        <tr>
                            <td>{{ ($courses->currentPage() - 1) * $courses->perPage() + $i + 1 }}</td>
                            <td>{{ $course->name }}</td>
                            <td>{{ $course->program?->name }}</td>
                            <td>{{ $course->book?->name }}</td>
                            <td>{{ $course->shift?->name }}</td>
                            <td>{{ $course->start_time->format('h:i A') }}</td>
                            <td>{{ $course->end_time->format('h:i A') }}</td>
                            <td>{{ $course->min_capacity }}</td>
                            <td>{{ $course->max_capacity }}</td>
                            <td>{{ $course->total_teaching_days }}</td>
                            <td>{{ $course->start_date->format('Y/m/d') }}</td>
                            <td>{{ $course->end_date->format('Y/m/d') }}</td>
                            <td>{{ $course->mid_exam_date->format('Y/m/d') }}</td>
                            <td>{{ $course->final_exam_date->format('Y/m/d') }}</td>
                            <td>{{ $course->classroom?->name }}</td>
                            <td>
                                {{ $course->teachers
                                    ->map(fn($teacher) => $teacher->name . ' ' . $teacher->last_name)
                                    ->join(', ')
                                }}
                            </td>
                            <td>
                                <span class="badge rounded-pill {{ $course->status_badge_class }}">
                                    {{ __('label.' . $course->status) }}
                                </span>
                            </td>
                            <td>{{ $course->courseType?->name }}</td>
                            @if(!auth()->user()->branch_id)
                            <td>{{ $course->branch?->name }}</td>
                            @endif
                            <td>
                                @if($course->status=='draft' || $course->status=='scheduled' )
                                <div class="dropdown position-static">
                                    <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if(edit(Auth::user()->role_ids,$active_menu_id))
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="edit({{ $course->id }})"
                                            ><i class="bx bx-edit-alt me-1 text-success"></i>{{ __('label.edit') }}</a>
                                        @endif
                                        @if(delete(Auth::user()->role_ids,$active_menu_id))
                                            <a class="dropdown-item " href="javascript:void(0);"  onclick="confirmDelete({{ $course->id }},'{{$table_name}}')"
                                            ><i class="bx bx-trash me-1 text-danger"></i>{{ __('label.delete') }}</a>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 justify-content-end px-3">
                {{ $courses->links() }}
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
                            @if(!auth()->user()->branch_id)
                            <div class="col mb-3">
                              <label class="form-label">{{ __('label.branch') }} <span style="color:red;">*</span></label>
                              <select class="form-select @error('branch_id') is-invalid @enderror"
                               wire:model.lazy="branch_id" id ="branch_id"
                               wire:change="loadClassroomAndTeacher($event.target.value)">
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
                                <select class="form-select @error('course_type_id') is-invalid @enderror" wire:model.lazy="course_type_id" id ="course_type_id">
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
                                <label for="nameBasic" class="form-label">{{ __('label.min_capacity') }} <span style="color:red;">*</span></label>
                                <input type="number" min="1" id="nameBasic" class="form-control @error('min_capacity') is-invalid @enderror" wire:model.lazy="min_capacity">
                                @error('min_capacity') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.max_capacity') }} <span style="color:red;">*</span></label>
                                <input type="number" min="1" id="nameBasic" class="form-control @error('max_capacity') is-invalid @enderror" wire:model.lazy="max_capacity">
                                @error('max_capacity') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.total_teaching_days') }} <span style="color:red;">*</span></label>
                                <input type="number" min="1" id="nameBasic" class="form-control @error('total_teaching_days') is-invalid @enderror" wire:model.lazy="total_teaching_days">
                                @error('total_teaching_days') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
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
                        
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.start_time') }} <span style="color:red;">*</span></label>
                                <input type="time" id="nameBasic" class="form-control @error('start_time') is-invalid @enderror" wire:model.lazy="start_time">
                                @error('start_time') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.end_time') }} <span style="color:red;">*</span></label>
                                <input type="time" id="nameBasic" class="form-control @error('end_time') is-invalid @enderror" wire:model.lazy="end_time">
                                @error('end_time') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.start_date') }} <span style="color:red;">*</span></label>
                                <input type="date" id="nameBasic" class="form-control @error('start_date') is-invalid @enderror" wire:model.lazy="start_date">
                                @error('start_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.end_date') }} <span style="color:red;">*</span></label>
                                <input type="date" id="nameBasic" class="form-control @error('end_date') is-invalid @enderror" wire:model.lazy="end_date">
                                @error('end_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.mid_exam_date') }} <span style="color:red;">*</span></label>
                                <input type="date" id="nameBasic" class="form-control @error('mid_exam_date') is-invalid @enderror" wire:model.lazy="mid_exam_date">
                                @error('mid_exam_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.final_exam_date') }} <span style="color:red;">*</span></label>
                                <input type="date" id="nameBasic" class="form-control @error('final_exam_date') is-invalid @enderror" wire:model.lazy="final_exam_date">
                                @error('final_exam_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3" wire:key="teacher-select-{{ $branch_id }}" wire:ignore.self>
                              <label class="form-label">{{ __('label.teacher') }} <span style="color:red;">*</span></label>
                              <select class="form-select select2 @error('teacher_ids') is-invalid @enderror" multiple wire:model.lazy="teacher_ids" id ="teacher_ids">
                                 <option value="" disabled>{{ __('label.select') }}</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}"  wire:key="teacher-{{ $teacher->id }}">
                                            {{ $teacher->name }} {{ $teacher->last_name }}
                                        </option>
                                    @endforeach
                              </select>
                                @error('teacher_ids') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                           </div>
                           <div class="col mb-3" wire:key="classroom-select-{{ $branch_id }}">
                              <label class="form-label">{{ __('label.classroom') }} <span style="color:red;">*</span></label>
                              <select class="form-select @error('classroom_id') is-invalid @enderror" wire:model.lazy="classroom_id" id ="classroom_id">
                                 <option value="">{{ __('label.select') }}</option>
                                    @foreach($classrooms as $classroom)
                                        <option value="{{ $classroom->id }}"  wire:key="classroom-{{ $classroom->id }}">
                                            {{ $classroom->name }}
                                        </option>
                                    @endforeach
                              </select>
                                @error('classroom_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                           </div>
                        </div>
                         <div class="row">
                            <div class="col mb-3">
                                @if($existing_image)
                                    <div class="mb-2 text-center">
                                        <img src="{{ asset('storage/' . $existing_image) }}" 
                                            alt="Cover Image" class="rounded" width="120" height="120">
                                    </div>
                                @endif
                                <label for="formFile" class="form-label">{{ __('label.image') }}</label>
                                <input class="form-control @error('image') is-invalid @enderror" 
                                    type="file" id="formFile" wire:model.lazy="image">
                                @error('image') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
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
            $wire.call('loadClassroomAndTeacher', $(this).val());
        });

        $('#search_book_id').off('change').on('change', function () {
            $wire.set('search.book_id', $(this).val());
        });
        $('#search_teacher_id').off('change').on('change', function () {
            $wire.set('search.teacher_id', $(this).val());
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

