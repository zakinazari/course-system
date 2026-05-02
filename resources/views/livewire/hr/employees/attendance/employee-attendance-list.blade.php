
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
        <div class=" text-nowrap">
 
            <div class="mb-3 px-3">
                <form wire:submit.prevent="applySearch" class="row g-3 align-items-end">
                
                    @if(!auth()->user()->branch_id)
                    <div class="col-md-2">
                        <label class="form-label">{{ __('label.branch') }}</label>
                        <select class="form-select" wire:model.lazy="branch_id" id ="">
                           <option value="">{{ __('label.all') }}</option>
                           @foreach($branches as $branch)
                                 <option value="{{ $branch->id }}"  wire:key="branch-search-{{ $branch->id }}">
                                    {{ $branch->name }}
                                 </option>
                           @endforeach
                        </select>
                     </div>
                     @endif
                    <div class="col-md-2 d-flex flex-column">
                        <label class="form-label">{{ __('label.position') }}</label>
                        <select  class="form-select select2" id ="search_position_id" wire:model.lazy="position_id">
                           <option value="">{{ __('label.all') }}</option>
                           @foreach($positions as $position)
                                 <option value="{{ $position->id }}"  wire:key="position-search-{{ $position->id }}">
                                    {{ $position->name }}
                                 </option>
                           @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex flex-column" >
                        <label class="form-label">{{ __('label.employee') }}</label>
                        <select  class="form-select select2" id ="search_employee_id">
                           <option value="">{{ __('label.all') }}</option>
                           @foreach($employees as $em)
                                 <option value="{{ $em->employee->id }}"  wire:key="employee-search-{{ $em->employee->id }}">
                                    {{ $em->employee->name }} {{ $em->employee->last_name }}
                                 </option>
                           @endforeach
                        </select>
                    </div>
                     <div class="col-md-3">
                        <label class="form-label">{{ __('label.date') }}</label>
                        <input type="date" class="form-control @error('attendance_date') is-invalid @enderror" placeholder="" wire:model.lazy="attendance_date">
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
            @if(count($selected_employees) > 0)
            
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead class="table-dark">
                        <tr>
                            <th>
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
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="attendance_status">
                                {{ __('label.attendance_status') }}
                            </th>
                           
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="status">
                                {{ __('label.status') }}
                            </th>
                            @if(!auth()->user()->branch_id)
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="branch_id">
                                {{ __('label.branch') }}
                            </th>
                            @endif
                        </tr>

                    </thead>
                    
                    <tbody class="table-border-bottom-0">
                        @foreach($selected_employees as $i => $emp)
                        <tr wire:key="employee-{{ $emp->employee_id }}">
                            <td>{{ $i + 1 }}</td>

                            <td>
                                {{ $emp->employee?->name }} {{ $emp->employee?->last_name }}
                            </td>
                            <td>
                                {{ $emp->position?->name }}
                            </td>

                            @if(Auth::user()->isDeveloper() || Auth::user()->isAdmin() || ($attendance_date === now()->format('Y-m-d')))
                            <td>
                                
                                <div class="d-flex gap-2">
                                    <div class="form-check form-check-success">
                                    <label><input class="form-check-input" type="radio" wire:model="attendances.{{ $emp->employee?->id }}" value="present"> <span class="badge bg-success"> Present </span> </label>
                                    </div>
                                    <div class="form-check form-check-danger">
                                    <label><input class="form-check-input" type="radio" wire:model="attendances.{{ $emp->employee?->id }}" value="absent" > <span class="badge bg-danger"> Absent </span></label>
                                    </div>
                                    <div class="form-check form-check-warning">
                                    <label><input class="form-check-input" type="radio" wire:model="attendances.{{ $emp->employee?->id }}" value="late"> <span class="badge bg-warning"> Late </span></label>
                                    </div>
                                    <div class="form-check form-check-info">
                                    <label><input class="form-check-input" type="radio" wire:model="attendances.{{ $emp->employee?->id }}" value="excused" > <span class="badge bg-info"> Excused </span></label>
                                </div>
                          
                            </td>
                         
                            <td>
                                @if($existing_attendances[$emp->employee?->id] ?? false)
                                    <span class="badge bg-success">Taken</span>
                                @else
                                    <span class="badge bg-secondary">Not Taken</span>
                                @endif
                            </td>
                                @if(!auth()->user()->branch_id)
                                <td>
                                    {{ $emp->branch?->name }}
                                </td>
                                @endif
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
             @if(add(Auth::user()->role_ids,$active_menu_id) && count($employees) > 0)
                  @if(Auth::user()->isDeveloper() || Auth::user()->isAdmin() || ($attendance_date === now()->format('Y-m-d')))
                    <div class="d-flex justify-content-end mt-4 mb-3 px-3">
                        <button type="button" class="btn btn-primary" wire:click="saveAttendance">
                            <i class="bi bi-save me-1"></i> {{ __('label.save_attendance') }}
                        </button>
                    </div>
                    @endif
                @endif
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



        $('#search_employee_id').off('change').on('change', function () {
            $wire.set('employee_id', $(this).val());
        });

        $('#search_position_id').off('change').on('change', function () {
            $wire.set('position_id', $(this).val());
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


