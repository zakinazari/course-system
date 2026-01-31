<div>
       <!-- title -->
    @section('title',
    (
        ($active_menu?->parent?->{app()->getLocale() === 'fa' ? 'name' : 'name_en'} ?? '') 
        ? $active_menu?->parent?->{app()->getLocale() === 'fa' ? 'name' : 'name_en'} . '-' 
        : ''
    ) 
    . $active_menu?->{app()->getLocale() === 'fa' ? 'name_fa' : 'name_en'} 
    . ' | ' . __('label.app_name')
    )
    <!-- end title --> 
    <div class="search-content">
        <form wire:submit.prevent="applySearch" class="row g-3 align-items-end">
            <h3>{{ __('label.ruling_order.order_list') }}</h3>

            <div class="form-group">
                <div class="input-group category-content">
            
                    <select class="form-select form-select-sm flex-grow-0" style="width: 120px;" wire:model.live="search.type">
                        <option value="title">{{ __('label.title') }}</option>
                        <option value="ruling_number">{{ __('label.ruling_order.order_number') }}</option>
                    </select>


                    <input type="text" class="form-control form-control-sm"
                    placeholder="{{ $search['type'] === 'title' ? __('label.title') : __('label.ruling_order.order_number') }}"
                    wire:model.live="search.identity">

                    <button type="submit" class="btn  btn" style="background-color:#00b2f2;color:white;">
                        <i class="flaticon-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>


    <table class="table">
        <thead class="table">
            <tr>
                <th>{{ __('label.NO') }}</th>
                <th>{{ __('label.title') }}</th>
                <th style="white-space:nowrap;">{{ __('label.ruling_order.order_number') }}</th>
                <th style="white-space:nowrap;">{{ __('label.ruling_order.order_date') }}</th>
            </tr>
        </thead>
        <tbody class="table-border">
            @foreach($orders as $i => $order)
            <tr>
                <td>{{ ($orders->currentPage() - 1) * $orders->perPage() + $i + 1 }}</td>
                <td>
                        @if(App::getLocale()=='fa') {{ $order->title_fa }} @else {{ $order->title_en }} @endif 
                </td>
                <td> {{ $order->ruling_number }} </td>
                <td> {{ hijri::date($order->ruling_date) }} </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4 justify-content-end px-3">
        {{ $orders->links() }}
    </div>
</div>

