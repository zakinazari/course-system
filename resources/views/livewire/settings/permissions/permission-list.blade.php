
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
                        <select style="" class="form-control select2" wire:model="search.access_role" id="access_role">
                            <option value="">{{ __('label.select') }}</option>
                            @foreach($access_roles as $role)
                                <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">{{ __('label.menu_name') }}, {{ __('label.url') }}</label>
                        <input type="text" class="form-control"
                            placeholder="{{ __('label.menu_name') }}, {{ __('label.url') }}"
                            wire:model.defer="search.identity">
                    </div>

                    <div class="col-md-2" wire:ignore>
                        <label class="form-label">{{ __('label.main_menu') }}</label>
                        <select class="form-control select2" id="main_menu"
                            wire:model.defer="search.main_menu"
                            wire:change="loadSubMenus($event.target.value)">
                            <option value="">{{ __('label.all') }}</option>
                            @foreach($main_menus as $mm)
                                <option value="{{ $mm->id }}">
                                    {{ $mm->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2"> 
                        <label class="form-label">{{ __('label.sub_menu') }}</label>
                        <select class="form-control " wire:model.defer="search.sub_menu" id="sub_menu" >
                            <option value="">{{ __('label.all') }}</option>
                            @foreach($sub_menus as $sm)
                                <option value="{{ $sm->id }}">
                                    {{ $sm->name }}
                                </option>
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
                @if($show_table)
                
                <form wire:submit.prevent="savePermissions">
                    <table class="table">
                        <thead class="table-dark">
                            <tr>
                                <th>{{ __('label.menu_name') }}</th>
                                <th>{{ __('label.menu_type') }}</th>
                                <th>{{ __('label.grand_parent') }}</th>
                                <th>{{ __('label.parent') }}</th>
                                <th style="vertical-align: middle;">
                                    <div class="form-check me-3 mb-2 d-flex align-items-center">
                                        <input type="checkbox" class="form-check-input option-input" id="checkall"  wire:model="check_all" 
                                            wire:click="checkAll()" /> 
                                        <span class="form-check-label ms-1">{{ __('label.select_all') }}</span> 
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach($menus as $menu)
                                <tr wire:key="menu-{{ $menu->id }}">
                                    <td>
                                        {{ $menu->name }}
                                    </td>
                                    <td>
                                        {{ $menu->type?->type_name }}
                                    </td>
                                    <td>
                                        {{ $menu->grandParent?->name }}
                                    </td>
                                    <td>
                                        {{ $menu->parent?->name }}
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <div class="d-flex align-items-center flex-wrap">
                                            @foreach($actions as $action)
                                                
                                                <div class="form-check me-3 mb-2 d-flex align-items-center" wire:key="menu-{{ $menu->id }}-action-{{ $action->id }}">
                                                    <input class="form-check-input option-input" type="checkbox" 
                                                       wire:model="permissions.{{ $menu->id }}.{{ $action->id }}"
                                                        id="action_{{ $menu->id }}_{{ $action->id }}"
                                                        />
                                                    <label class="form-check-label ms-1" 
                                                        for="action_{{ $menu->id }}_{{ $action->id }}" 
                                                        style="font-size:11px;">
                                                        {{ $action->name_en }}
                                                    </label>
                                                </div>
                                                @if($menu->category==1)
                                                   @php break; @endphp
                                                @endif
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
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
                @endif
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

            $('#main_menu').off('change').on('change', function () {
                let val = $(this).val();
                $wire.set('search.main_menu', val);
                $wire.call('loadSubMenus', val);
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