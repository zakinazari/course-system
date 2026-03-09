
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
                    <div class="col-md-3">
                        <label class="form-label">{{ __('label.name') }}</label>
                        <input type="text" class="form-control" placeholder="" wire:model="search.name">
                    </div>
                     @if(!auth()->user()->branch_id)
                    <div class="col-md-2">
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
                    <div class="col-md-2">
                        <label class="form-label">{{ __('label.status') }}</label>
                        <select class="form-select" wire:model.defer="search.status" id ="">
                           <option value="">{{ __('label.all') }}</option>
                            <option value="pending">{{ __('label.pending') }}</option>
                            <option value="done">{{ __('label.done') }}</option>
                            <option value="canceled">{{ __('label.cancelled') }}</option>
                        </select>
                     </div>
                     <div class="col-md-3" wire:ignore>
                        <label for="search.reference_id" class="form-label">{{ __('label.meeting_reference') }}</label>
                        <select class="form-select select2 @error('search.reference_id') is-invalid @enderror" wire:model.defer="search.reference_id" id ="search_reference_id">
                            <option value="">{{ __('label.all') }}</option>
                            @foreach($references as $reference)
                                    <option value="{{ $reference->id }}"  wire:key="reference-search-{{ $reference->id }}">
                                        {{ $reference->name }}
                                            ({{ $reference->role?->role_name }})
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
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="name">
                                {{ __('label.name') }}
                            </th>

                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="last_name">
                                {{ __('label.last_name') }}
                            </th>

                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="father_name">
                                {{ __('label.father_name') }}
                            </th>

                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="phone_no">
                                {{ __('label.phone_no') }}
                            </th>

                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="subject">
                                {{ __('label.subject') }}
                            </th>

                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="date">
                                {{ __('label.date') }}
                            </th>

                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="status">
                                {{ __('label.status') }}
                            </th>

                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="reference_id">
                                {{ __('label.meeting_reference') }}
                            </th>
                            @if(!auth()->user()->branch_id)
                            <th>
                                <input class="form-check-input" type="checkbox" wire:model="selectedFields" value="branch_id">
                                {{ __('label.branch') }}
                            </th>
                            @endif
                            <th>
                                {{ __('label.actions') }}
                            </th>
                        </tr>

                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($meetings as $i => $meeting)
                        <tr @if($highlightMeetingId == $meeting->id) class="table-warning" @endif>
                            <td>{{ ($meetings->currentPage() - 1) * $meetings->perPage() + $i + 1 }}</td>
                            <td>{{ $meeting->name }}</td>
                            <td>{{ $meeting->last_name }}</td>
                            <td>{{ $meeting->father_name }}</td>
                            <td>{{ $meeting->phone_no }}</td>
                            <td>{{ $meeting->subject }}</td>
                            <td>{{ $meeting->date->format('Y/m/d - h:i A') }}</td>
                            <td>
                                @if($meeting->status=='pending')
                                <span class="badge bg-label-warning me-1" style="font-size:10px;">{{ ucfirst($meeting->status) }}</span>
                                @elseif($meeting->status=='done')
                                <span class="badge bg-label-success me-1" style="font-size:10px;">{{ ucfirst($meeting->status) }}</span>
                                @elseif($meeting->status=='canceled')
                                <span class="badge bg-label-danger me-1" style="font-size:10px;">{{ ucfirst($meeting->status) }}</span>
                                @endif
                            </td>
                            <td>{{ $meeting->reference?->name }}</td>
                            @if(!auth()->user()->branch_id)
                            <td>{{ $meeting->branch?->name }}</td>
                            @endif
                            <td>
                                <div class="dropdown position-static">
                                    <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if($meeting->status == 'pending' && confirm(Auth::user()->role_ids,$active_menu_id))
                                        
                                            <a class="dropdown-item" href="javascript:void(0);" 
                                            wire:click="openStatusModal({{ $meeting->id }}, 'done')">
                                                <i class="bx bx-check-circle me-1 text-success"></i>{{ __('label.done') }}
                                            </a>

                                            <a class="dropdown-item" href="javascript:void(0);" 
                                            wire:click="openStatusModal({{ $meeting->id }}, 'canceled')">
                                                <i class="bx bx-x-circle me-1 text-danger"></i>{{ __('label.cancel') }}
                                            </a>
                                        @endif


                                        @if(edit(Auth::user()->role_ids,$active_menu_id))
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="edit({{ $meeting->id }})"
                                            ><i class="bx bx-edit-alt me-1 text-success"></i>{{ __('label.edit') }}</a>
                                        @endif
                                        @if(delete(Auth::user()->role_ids,$active_menu_id))
                                            <a class="dropdown-item " href="javascript:void(0);"  onclick="confirmDelete({{ $meeting->id }},'{{$table_name}}')"
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
                {{ $meetings->links() }}
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
                        <div class="row" wire:ignore>
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.visitor') }} </label>
                                <select id="visitor_id" class="form-select select2" wire:model.lazy ="visitor_id">
                                    <option value="">Select from Visitor</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.name') }} <span style="color:red;">*</span></label>
                                <input type="text" id="nameBasic" class="form-control @error('name') is-invalid @enderror" wire:model.lazy="name" @if(!$editMode) readonly @endif>
                                @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.last_name') }}</label>
                                <input type="text" id="nameBasic" class="form-control @error('last_name') is-invalid @enderror" wire:model.lazy="last_name" @if(!$editMode) readonly @endif>
                                @error('last_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.father_name') }} <span style="color:red;">*</span></label>
                                <input type="text" id="nameBasic" class="form-control @error('father_name') is-invalid @enderror" wire:model.lazy="father_name" @if(!$editMode) readonly @endif>
                                @error('father_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.phone_no') }}</label>
                                <input type="text" id="nameBasic" class="form-control @error('phone_no') is-invalid @enderror" wire:model.lazy="phone_no" @if(!$editMode) readonly @endif>
                                @error('phone_no') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.subject') }} <span style="color:red;">*</span></label>
                                <textarea type="text" id="nameBasic" class="form-control @error('subject') is-invalid @enderror" wire:model.lazy="subject"></textarea>
                                @error('subject') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                          <div class="col mb-3" wire:ignore>
                                <label for="reference_id" class="form-label">{{ __('label.meeting_reference') }} <span style="color:red;">*</span></label>
                                <select class="form-select select2 @error('reference_id') is-invalid @enderror" wire:model.lazy="reference_id" id ="reference_id">
                                 <option value="">{{ __('label.select') }}</option>
                                  @foreach($references as $reference)
                                            <option value="{{ $reference->id }}"  wire:key="reference-{{ $reference->id }}">
                                                {{ $reference->name }}
                                                 ({{ $reference->role?->role_name }})
                                            </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('reference_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.date') }} <span style="color:red;">*</span></label>
                                <input type="datetime-local" id="nameBasic" class="form-control @error('date') is-invalid @enderror" wire:model.lazy="date">
                                @error('date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
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
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" >{{ __('label.close') }}</button>
                        <button type="submit" class="btn btn-primary">@if($editMode) {{ __('label.update') }}  @else {{ __('label.save') }} @endif</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
   @if($showStatusModal)
<div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Status Change</h5>
                <button type="button" class="btn-close" wire:click="$set('showStatusModal', false)"></button>
            </div>

            <div class="modal-body">
                Are you sure you want to change the status to 
                <strong>{{ ucfirst($newStatus) }}</strong> ?
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary"
                        wire:click="$set('showStatusModal', false)">
                    Cancel
                </button>

                <button class="btn btn-primary"
                        wire:click="updateStatus">
                    Yes, Confirm
                </button>
            </div>
        </div>
    </div>
</div>
@endif
</div>

@script
<script>
document.addEventListener("livewire:initialized", function () {

    function initVisitorSelect() {

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

        $('#search_reference_id').off('change').on('change', function () {
            @this.set('search.reference_id', $(this).val());
        });
        $('#reference_id').off('change').on('change', function () {
            @this.set('reference_id', $(this).val());
        });



        let $visitor = $('#visitor_id');

        if (!$visitor.length) return;

        if ($visitor.hasClass('select2-hidden-accessible')) {
            $visitor.select2('destroy');
        }

        let modalId = @js($modalId);
        $visitor.select2({
            dropdownParent: $('#' + modalId),
            width: '100%',
            minimumInputLength: 2,
            ajax: {
                url: '/search-visitors',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                }
            }
        });

        $visitor.off('select2:select').on('select2:select', function (e) {
            let data = e.params.data;

            $wire.set('visitor_id', data.id);
            $wire.call('setInfo', data.id);
        });

    }

    initVisitorSelect();

    Livewire.hook('morphed', () => {
        initVisitorSelect();
    });


    $(document).on('shown.bs.modal', function () {
        initVisitorSelect();
    });

});

</script>
@endscript


