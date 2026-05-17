
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
                   
                    <div class="col-md-3 d-flex flex-column" wire:ignore>
                        <label class="form-label">{{ __('label.warehouse') }}</label>
                        <select  class="form-select select2" id ="search_warehouse_id">
                           <option value="">{{ __('label.all') }}</option>
                           @foreach($warehouses as $warehouse)
                                 <option value="{{ $warehouse->id }}"  wire:key="warehouse-search-{{ $warehouse->id }}">
                                    {{ $warehouse->name }}
                                 </option>
                           @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex flex-column" wire:ignore>
                        <label class="form-label">{{ __('label.physical_book') }}</label>
                        <select  class="form-select select2" id ="search_book_id">
                           <option value="">{{ __('label.all') }}</option>
                           @foreach($books as $book)
                                 <option value="{{ $book->id }}"  wire:key="book-search-{{ $book->id }}">
                                    {{ $book->name }}
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
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="warehouse_id">
                                {{ __('label.warehouse') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="book_id">
                                {{ __('label.physical_book') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="quantity_change">
                                {{ __('label.quantity') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="balance_after">
                                {{ __('label.balance_after') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="unit_price">
                                {{ __('label.unit_price') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="created_at">
                                {{ __('label.date') }}
                            </th>
                            <th>{{ __('label.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($purchases as $i => $purchase)
                        <tr>
                            <td>{{ ($purchases->currentPage() - 1) * $purchases->perPage() + $i + 1 }}</td>
                            <td>{{ $purchase->inventory?->warehouse?->name }}</td>
                            <td>{{ $purchase->inventory?->book?->name }}</td>
                            <td>{{ $purchase->quantity_change }}</td>
                            <td>{{ $purchase->balance_after }}</td>
                            <td>{{ $purchase->unit_price }}</td>
                            <td>{{ $purchase->created_at->format('Y/m/d H:i:A') }}</td>
                            <td>
                                
                                <div class="dropdown position-static">
                                    <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if(edit(Auth::user()->role_ids,$active_menu_id))
                                        @if((Auth::user()->id === $purchase->user_id && $purchase->created_at->format('Y/m/d') == now()->format('Y/m/d')) || Auth::user()->isAdmin() || Auth::user()->isDeveloper())
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="edit({{ $purchase->id }})"
                                            ><i class="bx bx-edit-alt me-1 text-success"></i>{{ __('label.edit') }}</a>
                                        @endif
                                        @endif
                                   
                                        @if(delete(Auth::user()->role_ids,$active_menu_id))
                                            @if((Auth::user()->id === $purchase->user_id && $purchase->created_at->format('Y/m/d') == now()->format('Y/m/d')) || Auth::user()->isAdmin() || Auth::user()->isDeveloper())
                                            <a class="dropdown-item " href="javascript:void(0);"  onclick="confirmDelete({{ $purchase->id }},'{{$table_name}}')"
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
                {{ $purchases->links() }}
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
                        @if(!$editMode)
                           <div class="mb-3">
                                <label>{{ __('label.warehouse') }}<span style="color:red;">*</span></label>
                                <select wire:model.lazy="warehouse_id" class="form-control">
                                    <option value="">{{ __('label.select') }}</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">
                                            {{ $warehouse->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('warehouse_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3" >
                                <label>{{ __('label.physical_book') }}<span style="color:red;">*</span></label>
                                <select class="form-select select2"  wire:model.lazy="book_id" id="book_id">
                                    <option value="">{{ __('label.select') }}</option>
                                    @foreach($books as $book)
                                        <option value="{{ $book->id }}" wire:key="book-{{ $book->id }}">
                                            {{ $book->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('book_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>{{ __('label.quantity') }}<span style="color:red;">*</span></label>
                                <input type="number" wire:model.lazy="quantity" class="form-control" min="0">
                                @error('quantity') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>{{ __('label.unit_price') }}<span style="color:red;">*</span></label>
                                <input type="number" wire:model.lazy="unit_price" class="form-control" min="0">
                                @error('unit_price') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
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

        $('#book_id').off('change').on('change', function () {
            $wire.set('book_id', $(this).val());
        });

        $('#search_book_id').off('change').on('change', function () {
            $wire.set('search.book_id', $(this).val());
        });

        $('#search_warehouse_id').off('change').on('change', function () {
            $wire.set('search.warehouse_id', $(this).val());
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