
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
        <div class="text-nowrap">
 
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
                        <label class="form-label">{{ __('label.status') }}</label>
                        <select class="form-select" wire:model.defer="search.status" id ="">
                           <option value="">{{ __('label.all') }}</option>
                            <option value="warehouse">{{ __('label.warehouse') }}</option>
                            <option value="assigned">{{ __('label.assigned') }}</option>

                        </select>
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
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="code">
                                {{ __('label.code') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="name">
                                {{ __('label.name') }}
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
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="purchase_price">
                                {{ __('label.purchase_price') }}
                            </th>

                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="section_id">
                                {{ __('label.section') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="note">
                                {{ __('label.note') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="status">
                                {{ __('label.status') }}
                            </th>
                            <th>
                                {{ __('label.assignments') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="purchase_date">
                                {{ __('label.purchase_date') }}
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
                        @foreach($assets as $i => $asset)
                        <tr>
                            <td>{{ ($assets->currentPage() - 1) * $assets->perPage() + $i + 1 }}</td>
                            <td>{{ $asset->code }}</td>
                            <td>{{ $asset->name }}</td>
                            <td>{{ $asset->unit?->name }}</td>
                            <td>{{ $asset->category?->name }}</td>
                            <td>{{ $asset->purchase_price }}</td>
                            <td>{{ $asset->section?->name }}</td>
                            <td>{{ $asset->note }}</td>
                            <td>
                                @if($asset->status=='warehouse')
                                <span class="badge bg-label-success me-1" style="font-size:10px;">{{ ucfirst($asset->status) }}</span>
                                @elseif($asset->status=='assigned')
                                <span class="badge bg-label-danger me-1" style="font-size:10px;">{{ ucfirst($asset->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <a class="btn btn-success btn-icon rounded-pill"
                                href="javascript:void(0);"
                                wire:click="showAssignments({{ $asset->id }})">
                                    <i class="bx bx-money text-white"></i>
                                </a>
                            </td>
                            <td>{{ $asset->purchase_date?->format('Y/m/d') }}</td>
                            @if(!auth()->user()->branch_id) 
                            <td>{{ $asset->branch?->name }}</td>
                            @endif 
                            <td>
                                <div class="dropdown position-static">
                                    <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if(edit(Auth::user()->role_ids,$active_menu_id) && $asset->status=='warehouse')
                                   
                                        <a class="dropdown-item"
                                        href="javascript:void(0);"
                                        wire:click="selectToAssign({{ $asset->id }})">
                                            <i class="bx bx-user-check me-1 text-primary"></i>
                                            {{ __('label.assign') }}
                                        </a>

                                        @endif

                                        @if(edit(Auth::user()->role_ids,$active_menu_id))
                                        @if((Auth::user()->id === $asset->user_id && $asset->created_at->format('Y/m/d') == now()->format('Y/m/d')) || Auth::user()->isAdmin() || Auth::user()->isDeveloper())
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="edit({{ $asset->id }})"
                                            ><i class="bx bx-edit-alt me-1 text-success"></i>{{ __('label.edit') }}</a>
                                        @endif
                                        @endif
                                        @if(delete(Auth::user()->role_ids,$active_menu_id))
                                        @if((Auth::user()->id === $asset->user_id && $asset->created_at->format('Y/m/d') == now()->format('Y/m/d')) || Auth::user()->isAdmin() || Auth::user()->isDeveloper())
                                            <a class="dropdown-item " href="javascript:void(0);"  onclick="confirmDelete({{ $asset->id }},'{{$table_name}}')"
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
                {{ $assets->links() }}
            </div>
        </div>
    </div>
    
    <!--/ Bootstrap Table with Header Dark -->
    <div class="modal fade" id="{{$modalId}}" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog modal-lg" asset="document">
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
                           
                        </div>

                        <div class="row">
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
                            <div class="col-md-6 mb-3">
                                <label>{{ __('label.purchase_price') }}<span style="color:red;">*</span></label>
                                <input type="number" wire:model.lazy="purchase_price" class="form-control" min="0">
                                @error('purchase_price') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col mb-3">
                                <div wire:ignore>
                                    <label class="form-label">{{ __('label.section') }} <span style="color:red;">*</span></label>
                                    <select class="form-select select2" wire:model="section_id" id ="section_id">
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
                                <label for="nameBasic" class="form-label">{{ __('label.purchase_date') }} <span style="color:red;">*</span></label>
                                <input type="date" id="nameBasic" class="form-control @error('purchase_date') is-invalid @enderror" wire:model.lazy="purchase_date">
                                @error('purchase_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
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

    <div class="modal fade" id="{{$assignModalId}}" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog modal-lg" asset="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('label.assign') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <form @if($editMode) wire:submit.prevent="assign" @else wire:submit.prevent="store" @endif>
                    <div class="modal-body">
                        
                    
                        <div class="row">
                            <div class="col mb-3">
                                <div wire:ignore>
                                    <label class="form-label">{{ __('label.employee') }} <span style="color:red;">*</span></label>
                                    <select class="form-select select2" wire:model.lazy="assign_to_employee_id" id ="assign_to_employee_id">
                                        <option value="">{{ __('label.select') }}</option>
                                        @foreach($assign_to_employees as $employee)
                                            <option value="{{ $employee->id }}"  wire:key="employee-add-edit-{{ $employee->id }}">
                                                {{ $employee->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('assign_to_employee_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.assign_date') }} <span style="color:red;">*</span></label>
                                <input type="date" id="nameBasic" class="form-control @error('assign_date') is-invalid @enderror" wire:model.lazy="assign_date">
                                @error('assign_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="mb-3">
                                <label>{{ __('label.note') }}</label>
                                <textarea type="text" wire:model="assign_note" class="form-control"></textarea>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" >{{ __('label.close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('label.assign') }} </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="assignmentModal" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog modal-xl" branch="document">
            <div class="modal-content">
                <div class="modal-header">
                    
                    <h5 class="modal-title">{{__('label.assignments')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                    
                </div>
                
                <div class="modal-body">
                    @if($this->assignedAsset->count())
                     <div class="table-responsive text-nowrap">
                        <table class="table">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width:40px;">{{ __('label.NO') }}</th>
                                    <th>{{ __('label.asset') }}</th>
                                    <th>{{ __('label.employee_code') }}</th>
                                    <th>{{ __('label.name') }}</th>
                                    <th>{{ __('label.status') }}</th>
                                    <th>{{ __('label.date') }}</th>
                                    <th>{{ __('label.note') }}</th>
                                    <th>{{ __('label.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @foreach($this->assignedAsset as $i => $assigned)
                                <tr>
                                    <td>{{ ($this->assignedAsset->currentPage() - 1) * $this->assignedAsset->perPage() + $i + 1 }}</td>
                                    <td>{{ $assigned->asset?->name }}</td>
                                    <td>{{ $assigned->employee?->employee_code }}</td>
                                    <td>{{ $assigned->employee?->name }} {{ $assigned->employee?->last_name }}</td>
                                    
                                    <td>
                                    @if($assigned->type==='assigned')
                                    <span class="badge bg-label-danger me-1" style="font-size:10px;">{{ ucfirst($assigned->type) }}</span>
                                    @elseif($assigned->type==='returned')
                                    <span class="badge bg-label-success me-1" style="font-size:10px;">{{ ucfirst($assigned->type) }}</span>
                                    @elseif($assigned->type==='transfer')
                                    <span class="badge bg-label-info me-1" style="font-size:10px;">{{ ucfirst($assigned->type) }}</span>
                                    @endif
                                    </td>

                                    <td>{{ $assigned->movement_date?->format('Y/m/d h:i A') }}</td>
                                    <td>{{ $assigned->note }}</td>

                                    <td>
                                        @if($assigned->is_last && $assigned->type === 'assigned' && $assigned->asset->status==='assigned')
                                        @if(edit(Auth::user()->role_ids,$active_menu_id))
   
                                            <div class="dropdown position-static">
                                                <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                
                                                    <a class="dropdown-item" href="javascript:void(0);" wire:click="assetReturn({{ $assigned->id }})"
                                                    ><i class="bx bx-edit-alt me-1 text-success"></i>{{ __('label.return') }}</a>
                                            
                                                </div>
                                            </div>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 justify-content-end px-3">
                        {{ $this->assignedAsset->links() }}
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('label.close') }}</button>
                </div>
            </div>
        </div>
    </div>
   
    <div class="modal fade" id="assetReturnModal" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog modal-lg" branch="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('label.asset_return') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <form  wire:submit.prevent="assetReturnStore" >
                    <div class="modal-body">
                        
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label">{{ __('label.return_date') }} <span style="color:red;">*</span></label>
                            <input type="date" id="nameBasic" class="form-control @error('return_date') is-invalid @enderror" wire:model.lazy="return_date">
                            @error('return_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label">{{ __('label.return_note') }}</label>
                            <textarea type="text" id="nameBasic" class="form-control @error('return_note') is-invalid @enderror" wire:model.lazy="return_note"></textarea>
                            @error('return_note') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" >{{ __('label.close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('label.return') }}</button>
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

        $('#assign_to_employee_id').off('change').on('change', function () {
            $wire.set('assign_to_employee_id', $(this).val());
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