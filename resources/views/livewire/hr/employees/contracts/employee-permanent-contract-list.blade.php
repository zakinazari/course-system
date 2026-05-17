
<div>

    <div >
       
       <div class="card-header">
            
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="card-title mb-2 mb-md-0">
                    {{ __('label.permanent_contract') }}
                </h5>

                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                

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
                    <div class="col-md-3">
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
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="position_id">
                                {{ __('label.position') }}
                            </th>
                            
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="basic_salary">
                                {{ __('label.basic_salary') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="taxi_fare">
                                {{ __('label.taxi_fare') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="credit_card">
                                {{ __('label.credit_card') }}
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
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="section_id">
                                {{ __('label.section') }}
                            </th>
                            @if(!auth()->user()->branch_id)
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="branch_id">
                                {{ __('label.branch') }}
                            </th>
                            @endif
                            <th>{{ __('label.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($contracts as $i => $contract)
                        <tr>
                            <td>{{ ($contracts->currentPage() - 1) * $contracts->perPage() + $i + 1 }}</td>
                            <td>{{ $contract->position?->name }}</td>
                            <td>{{ $contract->basic_salary }}</td>
                            <td>{{ $contract->taxi_fare }}</td>
                            <td>{{ $contract->credit_card }}</td>
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
                            <td>{{ $contract->section?->name }}</td>
                            @if(!auth()->user()->branch_id)
                            <td>{{ $contract->branch?->name }}</td>
                            @endif
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
                    <h5 class="modal-title">@if($editMode) {{ __('label.editing') }}  @else {{ __('label.adding') }} @endif {{ __('label.permanent_contract') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <form @if($editMode) wire:submit.prevent="update" @else wire:submit.prevent="store" @endif>
                    <div class="modal-body">
                         @error('employee_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
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
                            <div class="col mb-3">
                                <label>{{ __('label.position') }}<span style="color:red;">*</span></label>
                                <div wire:ignore>
                                <select  class="form-control select2" id="permanet_form_position_id" wire:model.lazy ="position_id">
                                    <option value="">{{ __('label.select') }}</option>
                                    @foreach($positions as $position)
                                    <option value="{{ $position->id }}" wire:key="position-section">
                                        {{ $position->name }}
                                    </option>
                                    @endforeach
                                </select>
                                </div>
                               @error('position_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                             
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label>{{ __('label.section') }}<span style="color:red;">*</span></label>
                                <div wire:ignore>
                                <select  class="form-control select2" id="permanet_form_section_id" wire:model.lazy ="section_id">
                                    <option value="">{{ __('label.select') }}</option>
                                    @foreach($sections as $section)
                                    <option value="{{ $section->id }}" wire:key="section-section">
                                        {{ $section->name }}
                                    </option>
                                    @endforeach
                                </select>
                                </div>
                                @error('section_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col mb-3">
                                <label>{{ __('label.basic_salary') }}<span style="color:red;">*</span></label>
                                <input type="number" wire:model.lazy="basic_salary" class="form-control" min="0">
                                @error('basic_salary') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label>{{ __('label.taxi_fare') }}</label>
                                <input type="number" wire:model.lazy="taxi_fare" class="form-control" min="0">
                                @error('taxi_fare') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col mb-3">
                                <label>{{ __('label.credit_card') }}</label>
                                <input type="number" wire:model.lazy="credit_card" class="form-control" min="0">
                                @error('credit_card') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
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
                         <div class="row">
                            <div class="col mb-3">
                               <label class="form-label d-block">{{ __('label.status') }}</label>
                                <div class="form-check form-check-inline">
                                    <input name="status" 
                                        class="form-check-input" 
                                        type="radio" 
                                        id="status-active" 
                                        value="active" 
                                        
                                        wire:model.lazy="status"  @checked($status == 'active' || is_null($status))>
                                    <label class="form-check-label" for="status-active">{{ __('label.active') }}</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input name="status" 
                                        class="form-check-input" 
                                        type="radio" 
                                        id="status-inactive" 
                                        value="inactive" 
                                        wire:model.lazy="status"  @checked($status ==='inactive')>
                                    <label class="form-check-label" for="status-inactive">{{ __('label.inactive') }}</label>
                                </div>
                                @error('status') 
                                    <div class="invalid-feedback d-block">{{ $message }}</div> 
                                @enderror
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

        $('#permanet_form_position_id').off('change').on('change', function () {
            @this.set('position_id', $(this).val());
        });
        $('#permanet_form_section_id').off('change').on('change', function () {
            @this.set('section_id', $(this).val());
        });

    }

    initSelect2();

    Livewire.hook('morphed', () => {
        initSelect2();
    });

      Livewire.hook('message.processed', function (message, component) {
        const $modal = $('#{{$modalId}}');
        if ($modal.is(':visible')) {
            initSelect2();
        }
    });

    $(document).on('shown.bs.modal', function () {
        initSelect2();
    });
});
</script>
@endscript
