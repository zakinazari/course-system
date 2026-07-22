
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
                <a class="btn btn-secondary d-flex align-items-center gap-2"
                href="#"
                    wire:click.prevent="print">
                    <i class="fa fa-print"></i>
                    {{ __('label.print') }}
                </a>

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
                    <div class="col-md-3 col-md-2 d-flex flex-column">
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

                
                    <div class="col-md-4 d-flex flex-column">
                        <label class="form-label">{{ __('label.course') }}</label>
                        <select class="form-select select2" id="search_course_id">
                           <option value="">{{ __('label.all') }}</option>
                           @foreach($courses as $course)
                                 <option value="{{ $course->id }}"  wire:key="course-search-{{ $course->id }}">
                                    {{ $course->name }}
                                 </option>
                           @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">{{ __('label.name') }} / {{ __('label.student_code') }} </label>
                        <input type="text" class="form-control" placeholder="" wire:model="student_code">
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
            <hr>

         @if(count($students) > 0)

            <div class="container-fluid px-3 px-md-5">

                <div class="d-flex gap-2 mb-3 print-hide">

                    <button class="btn btn-success btn-sm"
                            wire:click="selectAll">
                        Select All
                    </button>

                    <button class="btn btn-danger btn-sm"
                            wire:click="clearSelection">
                        Clear
                    </button>

                    <button class="btn btn-secondary btn-sm"
                            wire:click.prevent="print">
                        Print Selected
                    </button>

                </div>

                <div class="row print-hide mb-3">

                    @foreach($students as $cs)

                        <div class="col-md-6 mb-2">

                            <div class="border rounded p-2 d-flex justify-content-between align-items-center">

                                <!-- Checkbox -->
                                <div class="form-check" wire:key="student-check-{{ $cs->student_id }}">
                                    <input class="form-check-input"
                                        type="checkbox"
                                        wire:model.lazy="selected_students"
                                        value="{{ $cs->student_id }}">
                                </div>

                                <!-- Info -->
                                <div class="flex-grow-1 ms-2">

                                    <div class="fw-bold">
                                        {{ $cs->student?->name }} {{ $cs->student?->last_name }}
                                    </div>

                                    <small class="text-muted d-block">
                                        {{ $cs->student?->student_code }} |
                                        {{ $cs->course?->book?->name }} |
                                        Father: {{ $cs->student?->father_name ?? '-' }}
                                    </small>

                                </div>

                                <!-- Score -->
                                <div class="text-end">
                                    <span class="badge bg-primary">
                                        {{ $this->student_results[$cs->student_id]['total'] ?? 0 }}
                                    </span>
                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>

            @endif

        </div>
    </div>
    

    <!-- print area  -->
            <style>

                #printArea {
                    display: none;
                }

                .print-page {
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                .print-page {
                    position: relative;
                    width: 297mm;
                    height: 210mm;
                    margin: 0 auto;
                    padding: 25px;
                    box-sizing: border-box;

                    background-image: url('{{ asset("assets/images/certificates/" . ($course?->branch?->code ?? 'default') . ".jpg") }}');
                    background-size: 100% 100%;
                    background-repeat: no-repeat;
                    background-position: center;
                }

            @media print {

             #printArea {
                display: block;
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

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
           @if(!empty($students))
            <div id="printArea" dir="ltr">

                @foreach($students as $cs)

                    @if(in_array($cs->student_id, $selected_students))

                        <div class="print-page">

                            <div class="student-id">
                                {{ $cs->student?->student_code }}
                            </div>

                            <div class="score">
                                {{ $this->student_results[$cs->student_id]['total'] ?? null }}
                            </div>

                            <div class="book">
                                {{ $cs->course?->book?->name }}
                            </div>

                            <div class="grade">
                                {{ $this->student_results[$cs->student_id]['grade'] ?? null }}
                            </div>

                            <div class="start_date">
                                {{ $cs->course?->start_date?->format('Y/m/d') }}
                            </div>

                            <div class="end_date">
                                {{ $cs->course?->end_date?->format('Y/m/d') }}
                            </div>

                            <div class="student_name">
                                @if($cs->student?->gender_id ==1) Mr. @elseif($cs->student?->gender_id ==2) Miss. @endif
                                {{ $cs->student?->name }} {{ $cs->student?->last_name }}
                            </div>

                            <div class="main_text">
                                <p>
                                    @if($cs->student?->gender_id ==1)
                                        Son of
                                    @elseif($cs->student?->gender_id ==2)
                                        Daughter of
                                    @endif

                                    {{ $cs->student?->father_name }}
                                    for the successful completion of the aforementioned course book.
                                </p>

                                <p>
                                    This result card is issued as evidence for the next step toward further achievements.
                                </p>
                            </div>

                        </div>

                    @endif

                @endforeach

            </div>
           @endif
    

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
         $('#search_course_id').off('change').on('change', function () {
            $wire.set('course_id', $(this).val());
        });
    }

    initSelect2();

    Livewire.hook('morphed', () => {
        initSelect2();
    });

    $(document).on('shown.bs.modal', function () {
        initSelect2();
    });

    Livewire.on('reset-select2', () => {
        $('#search_course_id').val(null).trigger('change');        
    });

});
</script>
@endscript

<script>
window.addEventListener('show-print-preview', () => {

    const el = document.getElementById('printArea');

    // show
    el.style.display = 'block';

    void el.offsetHeight;

    setTimeout(() => {

        printDiv('printArea');

        // بعد از باز شدن print dialog دوباره hide کن
        setTimeout(() => {
            el.style.display = 'none';
        }, 500);

    }, 300);

});
</script>

