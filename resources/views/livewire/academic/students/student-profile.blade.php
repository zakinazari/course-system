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
             
                <div class="d-flex flex-column align-items-center">

                    <!-- Avatar -->
                    <img class="img-fluid rounded-circle shadow border border-3 border-white mb-3"
                        src="{{ $student->photo?->thumbnail_url ?? asset('default.png') }}"
                        width="110"
                        height="110"
                        alt="Student Avatar">

                    <!-- Information Card -->
                    <div class="w-100">

                     <!-- Student ID -->
                        <div class="d-flex justify-content-between align-items-center border rounded px-3 py-2 mb-2 bg-light">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-id-card text-info fs-4 me-2"></i>
                                <span class="text-muted">{{ __('label.student_code') }}</span>
                            </div>

                            <span class="badge bg-label-primary">
                                {{ $student->student_code }}
                            </span>
                        </div>

                        <!-- Student Name -->
                        <div class="d-flex justify-content-between align-items-center border rounded px-3 py-2 mb-2 bg-light">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-user-circle text-primary fs-4 me-2"></i>
                                <span class="text-muted">{{ __('label.student') }}</span>
                            </div>

                            <span class="fw-bold text-dark">
                                {{ $student->name }} {{ $student->last_name }}
                            </span>
                        </div>

                       

                        <!-- Father -->
                        <div class="d-flex justify-content-between align-items-center border rounded px-3 py-2 mb-2 bg-light">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-user text-warning fs-4 me-2"></i>
                                <span class="text-muted">{{ __('label.father_name') }}</span>
                            </div>

                            <span class="fw-semibold">
                                {{ $student->father_name ?? '-' }}
                            </span>
                        </div>

                        <!-- Phone -->
                        <div class="d-flex justify-content-between align-items-center border rounded px-3 py-2 bg-light">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-phone text-success fs-4 me-2"></i>
                                <span class="text-muted">{{ __('label.phone_no') }}</span>
                            </div>

                            <span class="fw-semibold">
                                {{ $student->phone_no ?? '-' }}
                            </span>
                        </div>

                    </div>

                </div>
                @if(edit(Auth::user()->role_ids,$active_menu_id))
                <div class="row g-2 mt-4">

                    <div class="col-6">
                        <a class="btn btn-primary w-100"
                        href="{{ route('student-financial-profile', [
                                'menu_id' => $this->active_menu_id,
                                'student_id' => encrypt($student->id),
                                'slug' => 'student_fees',
                            ]) }}" style="font-size:10px;">
                            <i class="bx bx-wallet me-1"></i>
                            {{ __('label.financial_profile') }}
                        </a>
                    </div>

                    <div class="col-6">
                        <a class="btn btn-info w-100"
                        href="{{ route('special-course-list', [
                                'menu_id' => $this->active_menu_id,
                                'student_id' => $student->id,
                            ]) }}" style="font-size:12px;">
                            <i class="bx bx-book-add me-1"></i>
                            {{ __('label.add_to_course') }}
                        </a>
                    </div>

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

                        <li class="nav-item mb-1">
                            <a class="nav-link {{ $activeTab == 'student_activity_logs' ? 'active' : '' }}" wire:click="changeTab('student_activity_logs')">
                                <i class="bx bx-history me-2 text-info"></i> {{ __('label.student_activity_logs') }}
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
                        @if($activeTab == 'student_courses')
                           @livewire(
                                'academic.students.student-courses.student-course-list', 
                                [
                                    'active_menu_id' => $active_menu_id,
                                    'student_id' => $student->id
                                ],
                                key('student_course_list_'.$student->id)
                            )
                        @endif
                        </div>

                        <div style="{{ $activeTab == 'result_sheet' ? '' : 'display:none' }}">
                            @if($activeTab == 'result_sheet')
                                @livewire(
                                    'academic.students.student-courses.student-course-result-list', 
                                    [
                                        'active_menu_id' => $active_menu_id,
                                        'student_id' => $student->id
                                    ],
                                    key('result_sheet_'.$student->id)
                                )
                            @endif
                        </div>

                        <!-- Book Fee Tab -->
                        <div style="{{ $activeTab == 'student_attendance' ? '' : 'display:none' }}">
                            @if($activeTab == 'student_attendance')
                                @livewire(
                                    'academic.students.student-courses.student-course-attendance-list', 
                                    [
                                        'active_menu_id' => $active_menu_id,
                                        'student_id' => $student->id
                                    ],
                                    key('student_attendance_'.$student->id)
                                )
                            @endif
                        </div>

                        <div style="{{ $activeTab == 'student_activity_logs' ? '' : 'display:none' }}">
                            @if($activeTab == 'student_activity_logs')
                                @livewire(
                                    'academic.students.student-activitylogs.student-activity-log-list', 
                                    [
                                        'active_menu_id' => $active_menu_id,
                                        'student_id' => $student->id
                                    ],
                                    key('student_activity_logs_'.$student->id)
                                )
                            @endif
                        </div>


                    </div>

                </div>
            </div>

        </div>
        <!--/ User Content -->
    </div>
    </div>
</div>

