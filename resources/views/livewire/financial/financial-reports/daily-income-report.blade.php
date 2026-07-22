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

            <h5 class="card-title mb-0">{{ $active_menu?->name }}</h5>

            <div class="d-flex align-items-center gap-2">
                <!-- <div class="btn-group">
                        <button type="button" class="btn btn-secondary">
                            <i class="fa fa-file-export"></i> {{ __('label.export') }}
                        </button>

                        <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Toggle Dropdown</span>
                        </button>

                        <ul class="dropdown-menu">
                            <li class="px-3 py-2">
                                <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-3 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="portraitRadio" wire:model="pdfOrientation" value="portrait">
                                        <label class="form-check-label" for="portraitRadio">(Portrait)</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="landscapeRadio" wire:model="pdfOrientation" value="landscape">
                                        <label class="form-check-label" for="landscapeRadio">(Landscape)</label>
                                    </div>
                                </div>

                                <a class="dropdown-item d-flex align-items-center gap-2" href="#" wire:click.prevent="exportPdf">
                                    <i class="fa fa-file-pdf text-danger"></i> {{ __('label.export_to_pdf') }}
                                </a>
                                
                            </li>
                        </ul>
                    </div> -->
                    <a class="btn btn-secondary d-flex align-items-center gap-2"
                    href="#"
                        wire:click.prevent="print">
                        <i class="fa fa-print"></i>
                        {{ __('label.print') }}
                    </a>
            </div>

        </div>
        <hr>
        <div class="table-responsive text-nowrap ">
 
            <div class="mb-3 px-3 mb-5">
                <form wire:submit.prevent="applySearch" class="row g-3 align-items-end">
     
                    @if(!auth()->user()->branch_id)
                    <div class="col-md-4">
                        <label class="form-label">{{ __('label.branch') }}</label>
                        <select class="form-select" wire:model.defer="search.branch_id" id ="">
                        <option value="">{{ __('label.all') }}</option>
                        @foreach($branches as $branch)
                                <option value="{{ $branch->id }}"  wire:key="branch-search-{{ $branch->id }}">
                                    {{ $branch->name }}
                                </option>
                        @endforeach
                        </select>
                    </div>
                    @endif
                
                    <div class="col-md-4">
                        <label class="form-label">{{ __('label.section') }}</label>
                            <select class="form-select " wire:model.defer="search.section_id" >
                            <option value="">{{ __('label.all') }}</option>
                            @foreach($sections as $section)
                                    <option value="{{ $section->id }}"  wire:key="section-search-{{ $section->id }}">
                                        {{ $section->name }}
                                    </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 d-flex flex-column" >
                        <label class="form-label">{{ __('label.category') }}</label>
                        <select class="form-select" wire:model.lazy="search.category" >
                        <option value="">{{ __('label.select') }}</option>
                        @foreach($income_categories as $category)
                                <option value="{{ $category->value }}"  wire:key="category-search-{{ $category->value }}">
                                    {{ str($category->name)->replace('_', ' ')->title() }}
                                </option>
                        @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 d-flex flex-column"  wire:ignore>
                        <label class="form-label">{{ __('label.user') }}</label>
                        <select class="form-select select2" wire:model.defer="search.user_id" id= "search_user_id">
                        <option value="">{{ __('label.all') }}</option>
                        @foreach($users as $user)
                                <option value="{{ $user->id }}"  wire:key="user-search-{{ $user->id }}">
                                    {{ $user->email }}
                                </option>
                        @endforeach
                        </select>
                    </div>
                
                    <div class="col-md-4">
                        <label class="form-label">{{ __('label.from_date') }}</label>
                        <div class="input-group input-daterange" id="bs-datepicker-daterange">
                            <input type="date" id="dateRangePicker" class="form-control" wire:model="search.from">
                            <span class="input-group-text">{{ __('label.to_date') }}</span>
                            <input type="date"  class="form-control" wire:model="search.to">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary">
                            {{ __('label.search') }}
                        </button>
                    </div>
                </form>
            </div>
            <div class="table-responsive text-nowrap mb-5">
                @if(!empty($financial_records))
                <hr>
                  <div id="printArea">
                    <!-- Logo -->
                        <div style="text-align:center;margin-bottom:10px;">
                            <img src="{{ asset('logo.png') }}" alt="Logo" style="height:70px;">
                        </div>
                        <!-- Title -->
                        <h2 style="text-align:center;">
                            {{ __('label.daily_income_report') }}
                        </h2>
                        @if($search['category'])
                            
                            <h2 style="text-align:center;">
                                {{ ucwords(str_replace('_', ' ', $search['category'])) }}
                            </h2>
                        @endif
                        <!-- Date range, aligned left, close to table -->
                        <div style="margin-bottom:3px; font-size:12px; text-align:left;">
                        {{ __('label.from_date') }}: {{ $search['from'] ?? '---' }} &nbsp;&nbsp; {{ __('label.to_date') }}: {{ $search['to'] ?? '---' }}
                        </div>

                        @includeIf('livewire.financial.financial-reports.financial-report-records.income-records.' . $records_view, [
                            'records' => $financial_records,
                            ''
                        ])
                      
                    </div>
                @endif
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

        $('#search_category_id').off('change').on('change', function () {
            $wire.set('search.category', $(this).val());
        });

        $('#search_user_id').off('change').on('change', function () {
            $wire.set('search.user_id', $(this).val());
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

<script>
    window.addEventListener('show-print-preview', () => {
        printDiv('printArea');
    });
</script>
