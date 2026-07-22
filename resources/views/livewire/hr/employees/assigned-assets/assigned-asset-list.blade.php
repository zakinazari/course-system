
<div>

    <div >
       
       <div class="card-header">
            
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="card-title mb-2 mb-md-0">
                    {{ __('label.assigned_assets') }}
                </h5>

             

            </div>
        </div>
        <hr>
        <div class="text-nowrap">
 
            <div class="mb-3 px-3">
                
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
                            <th style="width:40px;">{{ __('label.NO') }}</th>
                            <th>{{ __('label.asset') }}</th>
                            <th>{{ __('label.status') }}</th>
                            <th>{{ __('label.assigned_date') }}</th>
                            <th>{{ __('label.note') }}</th>
                            <!-- <th>{{ __('label.actions') }}</th> -->
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($assigned_asset as $i => $asset)
                        <tr>
                            <td>{{ ($assigned_asset->currentPage() - 1) * $assigned_asset->perPage() + $i + 1 }}</td>
                            <td>{{ $asset->asset?->name }}</td>
                            
                            <td>
                              @if($asset->type==='assigned')
                              <span class="badge bg-label-danger me-1" style="font-size:10px;">{{ ucfirst($asset->type) }}</span>
                              @elseif($asset->type==='returned')
                              <span class="badge bg-label-success me-1" style="font-size:10px;">{{ ucfirst($asset->type) }}</span>
                              @elseif($asset->type==='transfer')
                              <span class="badge bg-label-info me-1" style="font-size:10px;">{{ ucfirst($asset->type) }}</span>
                              @endif
                            </td>

                            <td>{{ $asset->movement_date?->format('Y/m/d h:i A') }}</td>
                            <td>{{ $asset->note }}</td>

                           {{-- <td>
                                @if(edit(Auth::user()->role_ids,$active_menu_id) && $asset->type==='assigned' && $asset->asset->status==='assigned')
                                    @if((Auth::user()->id === $asset->user_id && $asset->created_at->format('Y/m/d') == now()->format('Y/m/d')) || Auth::user()->isAdmin() || Auth::user()->isDeveloper())
                                    <div class="dropdown position-static">
                                        <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                        
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="edit({{ $asset->id }})"
                                            ><i class="bx bx-edit-alt me-1 text-success"></i>{{ __('label.return') }}</a>
                                    
                                        </div>
                                    </div>
                                    @endif
                                @endif
                            
                            </td>
                             --}}
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 justify-content-end px-3">
                {{ $assigned_asset->links() }}
            </div>
        </div>
    </div>
    
    <!--/ Bootstrap Table with Header Dark -->
    <div class="modal fade" id="{{$modalId}}" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog modal-lg" branch="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('label.asset_return') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <form @if($editMode) wire:submit.prevent="update" @else wire:submit.prevent="store" @endif>
                    <div class="modal-body">
                        
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label">{{ __('label.return_date') }} <span style="color:red;">*</span></label>
                            <input type="date" id="nameBasic" class="form-control @error('return_date') is-invalid @enderror" wire:model.lazy="return_date">
                            @error('return_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label">{{ __('label.note') }}</label>
                            <textarea type="text" id="nameBasic" class="form-control @error('note') is-invalid @enderror" wire:model.lazy="note"></textarea>
                            @error('note') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
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


        $('#asset_form_section_id').off('change').on('change', function () {
            @this.set('section_id', $(this).val());
            
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