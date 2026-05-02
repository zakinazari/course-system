
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
                        <!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#{{$modalId}}" wire:click="openModal">
                            <i class="bi bi-plus-lg"></i> {{ __('label.add_new_record') }}
                        </button> -->
                    @endif
                </div>

            </div>
        </div>
        <hr>
        <div class="text-nowrap">
 
            <div class="mb-3 px-3">
                <form wire:submit.prevent="applySearch" class="row g-3 align-items-end">
                   <div class="col-md-3 d-flex flex-column" wire:ignore>
                        <label class="form-label">{{ __('label.employee') }}</label>
                        <select  class="form-select select2" id ="search_employee_id">
                           <option value="">{{ __('label.all') }}</option>
                           @foreach($employees as $employee)
                                 <option value="{{ $employee->id }}"  wire:key="employee-search-{{ $employee->id }}">
                                    {{ $employee->name }} {{ $employee->last_name }}
                                 </option>
                           @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex flex-column" wire:ignore>
                        <label class="form-label">{{ __('label.position') }}</label>
                        <select  class="form-select select2" id ="search_position_id">
                           <option value="">{{ __('label.all') }}</option>
                           @foreach($positions as $position)
                                 <option value="{{ $position->id }}"  wire:key="position-search-{{ $position->id }}">
                                    {{ $position->name }}
                                 </option>
                           @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">{{ __('label.start_date') }}</label>
                        <div class="input-group input-daterange" id="bs-datepicker-daterange">
                            <input type="date" id="dateRangePicker" class="form-control" wire:model="search.start_date">
                            <span class="input-group-text">{{ __('label.end_date') }}</span>
                            <input type="date"  class="form-control" wire:model="search.end_date">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('label.status') }}</label>
                        <select class="form-select" wire:model.defer="search.status" id ="">
                           <option value="">{{ __('label.all') }}</option>
                            <option value="active">{{ __('label.active') }}</option>
                            <option value="inactive">{{ __('label.inactive') }}</option>
                            <option value="end">{{ __('label.end') }}</option>
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
                            <th style="width:40px;">
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="no">
                                {{ __('label.NO') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="employee_id">
                                {{ __('label.employee') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="position_id">
                                {{ __('label.position') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="basic_salary">
                                {{ __('label.basic_salary') }}
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
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="status">
                                {{ __('label.status') }}
                            </th>
                            <th>{{ __('label.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($contracts as $i => $contract)
                        <tr>
                            <td>{{ ($contracts->currentPage() - 1) * $contracts->perPage() + $i + 1 }}</td>
                            <td>{{ $contract->employee?->name }} {{ $contract->employee?->last_name }}</td>
                            <td>{{ $contract->position?->name }}</td>
                            <td>{{ $contract->basic_salary }}</td>
                            <td>{{ $contract->start_date?->format('Y/m/d') }}</td>
                            <td>{{ $contract->end_date?->format('Y/m/d') }}</td>
                            <td>
                              @if($contract->status==='active')
                              <span class="badge bg-label-success me-1" style="font-size:10px;">{{ ucfirst($contract->status) }}</span>
                              @elseif($contract->status==='inactive')
                              <span class="badge bg-label-danger me-1" style="font-size:10px;">{{ ucfirst($contract->status) }}</span>
                              @elseif($contract->status==='end')
                              <span class="badge bg-label-info me-1" style="font-size:10px;">{{ ucfirst($contract->status) }}</span>
                              @endif
                            </td>
                            <td>
                                
                                <div class="dropdown position-static">
                                    <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if(edit(Auth::user()->role_ids,$active_menu_id))
                                        @if((Auth::user()->id === $contract->user_id && $contract->created_at->format('Y/m/d') == now()->format('Y/m/d')) || Auth::user()->isAdmin() || Auth::user()->isDeveloper())
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="edit({{ $contract->id }})"
                                            ><i class="bx bx-edit-alt me-1 text-success"></i>{{ __('label.edit') }}</a>
                                        @endif
                                        @endif
                                   
                                        @if(delete(Auth::user()->role_ids,$active_menu_id))
                                            @if((Auth::user()->id === $contract->user_id && $contract->created_at->format('Y/m/d') == now()->format('Y/m/d')) || Auth::user()->isAdmin() || Auth::user()->isDeveloper())
                                            <a class="dropdown-item " href="javascript:void(0);"  onclick="confirmDelete({{ $contract->id }},'{{$table_name}}')"
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
                {{ $contracts->links() }}
            </div>
        </div>
    </div>
    
    <!--/ Bootstrap Table with Header Dark -->
    <div class="modal fade" id="{{$modalId}}" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog modal-lg" branch="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@if($editMode) {{ __('label.editing') }}  @else {{ __('label.adding') }} @endif @if(App::getLocale() =='en') {{ $active_menu?->name_en }} @elseif(App::getLocale()=='fa') {{ $active_menu?->name }} @endif</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <form @if($editMode) wire:submit.prevent="update" @else wire:submit.prevent="store" @endif>
                    <div class="modal-body">
                        

                         <div class="mb-3" >
                              <label>{{ __('label.employee') }}<span style="color:red;">*</span></label>
                              <select class="form-select select2"  wire:model.lazy="employee_id" id="employee_id">
                                   <option value="">{{ __('label.select') }}</option>
                                   @foreach($employees as $employee)
                                   <option value="{{ $employee->id }}" wire:key="employee-{{ $employee->id }}">
                                        {{ $employee->name }} {{ $employee->last_name }}
                                   </option>
                                   @endforeach
                              </select>
                              @error('employee_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                         </div>
                         <div class="mb-3">
                              <label>{{ __('label.position') }}<span style="color:red;">*</span></label>
                              <select wire:model.lazy="position_id" class="form-control select2" id="position_id">
                                   <option value="">{{ __('label.select') }}</option>
                                   @foreach($positions as $position)
                                   <option value="{{ $position->id }}" wire:key="position-{{ $position->id }}">
                                        {{ $position->name }}
                                   </option>
                                   @endforeach
                              </select>
                              @error('position_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                         </div>
                         <div class="mb-3">
                              <label>{{ __('label.basic_salary') }}<span style="color:red;">*</span></label>
                              <input type="number" wire:model.lazy="basic_salary" class="form-control" min="0">
                              @error('basic_salary') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
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

        $('#position_id').off('change').on('change', function () {
            $wire.set('position_id', $(this).val());
        });
        $('#employee_id').off('change').on('change', function () {
            $wire.set('employee_id', $(this).val());
        });

        $('#search_employee_id').off('change').on('change', function () {
            $wire.set('search.employee_id', $(this).val());
        });

        $('#search_position_id').off('change').on('change', function () {
            $wire.set('search.position_id', $(this).val());
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
