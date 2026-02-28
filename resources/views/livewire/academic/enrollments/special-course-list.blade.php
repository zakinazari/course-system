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
    @if(App::getLocale()=='en') {{ $active_menu?->name_en }} @else {{ $active_menu?->name }}  @endif / {{ __('label.courses') }}
    </h4>
    <!-- end header -->

    <div class="app-academy">
        <div class="card p-0 mb-4 ">
          <div class="mb-3 px-3 mt-3">
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
            </div>
        </div>

        <div class="card mb-4">
          <div class="card-header d-flex flex-column flex-md-row justify-content-between gap-3">
            <div class="card-title mb-0 me-1">
                <div class="d-flex align-items-center">
                <h5 class="card-title mb-0">
                    @if(App::getLocale()=='en') 
                        {{ $active_menu?->name_en }} 
                    @else 
                        {{ $active_menu?->name }}  
                    @endif 

                    / 👤 {{ $student->name }} {{ $student->last_name }} ( {{ $student->student_code }} )
                </h5>
                </div>
            </div>
            <div class="d-flex align-items-center gap-1 mt-3 mt-md-0 justify-content-end flex-wrap">
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

            <div class="card-body">
                @if($courses->isEmpty())
                <div class="container mt-5">
                    <div class="alert alert-warning d-flex flex-column align-items-center justify-content-center text-center py-5">
                        
                        <i class="bi bi-exclamation-triangle-fill fs-1 mb-3"></i>

                       
                        <h4 class="mb-2">{{ __('label.no_course_found') }}</h4>
                        <p class="mb-3 text-secondary">
                            {{ __('label.no_course_found_message') ?? 'Currently there are no courses available for this student.' }}
                        </p>

                        
                        @if(!empty($placement_test_id))
                            <button type="button" 
                                    class="btn btn-success d-flex align-items-center gap-2"
                                    data-bs-toggle="modal"  
                                    data-bs-target="#add_to_waiting_id"
                                    wire:click="setPlacementTestInfo">
                                <i class="bi bi-plus-lg"></i> {{ __('label.add_to_waiting_list') }}
                            </button>
                        @endif
                    </div>
                </div>
                @else
                  
                <div class="row gy-4 mb-4">
                    @foreach($courses as $i => $course)
                    <div class="col-sm-6 col-lg-4 ">
                        <div class="card p-2 h-100">
                            <div class="rounded-2 text-center mb-3">
                                <a href="{{ route('course-enrollments',['menu_id' =>$active_menu_id,'course_id'=>$course->id,'student_id'=>$student_id]) }}">

                                    <div class="rounded-2 overflow-hidden"
                                        style="height:220px; background-color:#f8f9fa;">
                                        @if($course->image)
                                            <img 
                                                class="w-100 h-100 object-fit-cover"
                                                src="{{ asset('storage/' . $course->image) }}"
                                                alt="course image">
                                        @else
                                            <div class="w-100 h-100 bg-white"></div>
                                        @endif
                                    </div>
                                </a>
                            </div>

                            <div class="card-body p-3 pt-2">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-label-primary">{{ $course->program?->name }}</span>
                                <h6 class="d-flex align-items-center justify-content-center gap-1 mb-0">
                                4.4 <span class="text-warning"><i class="bx bxs-star me-1"></i></span><span class="text-muted">(1.23k)</span>
                                </h6>
                            </div>
                            <a href="{{ route('course-enrollments',['menu_id' =>$active_menu_id,'course_id'=>$course->id,'student_id'=>$student_id]) }}" class="h5">{{ $course->book?->name }}</a>
                            <p class="mt-2">{{ $course->name }}</p>
                            <p class="d-flex align-items-center"><i class="bx bx-time-five me-2"></i>{{ $course->start_time->format('h:i A') }} - {{ $course->end_time->format('h:i A') }}</p>
                            <p class="d-flex align-items-center"><i class="bx bx bx-task bx-sm me-2"></i>
                            <span class="badge  {{ $course->status_badge_class }}">
                                    {{ __('label.' . $course->status) }}
                            </span></p>
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
                            <div class="d-flex flex-column flex-md-row gap-2 text-nowrap pe-xl-3 pe-xxl-0">
                                @if(add(Auth::user()->role_ids,$active_menu_id))
                                <button 
                                    type="button" 
                                    class="btn btn-success"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#{{$modalId}}" 
                                    wire:click="setIds({{ $course->id }}, {{ $student_id }})">

                                    <i class="bx bx-plus"></i>
                                    <span class="small"> {{ __('label.add_student_to_course') }} </span>
                                </button>
                                 @endif
                              <a class="app-academy-md-50 btn btn-label-primary d-flex align-items-center" href="{{ route('course-enrollments',['menu_id' =>$active_menu_id,'course_id'=>$course->id,'student_id'=>$student_id]) }}">
                                <span class="me-2">{{ __('label.details') }}</span><i class="bx bx-chevron-right lh-1 scaleX-n1-rtl"></i>
                              </a>
                            </div>
                            </div>
                        </div>
                    </div>
                    @endforeach

                </div>
                <div class="mt-4 justify-content-end px-3">
                    {{ $courses->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="{{$modalId}}" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog modal-sm" branch="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('label.add_student_to_course') }} </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <form  wire:submit.prevent="store">
                    <div class="modal-body">

                        <div class="col mb-3">
                            
                            <div class="alert alert-solid-danger" role="alert">
                            {{ __('label.are_you_sure') }}
                        </div>
                    
                            @error('student_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" >{{ __('label.no') }}</button>
                        <button type="submit" class="btn btn-success"> {{ __('label.yes') }} </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add_to_waiting_id" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog modal-sm" branch="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add To Waiting List </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <form  wire:submit.prevent="storeToWaitingList">
                    <div class="modal-body">

                        <div class="col mb-3">
                            
                            <div class="alert alert-solid-danger" role="alert">
                            {{ __('label.are_you_sure') }}
                        </div>
                    
                            @error('student_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            @error('branch_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            @error('program_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            @error('book_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            @error('shift_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" >{{ __('label.no') }}</button>
                        <button type="submit" class="btn btn-success"> {{ __('label.yes') }} </button>
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

        $('#search_program_id').off('change').on('change', function () {
            $wire.set('search.program_id', $(this).val());
            $wire.call('loadProgramBook', $(this).val());
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