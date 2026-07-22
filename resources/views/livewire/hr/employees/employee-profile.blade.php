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
    @if(App::getLocale()=='en') {{ $active_menu?->name_en }} @else {{ $active_menu?->name }}  @endif
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
                    <img class="img-fluid rounded my-4" src="{{ $employee->photo?->thumbnail_url ?? asset('default.png') }}" height="110" width="110" alt="User avatar">
                    <div class="user-info text-center">
                    <h5 class="mb-2"> {{ $employee->name }} {{ $employee->last_name }}</h5>
                    <span class="badge bg-label-secondary">{{ $employee->employee_code }}</span>
                    </div>
                </div>
                </div>
        
                <h5 class="pb-2 border-bottom mb-4"></h5>
                <div class="info-container">
                    <ul class="nav nav-pills flex-column mb-3 fee-menu">
                        <li class="nav-item mb-1">
                            <a class="nav-link {{ $activeTab == 'temporary_contract' ? 'active' : '' }}" wire:click="changeTab('temporary_contract')">
                                <i class="bx bx-book me-2 text-primary"></i> {{ __('label.temporary_contract') }}
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a class="nav-link {{ $activeTab == 'permanent_contract' ? 'active' : '' }}" wire:click="changeTab('permanent_contract')">
                                <i class="bx bx-book me-2 text-info"></i> {{ __('label.permanent_contract') }}
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a class="nav-link {{ $activeTab == 'salary_advance' ? 'active' : '' }}" wire:click="changeTab('salary_advance')">
                                <i class="bx bx-id-card me-2 text-danger"></i> {{ __('label.salary_advance') }}
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a class="nav-link {{ $activeTab == 'staff_attendance' ? 'active' : '' }}" wire:click="changeTab('staff_attendance')">
                                <i class="bx bx-user-check me-2 text-primary"></i>
                                {{ __('label.staff_attendance') }}
                            </a>
                        </li>

                        <li class="nav-item mb-1">
                            <a class="nav-link {{ $activeTab == 'teacher_attendance' ? 'active' : '' }}" wire:click="changeTab('teacher_attendance')">
                                <i class="bx bx-user-pin me-2 text-success"></i>
                                {{ __('label.teacher_attendance') }}
                            </a>
                        </li>

                       
                        <li class="nav-item mb-1">
                            <a class="nav-link {{ $activeTab == 'employee_leave' ? 'active' : '' }}" wire:click="changeTab('employee_leave')">
                                <i class="bx bx-calendar-event me-2 text-danger"></i> {{ __('label.employee_leave') }}
                            </a>
                        </li>
                        
                        <li class="nav-item mb-1">
                            <a class="nav-link {{ $activeTab == 'permanent_payroll' ? 'active' : '' }}" wire:click="changeTab('permanent_payroll')">
                                <i class="bx bx-credit-card me-2 text-info"></i>
                                {{ __('label.permanent_payroll') }}
                            </a>
                        </li>

                        <li class="nav-item mb-1">
                            <a class="nav-link {{ $activeTab == 'temporary_payroll' ? 'active' : '' }}" wire:click="changeTab('temporary_payroll')">
                                <i class="bx bx-credit-card me-2 text-secondary"></i>
                                {{ __('label.temporary_payroll') }}
                            </a>
                        </li>

                         <li class="nav-item mb-1">
                            <a class="nav-link {{ $activeTab == 'salary_saving' ? 'active' : '' }}" wire:click="changeTab('salary_saving')">
                                <i class="bx bx-lock-alt me-2 text-success"></i> {{ __('label.salary_saving') }}
                            </a>
                        </li>


                        <li class="nav-item mb-1">
                            <a class="nav-link {{ $activeTab == 'assigned_assets' ? 'active' : '' }}" wire:click="changeTab('assigned_assets')">
                                <i class="bx bx-detail me-2 text-warning"></i> {{ __('label.assigned_assets') }}
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a class="nav-link {{ $activeTab == 'assigned_books' ? 'active' : '' }}" wire:click="changeTab('assigned_books')">
                                <i class="bx bx-book me-2 text-success"></i> {{ __('label.book_assignment') }}
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
                        <div style="{{ $activeTab == 'temporary_contract' ? '' : 'display:none' }}">
                        
                            @livewire(
                                'hr.employees.contracts.employee-temporary-contract-list', 
                                [
                                    'active_menu_id' => $active_menu_id,
                                    'employee_id' => $employee->id
                                ],
                                key('temporary_contract_'.$employee->id)
                            )
                      
                        </div>

                        <div style="{{ $activeTab == 'permanent_contract' ? '' : 'display:none' }}">
                     
                                @livewire(
                                    'hr.employees.contracts.employee-permanent-contract-list', 
                                    [
                                        'active_menu_id' => $active_menu_id,
                                        'employee_id' => $employee->id
                                    ],
                                    key('permanent_contract_'.$employee->id)
                                )
                          
                        </div>

                        <!-- Book Fee Tab -->
                        <div style="{{ $activeTab == 'salary_advance' ? '' : 'display:none' }}">
                            @if($activeTab == 'salary_advance')
                                @livewire(
                                    'hr.employees.contracts.employee-salary-advance-list', 
                                    [
                                        'active_menu_id' => $active_menu_id,
                                        'employee_id' => $employee->id
                                    ],
                                    key('salary_advance_'.$employee->id)
                                )
                            @endif
                        </div>

                        <div style="{{ $activeTab == 'staff_attendance' ? '' : 'display:none' }}">

                        @if($activeTab == 'staff_attendance')
                           @livewire(
                                'hr.employees.personal-attendance.staff-attendance-list', 
                                [
                                    'active_menu_id' => $active_menu_id,
                                    'employee_id' => $employee->id
                                ],
                                key('staff_attendance_'.$employee->id)
                            )
                        @endif
                        </div>

                        <div style="{{ $activeTab == 'teacher_attendance' ? '' : 'display:none' }}">
                            @if($activeTab == 'teacher_attendance')
                                @livewire(
                                        'hr.employees.personal-attendance.teacher-attendance-list', 
                                        [
                                            'active_menu_id' => $active_menu_id,
                                            'employee_id' => $employee->id
                                        ],
                                        key('teacher_attendance_'.$employee->id)
                                    )
                            @endif
                        </div>

                        <div style="{{ $activeTab == 'employee_leave' ? '' : 'display:none' }}">
                            @if($activeTab == 'employee_leave')
                                @livewire(
                                        'hr.employees.employee-leaves.employee-leave-list', 
                                        [
                                            'active_menu_id' => $active_menu_id,
                                            'employee_id' => $employee->id
                                        ],
                                        key('employee_leave_'.$employee->id)
                                    )
                            @endif
                        </div>

                        <div style="{{ $activeTab == 'permanent_payroll' ? '' : 'display:none' }}">
                            @if($activeTab == 'permanent_payroll')
                                @livewire(
                                        'hr.employees.personal-payrolls.employee-permanent-payroll-list', 
                                        [
                                            'active_menu_id' => $active_menu_id,
                                            'employee_id' => $employee->id
                                        ],
                                        key('permanent_payroll_'.$employee->id)
                                    )
                            @endif
                        </div>

                        <div style="{{ $activeTab == 'temporary_payroll' ? '' : 'display:none' }}">
                            @if($activeTab == 'temporary_payroll')
                                @livewire(
                                        'hr.employees.personal-payrolls.employee-temporary-payroll-list', 
                                        [
                                            'active_menu_id' => $active_menu_id,
                                            'employee_id' => $employee->id
                                        ],
                                        key('temporary_payroll_'.$employee->id)
                                    )
                            @endif
                        </div>

                        <div style="{{ $activeTab == 'salary_saving' ? '' : 'display:none' }}">
                            @if($activeTab == 'salary_saving')
                                @livewire(
                                        'hr.employees.salary-security-saving.salary-security-saving-list', 
                                        [
                                            'active_menu_id' => $active_menu_id,
                                            'employee_id' => $employee->id
                                        ],
                                        key('salary_saving_'.$employee->id)
                                    )
                            @endif
                        </div>

                        <div style="{{ $activeTab == 'assigned_assets' ? '' : 'display:none' }}">
                        @if($activeTab == 'assigned_assets')
                          @livewire(
                                'hr.employees.assigned-assets.assigned-asset-list', 
                                [
                                    'active_menu_id' => $active_menu_id,
                                    'employee_id' => $employee->id
                                ],
                                key('assigned_assets_'.$employee->id)
                            )
                        @endif
                        </div>

                        <!-- Registration Fee Tab -->
                        <div style="{{ $activeTab == 'assigned_books' ? '' : 'display:none' }}">
                            @if($activeTab == 'assigned_books')
                                @livewire(
                                    'hr.employees.assigned-books.assigned-book-list', 
                                    [
                                        'active_menu_id' => $active_menu_id,
                                        'employee_id' => $employee->id
                                    ],
                                    key('assigned_book_'.$employee->id)
                                )
                            @endif
                        </div>

                        <!-- Card Fee Tab -->
                        <div style="{{ $activeTab == 'card_fee' ? '' : 'display:none' }}">
                          
                        </div>

                        <!-- Exam Fee Tab -->
                        <div style="{{ $activeTab == 'exam_fee' ? '' : 'display:none' }}">
                         
                        </div>

                    </div>

                </div>
            </div>

        </div>
        <!--/ User Content -->
    </div>
    </div>
</div>
