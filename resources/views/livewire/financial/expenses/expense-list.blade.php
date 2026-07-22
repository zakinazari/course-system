
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
       
        <div class="card-header">
      
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="card-title mb-2 mb-md-0">
                    {{ $active_menu?->name }}
                </h5>

                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <!-- Export Button -->
                    <div class="btn-group">
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
                                <!-- <a class="dropdown-item d-flex align-items-center gap-2" href="#" wire:click.prevent="print">
                                    <i class="fa fa-print text-secondary" ></i> {{ __('label.print') }}
                                </a> -->
                            </li>
                        </ul>
                    </div>

                    <!-- Add New Record Button -->
                    @if(add(Auth::user()->role_ids,$active_menu_id))
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#{{$modalId}}" wire:click="openModal">
                            <i class="bi bi-plus-lg"></i> {{ __('label.add_new_record') }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
        <hr>
        <div class="table-responsive text-nowrap">
 
            <div class="mb-3 px-3">
                <form wire:submit.prevent="applySearch" class="row g-3 align-items-end">
                    
                    <div class="@if(!auth()->user()->branch_id) col-md-3 @else col-md-6 @endif">
                        <label class="form-label">{{ __('label.name') }}</label>
                        <input type="text" class="form-control" placeholder="{{ __('label.name') }}" wire:model="search.name">
                    </div>
                    @if(!auth()->user()->branch_id)
                    <div class="col-md-3">
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
                      <div class="col-md-3" wire:ignore>
                        <label class="form-label">{{ __('label.section') }}</label>
                        <select class="form-select select2" wire:model.defer="search.section_id" id ="search_section_id">
                        <option value="">{{ __('label.all') }}</option>
                        @foreach($sections as $section)
                                <option value="{{ $section->id }}"  wire:key="section-search-{{ $section->id }}">
                                    {{ $section->name }}
                                </option>
                        @endforeach
                        </select>
                    </div>

                    <div class="col-md-3" wire:ignore>
                        <label class="form-label">{{ __('label.category') }}</label>
                        <select class="form-select select2" wire:model.defer="search.category_id" id ="search_category_id">
                        <option value="">{{ __('label.all') }}</option>
                        @foreach($categories as $category)
                                <option value="{{ $category->id }}"  wire:key="category-search-{{ $category->id }}">
                                    {{ $category->name }}
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
                    <div class="col-md-2">
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
                        <option value="100">100</option>
                        <option value="">{{ __('label.all') }}</option>
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
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="quantity">
                                {{ __('label.quantity') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="unit_id">
                                {{ __('label.unit') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="category_id">
                                {{ __('label.category') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="unit_price">
                                {{ __('label.unit_price') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="total_amount">
                                {{ __('label.total_amount') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="section_id">
                                {{ __('label.section') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="shop_id">
                                {{ __('label.shop') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="employee_id">
                                {{ __('label.purchased_by') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="note">
                                {{ __('label.note') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="expense_date">
                                {{ __('label.expense_date') }}
                            </th>
                            
                            @if(!auth()->user()->branch_id)
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="branch_id">
                                {{ __('label.branch') }}
                            </td>
                            @endif
                            <th>{{ __('label.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($expenses as $i => $expense)
                        <tr>
                            <td>{{ ($expenses->currentPage() - 1) * $expenses->perPage() + $i + 1 }}</td>
                            <td>{{ $expense->name }}</td>
                            <td>{{ $expense->quantity }}</td>
                            <td>{{ $expense->unit?->name }}</td>
                            <td>{{ $expense->category?->name }}</td>
                            <td>{{ $expense->unit_price }}</td>
                            <td>{{ $expense->total_amount }}</td>
                            <td>{{ $expense->section?->name }}</td>
                            <td>{{ $expense->shop?->name }}</td>
                            <td>{{ $expense->purchasedByEmployee?->name }} {{ $expense->purchasedByEmployee?->last_name }}</td>
                            <td>{{ $expense->note }}</td>
                            <td>{{ $expense->expense_date?->format('Y/m/d') }}</td>
                            @if(!auth()->user()->branch_id) 
                            <td>{{ $expense->branch?->name }}</td>
                            @endif 
                            <td>
                                <div class="dropdown position-static">
                                    <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if(edit(Auth::user()->role_ids,$active_menu_id))
                                        @if((Auth::user()->id === $expense->user_id && $expense->created_at->format('Y/m/d') == now()->format('Y/m/d')) || Auth::user()->isAdmin() || Auth::user()->isDeveloper())
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="edit({{ $expense->id }})"
                                            ><i class="bx bx-edit-alt me-1 text-success"></i>{{ __('label.edit') }}</a>
                                        @endif
                                        @endif
                                        @if(delete(Auth::user()->role_ids,$active_menu_id))
                                        @if((Auth::user()->id === $expense->user_id && $expense->created_at->format('Y/m/d') == now()->format('Y/m/d')) || Auth::user()->isAdmin() || Auth::user()->isDeveloper())
                                            <a class="dropdown-item " href="javascript:void(0);"  onclick="confirmDelete({{ $expense->id }},'{{$table_name}}')"
                                            ><i class="bx bx-trash me-1 text-danger"></i>{{ __('label.delete') }}</a>
                                        @endif
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
                {{ $expenses->links() }}
            </div>
        </div>
    </div>
    
    <!--/ Bootstrap Table with Header Dark -->
    <div class="modal fade" id="{{$modalId}}" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog modal-lg" expense="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@if($editMode) {{ __('label.editing') }}  @else {{ __('label.adding') }} @endif @if(App::getLocale() =='en') {{ $active_menu?->name_en }} @elseif(App::getLocale()=='fa') {{ $active_menu?->name }} @endif</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <form @if($editMode) wire:submit.prevent="update" @else wire:submit.prevent="store" @endif>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.name') }} <span style="color:red;">*</span></label>
                                <input type="text" id="nameBasic" class="form-control @error('name') is-invalid @enderror" wire:model.lazy="name">
                                @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label>{{ __('label.quantity') }}<span style="color:red;">*</span></label>
                                <input type="number" wire:model.lazy="quantity" class="form-control" min="0" step="any">
                                @error('quantity') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>{{ __('label.unit_price') }}<span style="color:red;">*</span></label>
                                <input type="number" wire:model.lazy="unit_price" class="form-control" min="0" step="any">
                                @error('unit_price') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>{{ __('label.total_amount') }}</label>
                                <input type="number" wire:model.lazy="total_amount" class="form-control" min="0" readonly step="any">
                                @error('total_amount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col mb-3">
                                <div wire:ignore>
                                    <label class="form-label">{{ __('label.unit') }} <span style="color:red;">*</span></label>
                                    <select class="form-select select2 @error('unit_id') is-invalid @enderror" wire:model.lazy="unit_id" id ="unit_id">
                                        <option value="">{{ __('label.select') }}</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}"  wire:key="unit-{{ $unit->id }}">
                                                {{ $unit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('unit_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col mb-3">
                                <div wire:ignore>
                                    <label class="form-label">{{ __('label.category') }} <span style="color:red;">*</span></label>
                                    <select class="form-select select2 @error('category_id') is-invalid @enderror" wire:model.lazy="category_id" id ="category_id">
                                        <option value="">{{ __('label.select') }}</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}"  wire:key="category-{{ $category->id }}">
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('category_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                           
                        </div>

                        <div class="row">
                            <div class="col mb-3" style="position: relative; z-index: 1000;">
                                <label class="form-label">{{ __('label.shop') }} <span style="color:red;">*</span></label>
                                <input type="text"
                                    wire:model.live.debounce.300ms="shop_search"
                                    class="form-control"
                                    placeholder="Search shop...">
                                @error('shop_search')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror

                                @if(!empty($shops))

                                    <ul class="list-group"
                                        style="
                                            position: absolute;
                                            top: 100%;
                                            left: 0;
                                            width: 100%;
                                            z-index: 99999;
                                            background: #fff;
                                        ">

                                        @foreach($shops as $shop)
                                            <li class="list-group-item list-group-item-action"
                                                wire:click="selectShop({{ $shop->id }})"
                                                style="cursor:pointer;">
                                                {{ $shop->name }}
                                            </li>
                                        @endforeach

                                    </ul>

                                @endif

                            </div>
                            <div class="col mb-3">
                                <div wire:ignore>
                                    <label class="form-label">{{ __('label.purchased_by') }}</label>
                                    <select class="form-select select2 @error('employee_id') is-invalid @enderror" wire:model.lazy="employee_id" id ="employee_id">
                                        <option value="">{{ __('label.select') }}</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}"  wire:key="employee-{{ $employee->id }}">
                                                {{ $employee->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('employee_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col mb-3">
                                <div wire:ignore>
                                    <label class="form-label">{{ __('label.section') }} <span style="color:red;">*</span></label>
                                    <select class="form-select" wire:model.lazy ="section_id">
                                        <option value="">{{ __('label.select') }}</option>
                                        @foreach($sections as $section)
                                            <option value="{{ $section->id }}"  wire:key="section-add-edit-{{ $section->id }}">
                                                {{ $section->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('section_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.expense_date') }} <span style="color:red;">*</span></label>
                                <input type="date" id="nameBasic" class="form-control @error('expense_date') is-invalid @enderror" wire:model.lazy="expense_date">
                                @error('expense_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            @if(!auth()->user()->branch_id)
                            <div class="col mb-3">
                              <label class="form-label">{{ __('label.branch') }} <span style="color:red;">*</span></label>
                              <select class="form-select @error('branch_id') is-invalid @enderror" wire:model.lazy="branch_id" id ="branch_id">
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
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <label>{{ __('label.note') }}</label>
                                <textarea type="text" wire:model="note" class="form-control"></textarea>
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

        $('#section_id').off('change').on('change', function () {
            $wire.set('section_id', $(this).val());
        });

        $('#search_section_id').off('change').on('change', function () {
            $wire.set('search.section_id', $(this).val());
        });

        $('#search_category_id').off('change').on('change', function () {
            $wire.set('search.category_id', $(this).val());
        });
        $('#category_id').off('change').on('change', function () {
            $wire.set('category_id', $(this).val());
        });
        $('#unit_id').off('change').on('change', function () {
            $wire.set('unit_id', $(this).val());
        });

        $('#employee_id').off('change').on('change', function () {
            $wire.set('employee_id', $(this).val());
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