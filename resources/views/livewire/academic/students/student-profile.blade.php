<div>
    <style>
        /* اطمینان از اینکه منو همیشه سمت چپ باشد */
        .info-container .fee-menu {
            display: flex !important;
            flex-direction: column !important;
            align-items: flex-start !important; /* سمت چپ */
            justify-content: flex-start !important;
            text-align: left !important;
            width: 100%;
        }

        /* آیکن‌ها و متن */
        .info-container .fee-menu .nav-link i {
            min-width: 20px; /* فاصله مناسب آیکن و متن */
        }

        /* رنگ لینک active */
        .info-container .fee-menu .nav-link.active {
            background-color: #39da8a; /* آبی bootstrap */
            color: white !important;
        }

        /* رنگ لینک‌های عادی و hover */
        .info-container .fee-menu .nav-link {
            color: #495057;
        }
        .info-container .fee-menu .nav-link:hover {
            background-color: #e7f1ff;
            color: #39da8a;
        }

        .info-container .fee-menu .nav-link {
            cursor: pointer;
        }
    </style>
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
    @if(App::getLocale()=='en') {{ $active_menu?->name_en }} @else {{ $active_menu?->name }}  @endif / {{ __('label.student_profile') }}
    </h4>
    <!-- end header -->
    <div class="row gy-4">
        <!-- User Sidebar -->
        <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
            <!-- User Card -->
            <div class="card mb-4">
            <div class="card-body ">
                <div class="user-avatar-section">
                    <div class="d-flex align-items-center flex-column">
                        <img class="img-fluid rounded my-4" src="{{ $student->photo?->thumbnail_url ?? asset('default.png') }}" height="110" width="110" alt="User avatar">
                        <div class="user-info text-center">
                        <h5 class="mb-2"> {{ $student->name }} {{ $student->last_name }}</h5>
                        <span class="badge bg-label-secondary">{{ $student->student_code }}</span>
                        </div>
                    </div>
                </div>
                @if(edit(Auth::user()->role_ids,$active_menu_id))
                 <div class="d-flex gap-2 mt-3">
                    <a class="btn btn-primary "
                    href="{{ route('student-financial-profile', [
                            'menu_id'   => $this->active_menu_id,
                            'student_id' => encrypt($student->id),
                            'slug' => 'student_fees',
                        ]) }}">
                        <span class="small">{{ __('label.financial_profile') }}</span>
                    </a>
                    <a class="btn btn-info"
                        href="{{ route('special-course-list', [
                                'menu_id'   => $this->active_menu_id,
                                'student_id' => $student->id,
                            ]) }}">
                        <span class="small">{{ __('label.add_to_course') }}</span>
                    </a>
                 </div>
                @endif
                <h5 class="pb-2 border-bottom mb-4"></h5>
                <div class="info-container">
                    <ul class="nav nav-pills flex-column mb-3 fee-menu">
                        <li class="nav-item mb-1">
                            <a class="nav-link {{ $activeTab == 'student_courses' ? 'active' : '' }}" wire:click="changeTab('student_courses')">
                                <i class="bx bx-book me-2 text-primary"></i> {{ __('label.student_courses') }}
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a class="nav-link {{ $activeTab == 'result_sheet' ? 'active' : '' }}" wire:click="changeTab('result_sheet')">
                                <i class="bx bx-book me-2 text-info"></i> {{ __('label.result_sheet') }}
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a class="nav-link {{ $activeTab == 'student_attendance' ? 'active' : '' }}" wire:click="changeTab('student_attendance')">
                                <i class="bx bx-id-card me-2 text-danger"></i> {{ __('label.student_attendance') }}
                            </a>
                        </li>

                    </ul>
                </div>
            </div>
            </div>
            <!-- /User Card -->
        </div>
        <!--/ User Sidebar -->

        <!-- User Content -->
        <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">

            <!-- Dynamic Content -->
            <div class="card">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">

                    <div wire:loading wire:target="changeTab" class="text-center my-5">
                        <div class="spinner-border" role="status"></div>
                        <p class="mt-2 mb-0"> {{ __('label.loading') }}</p>
                    </div>

                    <div wire:loading.remove wire:target="changeTab" class="w-100">

                        <!-- Course Fee Tab -->
                        <div style="{{ $activeTab == 'student_courses' ? '' : 'display:none' }}">
                           @livewire(
                                'academic.students.student-courses.student-course-list', 
                                [
                                    'active_menu_id' => $active_menu_id,
                                    'student_id' => $student->id
                                ],
                                key('student_course_list_'.$student->id)
                            )
                        </div>

                        <div style="{{ $activeTab == 'result_sheet' ? '' : 'display:none' }}">
                            @livewire(
                                'academic.students.student-courses.student-course-result-list', 
                                [
                                    'active_menu_id' => $active_menu_id,
                                    'student_id' => $student->id
                                ],
                                key('result_sheet_'.$student->id)
                            )
                        </div>

                        <!-- Book Fee Tab -->
                        <div style="{{ $activeTab == 'student_attendance' ? '' : 'display:none' }}">
                           @livewire(
                                'academic.students.student-courses.student-course-attendance-list', 
                                [
                                    'active_menu_id' => $active_menu_id,
                                    'student_id' => $student->id
                                ],
                                key('student_attendance_'.$student->id)
                            )
                        </div>


                    </div>

                </div>
            </div>

        </div>
        <!--/ User Content -->
    </div>
    </div>
</div>

