<div>
  <style>
    .datepicker-plot-area { z-index: 99999999 !important; }
</style>
    <!-- title -->
    @section('title',
    (
        ($active_menu?->parent?->name ?? '') 
        ? $active_menu?->parent?->name . '-' 
        : ''
    ) 
    . $active_menu?->name
    . ' | ' . __('label.app_name')
    )
    <!-- end title -->
    <!-- start header -->
    <h4 class="py-3 breadcrumb-wrapper mb-4">
    @if(!empty($active_menu?->grandParent?->name))
        <span class="text-muted fw-light"> {{ $active_menu?->grandParent?->name }} /</span>
    @endif
    @if(!empty($active_menu?->parent?->name))
        <span class="text-muted fw-light"> {{ $active_menu?->parent?->name }} /</span>
    @endif
    {{ $active_menu?->name }}  
    </h4>
    <!-- end header -->


    <div class="card">
       
        <div class="card-header d-flex justify-content-between align-items-center">

            <h5 class="card-title mb-0">{{ $active_menu?->name }}</h5>

            <div class="d-flex align-items-center gap-2">
                
                @if(add(Auth::user()->role_ids,$active_menu_id))
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#{{$modalId}}" wire:click="openModal">
                            <i class="bi bi-plus-lg"></i> {{ __('label.add_new_record') }} 
                        </button>
                    </div>
                @endif
            </div>

        </div>
        <hr>
        <div class="table-responsive text-nowrap">
 
            <div class="mb-3 px-3">
                <form wire:submit.prevent="applySearch" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <input type="text" class="form-control" placeholder="{{ __('label.title') }}" wire:model="search.identity">
                    </div>
                    <div class="col-md-3" wire:ignore>
                        <label class="form-label">{{ __('label.gazette') }}</label>
                        <select class="form-control select2"
                            wire:model.defer="search.gazette" id ="search_gazette">
                            <option value="">{{ __('label.all') }}</option>
                            @foreach($gazettes as $gazette)
                                <option value="{{ $gazette->id }}">
                                    @if(App::getLocale()=='fa')
                                        {{ $gazette->title_fa }}
                                    @else
                                        {{ $gazette->title_en }}
                                    @endif
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
                            <th>{{ __('label.title') }}</th>
                            <th>{{ __('label.ruling_order.order_number') }}</th>
                            <th>{{ __('label.ruling_order.order_date') }}</th>
                            <th>{{ __('label.gazette_number') }}</th>
                            <th>{{ __('label.files') }}</th>
                            <th>{{ __('label.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($orders as $i => $order)
                        <tr>
                            <td>{{ ($orders->currentPage() - 1) * $orders->perPage() + $i + 1 }}</td>
                            <td>
                                 @if(App::getLocale()=='fa') {{ $order->title_fa }} @else {{ $order->title_en }} @endif 
                            </td>
                            <td> {{ $order->ruling_number }} </td>
                            <td> {{ hijri::date($order->ruling_date) }} </td>
                            <td> {{ $order->gazette?->gazette_number }} </td>
                            <td>
                                <button type="button" class="btn btn-success btn-icon rounded-pill" wire:click="showFiles({{ $order->id }})">
                                    <i class="bx bx-show"></i>
                                </button>
                           </td>
                           
                            <td>
                                <div class="dropdown position-static">
                                    <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if(edit(Auth::user()->role_ids,$active_menu_id))
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="edit({{ $order->id }})"
                                            ><i class="bx bx-edit-alt me-1 text-success"></i>{{ __('label.edit') }}</a>
                                        @endif
                                        @if(delete(Auth::user()->role_ids,$active_menu_id))
                                            <a class="dropdown-item " href="javascript:void(0);"  onclick="confirmDelete({{ $order->id }},'{{$table_name}}')"
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
                {{ $orders->links() }}
            </div>
        </div>
    </div>
    
    <!--/ Bootstrap Table with Header Dark -->
    <div class="modal fade" id="{{$modalId}}" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@if($editMode) {{ __('label.editing') }}  @else {{ __('label.adding') }} @endif {{ $active_menu?->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <form @if($editMode) wire:submit.prevent="update" @else wire:submit.prevent="store" @endif>
                    <div class="modal-body">
                        
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.title',locale:'fa') }}</label>
                                <input type="text" id="nameBasic" class="form-control @error('title_fa') is-invalid @enderror" wire:model.lazy="title_fa">
                                @error('title_fa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.title',locale:'pa') }}</label>
                                <input type="text" id="nameBasic" class="form-control @error('title_en') is-invalid @enderror" wire:model.lazy="title_en">
                                @error('title_en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.ruling_order.order_number') }}</label>
                                <input type="text" id="nameBasic" class="form-control @error('order_number') is-invalid @enderror" wire:model.lazy="order_number">
                                @error('order_number') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                         
                            <div class="col mb-3">
                                <label class="form-label">{{ __('label.ruling_order.order_date') }}</label>

                                <input type="text"
                                    class="form-control @error('order_date') is-invalid @enderror"
                                    placeholder="{{ hijri::Date('Y/m/d') }}"
                                    wire:model.defer="order_date">
                                @error('order_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3" wire:ignore>
                                <label class="form-label">{{ __('label.gazette') }}</label>
                                <select class="form-control select2 @error('gazette_id') is-invalid @enderror"
                                    wire:model.lazy="gazette_id" id="gazette_id">
                                    <option value="">{{ __('label.all') }}</option>
                                    @foreach($gazettes as $gazette)
                                        <option value="{{ $gazette->id }}">
                                            @if(App::getLocale()=='fa')
                                                {{ $gazette->title_fa }}
                                            @else
                                                {{ $gazette->title_en }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('gazette_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="fileInput" class="form-label">{{ __('label.files') }}</label>
                                <div class="input-group mb-2">
                                    <input type="file" class="form-control @error('files.*') is-invalid @enderror" id="fileInput"  wire:model.lazy="files">
                                </div>
                                @error('files.*')<span class="text-danger">{{ $message }}</span>@enderror
                                @error('files')<span class="text-danger">{{ $message }}</span>@enderror
                                <div id="fileError" class="invalid-feedback d-block" style="display:none;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer" >
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('label.close') }}</button>
                        <button type="submit" class="btn btn-primary">@if($editMode) {{ __('label.update') }} @else {{ __('label.save') }} @endif</button>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="modalShowFiles" tabindex="-1" style="display: none;" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel1">{{ __('label.files') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($existing_files)
                <div class="mt-3">
                    <ul class="list-group">
                        @foreach($existing_files as $f)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @php
                                    $ext = strtolower(pathinfo($f->file_name, PATHINFO_EXTENSION));
                                    switch ($ext) {
                                        case 'pdf':
                                            $icon = '📄'; 
                                            break;
                                        case 'doc':
                                        case 'docx':
                                            $icon = '📝'; 
                                            break;
                                        default:
                                            $icon = '📁'; 
                                            break;
                                    }
                                @endphp
                                <span>
                                    <span class="me-2">{{ $icon }}</span>
                                    <span>{{ $f->file_name }}</span>&nbsp;&nbsp;&nbsp;<small></small>
                                </span>
                                
                                <div class="d-flex gap-2">
                                    <a wire:click.prevent="downloadFile({{ $f->id }})" class="btn btn-xs btn-secondary text-white">
                                        <i class="bx bx-download me-1"></i> {{ __('label.download') }}
                                    </a>
                                    <button onclick="confirmDelete({{ $f->id }},'order_files')" type="button" class="btn btn-icon btn-label-danger">
                                    <i class="bx bx-trash-alt"></i>
                                    </button>
                                </div>

                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                {{ __('label.close') }}
            </button>
            </div>
        </div>
        </div>
    </div>
</div>
<script>
 const input = document.getElementById('fileInput');
    const errorDiv = document.getElementById('fileError');
    const maxLengthEnglish = 120;
    const maxLengthPersian = 60;

    input.addEventListener('change', function() {
        const file = this.files[0];
        if(file){
            const isPersian = /[\u0600-\u06FF]/.test(file.name); 
            const maxLength = isPersian ? maxLengthPersian : maxLengthEnglish;

            if(file.name.length > maxLength){
                errorDiv.textContent = "{{ __('label.file_name_too_long') }}".replace(':max', maxLength);
                errorDiv.style.display = 'block';
                this.value = ''; 
            } else {
                errorDiv.style.display = 'none';
            }
        }
    });

</script>
@script
<script>
document.addEventListener("livewire:initialized", function () {

    function initSelect2() {

        $('.select2').each(function () {
            const $select = $(this);
            const $modal  = $select.closest('.modal');

            // جلو گیری از تکرار init
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }

            $select.select2({
                width: '100%',
                dropdownParent: $modal.length ? $modal : $(document.body)
            });
        });

        
        $('#search_gazette')
            .off('change')
            .on('change', function () {
                $wire.set('search.gazette', $(this).val());
            });

        $('#gazette_id')
        .off('change')
        .on('change', function () {
            $wire.set('gazette_id', $(this).val());
        });
    }

    initSelect2();

    // بعد از هر re-render لایووایر
    Livewire.hook('morphed', () => {
        initSelect2();
    });

    // وقتی مودال باز می‌شود
    $(document).on('shown.bs.modal', function () {
        initSelect2();
    });

});
</script>
@endscript