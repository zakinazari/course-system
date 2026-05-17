
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

                    <div class="col-md-3" wire:ignore>
                        <label class="form-label">{{ __('label.account') }}</label>
                        <select class="form-select select2" wire:model.defer="search.account_id" id ="search_account_id">
                        <option value="">{{ __('label.all') }}</option>
                        @foreach($accounts as $accs)
                                <option value="{{ $accs->id }}"  wire:key="account-search-{{ $accs->id }}">
                                    {{ $accs->name }}
                                </option>
                        @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" >{{ __('label.fund_type') }}</label>
                        <select wire:model="search.type" class="form-control">
                            <option value="">{{ __('label.select') }}</option>
                            <option value="opening_balance">{{ __('label.opening_balance') }}</option>
                            <option value="capital_injection">{{ __('label.capital_injection') }}</option>
                            <option value="loan_received">{{ __('label.loan_received') }}</option>
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
                            <th>{{ __('label.account') }}</th>
                            <th>{{ __('label.amount') }}</th>
                            <th>{{ __('label.transaction_date') }}</th>
                            <th>{{ __('label.fund_type') }}</th>
                            <th>{{ __('label.note') }}</th>
                            <th>{{ __('label.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($external_funds as $i => $ef)
                        <tr>
                            <td>{{ ($external_funds->currentPage() - 1) * $external_funds->perPage() + $i + 1 }}</td>
                            <td>{{ $ef->account?->name }}</td>
                            <td>{{ $ef->amount }}</td>
                            <td>{{ $ef->transaction_date->format('Y/m/d') }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $ef->category)) }}</td>
                            <td>{{ $ef->note }}</td>
                            <td>
                                <div class="dropdown position-static">
                                    <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if(edit(Auth::user()->role_ids,$active_menu_id))
                                            @if((Auth::user()->id === $ef->created_by && $ef->created_at->format('Y/m/d') == now()->format('Y/m/d')) || Auth::user()->isAdmin() || Auth::user()->isDeveloper())
                                                <a class="dropdown-item" href="javascript:void(0);" wire:click="edit({{ $ef->id }})"
                                                ><i class="bx bx-edit-alt me-1 text-success"></i>{{ __('label.edit') }}</a>
                                            @endif
                                        @endif
                                        @if(delete(Auth::user()->role_ids,$active_menu_id))
                                            @if((Auth::user()->id === $ef->created_by && $ef->created_at->format('Y/m/d') == now()->format('Y/m/d')) || Auth::user()->isAdmin() || Auth::user()->isDeveloper())
                                                <a class="dropdown-item " href="javascript:void(0);"  onclick="confirmDelete({{ $ef->id }},'{{$table_name}}')"
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
                {{ $external_funds->links() }}
            </div>
        </div>
    </div>
    
    <!--/ Bootstrap Table with Header Dark -->
    <div class="modal fade" id="{{$modalId}}" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog" branch="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@if($editMode) {{ __('label.editing') }}  @else {{ __('label.adding') }} @endif @if(App::getLocale() =='en') {{ $active_menu?->name_en }} @elseif(App::getLocale()=='fa') {{ $active_menu?->name }} @endif</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <form @if($editMode) wire:submit.prevent="update" @else wire:submit.prevent="store" @endif>
                    <div class="modal-body">
                        @if(!$editMode)
                        <div class="col mb-3" wire:ignore>
                            <label class="form-label">{{ __('label.account') }} <span style="color:red;">*</span></label>
                            <select class="form-select select2" wire:model="account_id" id ="account_id">
                                <option value="">{{ __('label.select') }}</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}"  wire:key="section-add-edit-{{ $account->id }}">
                                        {{ $account->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('account_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        @endif
                        <div class="col mb-3">
                            <label>{{ __('label.amount') }}<span style="color:red;">*</span></label>
                            <input type="number" wire:model.lazy="amount" class="form-control" min="0" step="any">
                            @error('amount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        @if(!$editMode)
                         <div class="mb-3">
                            <label>{{ __('label.fund_type') }} <span style="color:red;">*</span></label>
                            <select wire:model="type" class="form-control">
                                <option value="">{{ __('label.select') }}</option>
                                <option value="opening_balance">{{ __('label.opening_balance') }}</option>
                                <option value="capital_injection">{{ __('label.capital_injection') }}</option>
                                <option value="loan_received">{{ __('label.loan_received') }}</option>
                            </select>

                            @error('type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        @endif

                        <div class="col mb-3" wire:ignore>
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
        
                        <div class="row">
                            <div class="mb-3">
                                <label>{{ __('label.note') }}</label>
                                <textarea type="text" wire:model="note" class="form-control"></textarea>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" >{{ __('label.close') }}</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">@if($editMode) {{ __('label.update') }}  @else {{ __('label.save') }} @endif</button>
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

        $('#search_account_id').off('change').on('change', function () {
            @this.set('search.account_id', $(this).val());
        });

        $('#account_id').off('change').on('change', function () {
            $wire.set('account_id', $(this).val());
        });

        $('#section_id').off('change').on('change', function () {
            $wire.set('section_id', $(this).val());
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


