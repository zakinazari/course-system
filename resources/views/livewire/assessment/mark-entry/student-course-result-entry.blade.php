
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

                            <a class="dropdown-item d-flex align-items-center gap-2" href="#" wire:click.prevent="exportExcel">
                                <i class="fa fa-file-excel text-success"></i> {{ __('label.export_to_excel') }}
                            </a>

                        </li>
                    </ul>
                </div>

            </div>

        </div>
        <hr>
        <div class="table-responsive text-nowrap">
 
            <div class="mb-3 px-3">
                <form wire:submit.prevent="applySearch" class="row g-3 align-items-end">
                    @if(!auth()->user()->branch_id)
                    <div class="col-md-3">
                        <label class="form-label">{{ __('label.branch') }}</label>
                        <select class="form-select" wire:model.lazy="search.branch_id" 
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
                        <select class="form-select select2" wire:model.lazy="search.program_id" id ="search_program_id">
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
                        <select wire:ignore.self class="form-select select2" wire:model.lazy="search.book_id" id ="search_book_id">
                           <option value="">{{ __('label.all') }}</option>
                           @foreach($books as $book)
                                 <option value="{{ $book->id }}"  wire:key="book-search-{{ $book->id }}">
                                    {{ $book->name }}
                                 </option>
                           @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-md-2 d-flex flex-column" wire:ignore>
                        <label class="form-label">{{ __('label.teacher') }}</label>
                        <select class="form-select select2" wire:model.lazy="search.teacher_id" id ="search_teacher_id">
                           <option value="">{{ __('label.all') }}</option>
                           @foreach($teachers as $teacher)
                                 <option value="{{ $teacher->id }}"  wire:key="teacher-search-{{ $teacher->id }}">
                                    {{ $teacher->name }} {{ $teacher->last_name }}
                                 </option>
                           @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('label.course_type') }}</label>
                        <select class="form-select" wire:model.lazy="search.course_type_id">
                           <option value="">{{ __('label.all') }}</option>
                           @foreach($course_types as $type)
                                 <option value="{{ $type->id }}"  wire:key="type-search-{{ $type->id }}">
                                    {{ $type->name }}
                                 </option>
                           @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('label.shift') }}</label>
                        <select class="form-select" wire:model.lazy="search.shift_id">
                           <option value="">{{ __('label.all') }}</option>
                           @foreach($shifts as $shift)
                                 <option value="{{ $shift->id }}"  wire:key="shift-search-{{ $shift->id }}">
                                    {{ $shift->name }}
                                 </option>
                           @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">{{ __('label.course') }}</label>
                        <select class="form-select " wire:model="search.course_id">
                           <option value="">{{ __('label.all') }}</option>
                           @foreach($courses as $course)
                                 <option value="{{ $course->id }}"  wire:key="course-search-{{ $course->id }}">
                                    {{ $course->name }}
                                 </option>
                           @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('label.status') }}</label>
                        <select class="form-select" wire:model="search.status">
                           <option value="">{{ __('label.all') }}</option>
                              <option value="excellent">{{ __('label.excellent_student') }}</option>
                              <option value="accepted"> {{ __('label.accepted_student') }}</option>
                              <option value="week"> {{ __('label.week_student') }}</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary">
                            {{ __('label.search') }}
                        </button>
                    </div>
                    @error('search.attendance_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </form>
            </div>
            <br>
            @if(!empty($students))
            <div class="table-responsive text-nowrap">
                <table class="table table-sm align-middle mb-0" >
                    <thead class="table-dark table-border-bottom-0 small-table">
                        <tr>
                            <th style="width:15px;">
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
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="father_name">
                                {{ __('label.father_name') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="status">
                              {{ __('label.cognitive_score') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="status">
                               {{ __('label.attendance_score') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="status">
                                 {{ __('label.midterm_score') }}
                            </th>
                            
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="status">
                                 {{ __('label.final_score') }}
                            </th>
                           
                            <th>
                                
                               {{ __('label.total_score') }}
                            </th>

                        </tr>

                    </thead>
                    
                    <tbody class="table-border-bottom-0">
                        @foreach($students as $i => $cs)
                        <tr wire:key="course-{{ $search['course_id'] }}-student-{{ $cs->student_id }}">
                            <td >{{ $i + 1 }}</td>
                            <td>{{ $cs->student?->student_code }}</td>
                            <td>{{ $cs->student?->name }}</td>
                            <td>{{ $cs->student?->father_name }}</td>
                            <td>
                              <input class="form-control " min="0" max="20" step="0.1" type="text" wire:model.live="results.{{ $cs->student_id }}.cognitive"  wire:key="cognitive-{{ $cs->student_id }}">
                           </td>
                           <td>
                              <input class="form-control " min="0" max="20" step="0.1"  type="text" wire:model.live="results.{{ $cs->student_id }}.attendance"  wire:key="attendance-{{ $cs->student_id }}">
                           </td>
                          
                           <td>
                              <input class="form-control " min="0" max="30" step="0.1" type="text" wire:model.live="results.{{ $cs->student_id }}.midterm"  wire:key="midterm-{{ $cs->student_id }}">
                           </td>
                           <td>
                              <input class="form-control " min="0" max="30" step="0.1" type="text" wire:model.live="results.{{ $cs->student_id }}.final"  wire:key="final-{{ $cs->student_id }}">
                           </td>
                          <td  wire:key="total-{{ $cs->student_id }}">
                               {{
                                 number_format(
                                    min(
                                       100,
                                       (float)($results[$cs->student_id]['attendance'] ?? 0) +
                                       (float)($results[$cs->student_id]['cognitive'] ?? 0) +
                                       (float)($results[$cs->student_id]['midterm'] ?? 0) +
                                       (float)($results[$cs->student_id]['final'] ?? 0)
                                    ),
                                    1
                                 )
                              }}
                           </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                 @if(add(Auth::user()->role_ids,$active_menu_id) && !empty($students))
                <div class="d-flex justify-content-end mt-4 mb-3 px-3">
                    <button type="button" class="btn btn-primary" wire:click="saveMarks">
                        <i class="bi bi-save me-1"></i> {{ __('label.save') }}
                    </button>
                </div>
                @endif
            </div>
            @endif
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

