
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
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="warehouse_id">
                                {{ __('label.warehouse') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="book_id">
                                {{ __('label.physical_book') }}
                            </th>
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="quantity">
                                {{ __('label.quantity') }}
                            </th>

                            @if(!auth()->user()->branch_id)
                            <th>{{ __('label.branch') }}</td>
                            @endif
                            
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($book_inventories as $i => $inventory)
                        <tr>
                            <td>{{ ($book_inventories->currentPage() - 1) * $book_inventories->perPage() + $i + 1 }}</td>
                            <td>{{ $inventory->warehouse?->name }}</td>
                            <td>{{ $inventory->book?->name }}</td>
                            <td>
                                @if($inventory->quantity == 0)
                                    <span class="badge bg-label-danger me-1">
                                        {{ $inventory->quantity }}
                                    </span>
                                @else
                                    <span class="badge bg-label-success me-1">
                                        {{ $inventory->quantity }}
                                    </span>
                                @endif
                            </td>
                            @if(!auth()->user()->branch_id) 
                            <td>{{ $inventory->warehouse?->branch?->name }}</td>
                            @endif 
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 justify-content-end px-3">
                {{ $book_inventories->links() }}
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