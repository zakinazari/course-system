
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
                    <div class="col-md-3">
                        <label class="form-label">{{ __('label.book_name') }}</label>
                        <input type="text" class="form-control" placeholder="" wire:model="search.name">
                    </div>
                    <div class="col-md-3" wire:ignore>
                        <label class="form-label">{{ __('label.program') }}</label>
                        <select class="form-select select2" wire:model.defer="search.program_id" id ="search_program_id">
                           <option value="">{{ __('label.all') }}</option>
                           @foreach($programs as $program)
                                 <option value="{{ $program->id }}"  wire:key="program-search-{{ $program->id }}">
                                    {{ $program->name }}
                                 </option>
                           @endforeach
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
                            <th>{{ __('label.NO') }}</th>
                            <th>{{ __('label.book_name') }}</th>
                            <th>{{ __('label.abbreviation') }}</th>
                            <th>{{ __('label.program') }}</th>
                            <th>{{ __('label.level_number') }}</th>
                            <th>{{ __('label.status') }}</th>
                            <th>{{ __('label.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($books as $i => $book)
                        <tr>
                            <td>{{ ($books->currentPage() - 1) * $books->perPage() + $i + 1 }}</td>
                            <td>{{ $book->name }}</td>
                            <td>{{ $book->abbreviation }}</td>
                            <td>{{ $book->program?->name }}</td>
                            <td>{{ $book->level_number }}</td>
                            <td>
                                @if($book->status ==='active' ) 
                                    <span class="badge rounded-pill bg-success">{{ __('label.active') }}</span>
                                @else
                                    <span class="badge rounded-pill bg-danger">{{ __('label.inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown position-static">
                                    <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if(edit(Auth::user()->role_ids,$active_menu_id))
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="edit({{ $book->id }})"
                                            ><i class="bx bx-edit-alt me-1 text-success"></i>{{ __('label.edit') }}</a>
                                        @endif
                                        @if(delete(Auth::user()->role_ids,$active_menu_id))
                                            <a class="dropdown-item " href="javascript:void(0);"  onclick="confirmDelete({{ $book->id }},'{{$table_name}}')"
                                            ><i class="bx bx-trash me-1 text-danger"></i>{{ __('label.delete') }}</a>
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
                {{ $books->links() }}
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
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.book_name') }} <span style="color:red;">*</span></label>
                                <input type="text" id="nameBasic" class="form-control @error('name') is-invalid @enderror" wire:model.lazy="name">
                                @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                             <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.abbreviation') }} <span style="color:red;">*</span></label>
                                <input type="text" id="nameBasic" class="form-control @error('abbreviation') is-invalid @enderror" wire:model.lazy="abbreviation">
                                @error('abbreviation') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                           <div class="col mb-3" wire:ignore>
                              <label class="form-label">{{ __('label.program') }} <span style="color:red;">*</span></label>
                              <select class="form-select select2" wire:model="program_id" id ="program_id">
                                 <option value="">{{ __('label.select') }}</option>
                                 @foreach($programs as $program)
                                       <option value="{{ $program->id }}"  wire:key="program-add-edit-{{ $program->id }}">
                                          {{ $program->name }}
                                       </option>
                                 @endforeach
                              </select>
                           </div>
                           @error('program_id')
                              <div class="invalid-feedback d-block">{{ $message }}</div>
                           @enderror

                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.fee') }} <span style="color:red;">*</span></label>
                                <input type="text" id="nameBasic" class="form-control @error('fee') is-invalid @enderror" wire:model.lazy="fee">
                                @error('fee') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.pass_mark') }} <span style="color:red;">*</span></label>
                                <input type="text" id="nameBasic" class="form-control @error('pass_mark') is-invalid @enderror" wire:model.lazy="pass_mark">
                                @error('pass_mark') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.makeup_mark') }} <span style="color:red;">*</span></label>
                                <input type="text" id="nameBasic" class="form-control @error('makeup_mark') is-invalid @enderror" wire:model.lazy="makeup_mark">
                                @error('makeup_mark') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.total_teaching_days') }} <span style="color:red;">*</span></label>
                                <input type="number" min="1" id="nameBasic" class="form-control @error('total_teaching_days') is-invalid @enderror" wire:model.lazy="total_teaching_days">
                                @error('total_teaching_days') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                          
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.min_capacity') }} <span style="color:red;">*</span></label>
                                <input type="number" min="1" id="nameBasic" class="form-control @error('min_capacity') is-invalid @enderror" wire:model.lazy="min_capacity">
                                @error('min_capacity') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.max_capacity') }} <span style="color:red;">*</span></label>
                                <input type="number" min="1" id="nameBasic" class="form-control @error('max_capacity') is-invalid @enderror" wire:model.lazy="max_capacity">
                                @error('max_capacity') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.exam_fine_amount') }} <span style="color:red;">*</span></label>
                                <input type="number" min="1" id="nameBasic" class="form-control @error('exam_fine_amount') is-invalid @enderror" wire:model.lazy="exam_fine_amount">
                                @error('exam_fine_amount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.level_number') }} <span style="color:red;">*</span></label>
                                <input type="number" min="1" id="nameBasic" class="form-control @error('level_number') is-invalid @enderror" wire:model.lazy="level_number">
                                @error('level_number') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.drop_days') }} <span style="color:red;">*</span></label>
                                <input type="number" min="1" id="nameBasic" class="form-control @error('drop_days') is-invalid @enderror" wire:model.lazy="drop_days">
                                @error('drop_days') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="card mb-3 ">
                            <div class="card-header">
                                <strong>{{ __('label.exam_type') }}</strong> <span style="color:red;">*</span>
                            </div>

                            <div class="card-body ">
                                <table class="table table-bordered mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width: 60px;">#</th>
                                            <th>{{ __('label.exam_type') }}</th>
                                            <th style="width: 150px;">{{ __('label.percentage') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($exam_types as $type)
                                            <tr>
                                                <!-- Checkbox -->
                                                <td class="text-center">
                                                    <input class="form-check-input"
                                                        type="checkbox"
                                                        wire:model.lazy="exam_type_ids"
                                                        value="{{ $type->id }}">
                                                </td>

                                                <!-- Name -->
                                                <td>
                                                    {{ $type->name }}
                                                </td>

                                                <!-- Percentage -->
                                                <td>
                                                    <input type="number"
                                                        class="form-control"
                                                        placeholder="%"
                                                        min="0" max="100"
                                                        wire:model.lazy="percentages.{{ $type->id }}">
                                                    
                                                    @error("percentages.{$type->id}")
                                                        <div class="text-danger small">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <!-- Errors -->
                                <div class="p-3">
                                    @error('exam_type_ids')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror

                                    <div class="mt-2">
                                        <strong>{{ __('label.total') }}: {{ array_sum($percentages ?? []) }}%</strong>
                                    </div>

                                    @if($errors->has('percentages_total'))
                                        <div class="invalid-feedback d-block">
                                            {{ $errors->first('percentages_total') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- -------------------------special discounts--------------------- -->
                        <div class="card mb-3">
                        <div class="card-header">
                            <strong>Special Discounts</strong>
                        </div>

                        <div class="card-body">

                            @foreach($book_special_discount_types as $type)

                                <div class="border rounded p-3 mb-3">

                                    <h6 class="mb-2 text-capitalize">
                                        {{ str_replace('_', ' ', $type) }}
                                    </h6>

                                    <div class="row">

                                        {{-- amount --}}
                                        <div class="col-md-6">
                                            <label>Amount</label>
                                            <input type="number"
                                                class="form-control"
                                                wire:model.lazy="book_special_discounts.{{ $type }}.amount">
                                        </div>

                                        {{-- duration --}}
                                        <div class="col-md-6">
                                            <label>Duration (days)</label>
                                            <input type="number"
                                                class="form-control"
                                                wire:model.lazy="book_special_discounts.{{ $type }}.duration_days">
                                        </div>

                                    </div>

                                </div>

                            @endforeach

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

        $('#search_program_id').off('change').on('change', function () {
            @this.set('search.program_id', $(this).val());
        });

        $('#program_id')
        .off('change')
        .on('change', function () {
            $wire.set('program_id', $(this).val());
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