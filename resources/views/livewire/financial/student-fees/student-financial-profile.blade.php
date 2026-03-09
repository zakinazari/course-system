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
                    <img class="img-fluid rounded my-4" src="{{ $student->photo?->thumbnail_url ?? asset('default.png') }}" height="110" width="110" alt="User avatar">
                    <div class="user-info text-center">
                    <h5 class="mb-2">{{ $student->name }} {{ $student->last_name }}</h5>
                    <span class="badge bg-label-secondary">{{ $student->student_code }}</span>
                    </div>
                </div>
                </div>
                <div class="d-flex justify-content-around flex-wrap my-4 py-3">
                <div class="d-flex align-items-start me-4 mt-3 gap-3">
                    <span class="badge bg-label-primary p-2 rounded"><i class="bx bx-check bx-sm"></i></span>
                    <div>
                    <h5 class="mb-0">1.23k</h5>
                    <span>Tasks Done</span>
                    </div>
                </div>
                <div class="d-flex align-items-start mt-3 gap-3">
                    <span class="badge bg-label-primary p-2 rounded"><i class="bx bx-customize bx-sm"></i></span>
                    <div>
                    <h5 class="mb-0">568</h5>
                    <span>Projects Done</span>
                    </div>
                </div>
                </div>
                <h5 class="pb-2 border-bottom mb-4">Fees</h5>
                <div class="info-container">
                    <ul class="nav nav-pills flex-column mb-3 fee-menu">
                        <li class="nav-item mb-1">
                            <a class="nav-link {{ $activeTab == 'course_fee' ? 'active' : '' }}" wire:click="changeTab('course_fee')">
                                <i class="bx bx-user me-2 text-primary"></i> Course Fee
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a class="nav-link {{ $activeTab == 'book_fee' ? 'active' : '' }}" wire:click="changeTab('book_fee')">
                                <i class="bx bx-book me-2 text-success"></i> Book Fee
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a class="nav-link {{ $activeTab == 'registration_fee' ? 'active' : '' }}" wire:click="changeTab('registration_fee')">
                                <i class="bx bx-detail me-2 text-warning"></i> Registration Fee
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a class="nav-link {{ $activeTab == 'card_fee' ? 'active' : '' }}" wire:click="changeTab('card_fee')">
                                <i class="bx bx-id-card me-2 text-info"></i> Card Fee
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a class="nav-link {{ $activeTab == 'exam_fee' ? 'active' : '' }}" wire:click="changeTab('exam_fee')">
                                <i class="bx bx-file me-2 text-danger"></i> Exam Fee
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

            <!-- User Pills -->
           
            <!-- /User Pills -->

            <!-- Dynamic Content -->
            <div class="card">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">

                    <div wire:loading wire:target="changeTab" class="text-center my-5">
                        <div class="spinner-border" role="status"></div>
                        <p class="mt-2 mb-0"> Loading...</p>
                    </div>

                    <div wire:loading.remove wire:target="changeTab" class="w-100">
                        @if($activeTab == 'course_fee')
                           @livewire('financial.student-fees.student-course-fees', ['active_menu_id' => $active_menu_id,'student_id'=>$student->id])
                        @elseif($activeTab == 'book_fee')
                            <h5> Book Fee</h5>
                        
                        @elseif($activeTab == 'registration_fee')
                            <h5> Registration Fee</h5>
                        @elseif($activeTab == 'card_fee')
                            <h5> Card Fee</h5>
                        @elseif($activeTab == 'exam_fee')
                            <h5> Exam Fee</h5>
                        @endif
                    </div>

                </div>
            </div>
   

        </div>
        <!--/ User Content -->
    </div>
</div>
