
<div>
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

            <h5 class="card-title mb-0">{{ $active_menu?->name }} </h5>
        </div>
        <hr>
        <div class="table-responsive text-nowrap">
 
            <div class="mb-3 px-3">
                <form wire:submit.prevent="applySearch" class="row g-3 align-items-end">

                    <div class="col-md-3 " wire:ignore>
                        <label class="form-label">{{ __('label.access_role') }}</label>
                        <select style="" class="form-control select2" wire:model.lazy="search.access_role" id="access_role">
                            <option value="">{{ __('label.select') }}</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary">
                            {{ __('label.search') }}
                        </button>
                    </div>
                </form>
            </div>

            <div class="table-responsive text-nowrap mt-4">

        
                <div class="row">
                    <form wire:submit.prevent="savePermissions">
                     <table class="table table-bordered table-hover align-middle">

                        <thead class="table-light">

                            <tr>

                                <th width="70">
                                    {{ __('label.no') }}
                                </th>

                                <th>
                                    {{ __('label.notification_category') }}
                                </th>

                                <th>
                                    {{ __('label.description') }}
                                </th>

                                <th width="120" class="text-center">
                                    {{ __('label.access') }}
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($notification_categories as $category)

                                <tr>

                                    <td>{{ $loop->iteration }}</td>

                                    <td>
                                        {{ $category->name }}
                                    </td>

                                    <td class="text-muted">
                                        {{ $category->description }}
                                    </td>

                                    <td class="text-center">

                                        <input
                                            type="checkbox"
                                            class="form-check-input"
                                            value="{{ $category->id }}"
                                            wire:model="notification_category_ids">

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="4" class="text-center text-muted">

                                        {{ __('label.no_data_found') }}

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>
                    
                    <hr>
                        <div class="mt-3 mb-5 col-md-11">
                            <div class="row justify-content-end">
                                <div class="col-sm-3">
                                    <button type="submit" class="btn btn-primary me-sm-3">{{ __('label.save') }}</button>
                                    <button type="reset" class="btn btn-label-secondary">{{ __('label.cancel') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

@script
<script>
    document.addEventListener("livewire:initialized",function(){
         function initSelect2() {
            $('.select2').select2({ width: '100%' });

            $('#access_role').off('change').on('change', function () {
                $wire.set('search.access_role', $(this).val());
            });
            // $('#sub_menu').off('change').on('change', function () {
            //     $wire.set('search.sub_menu', $(this).val());
            // });
        }

        initSelect2();

        Livewire.hook('morphed', (el) => {
            initSelect2();
        });
    })
</script>
@endscript