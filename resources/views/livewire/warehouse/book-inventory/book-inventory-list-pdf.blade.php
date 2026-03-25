    
<style>
    body, html {
        background: #fff !important;
        -webkit-print-color-adjust: exact;
    }

    body * {
        visibility: hidden;
    }

    #printArea, #printArea * {
        visibility: visible;
    }
    .data-table {
        border-collapse: collapse;
        width: 100%;
    }

    .data-table th,
    .data-table td {
        border: 1px solid #000;
    }
    .data-table{
        margin-top:5px;
    }

/* Logo */
.logo-container {
    text-align: center;
    margin-bottom: 15px;
}

.logo-container .logo {
    max-width: 120px;
}

</style>


<div id="printArea">
    <!-- Logo -->
    <div style="text-align:center;margin-bottom:10px;">
        <img src="{{ getLogo() }}" alt="Logo" style="height:70px;">
    </div>
    <!-- Title -->
    <h2 style="text-align:center;">
        {{ __('label.book_inventory_list') }}
    </h2>

    <!-- Date range, aligned left, close to table -->

    <table class="data-table"  cellspacing="0" cellpadding="5" width="100%">
        <thead >
            <tr>
                @if(in_array('no', $selectedFields)) <th>{{ __('label.no') }}</th> @endif
                @if(in_array('warehouse_id', $selectedFields)) <th>{{ __('label.warehouse') }}</th> @endif
                @if(in_array('book_id', $selectedFields)) <th>{{ __('label.book') }}</th> @endif
                @if(in_array('quantity', $selectedFields)) <th>{{ __('label.quantity') }}</th> @endif
            </tr>
        </thead>
        <tbody>
            @foreach($book_inventories as $i => $inventory)
                <tr>
                    @if(in_array('no', $selectedFields)) <td>{{ $i + 1 }}</td> @endif
                    @if(in_array('warehouse_id', $selectedFields)) <td>{{ $inventory->warehouse?->name }}</td> @endif
                    @if(in_array('book_id', $selectedFields)) <td>{{ $inventory->book?->name }}</td> @endif
                    @if(in_array('quantity', $selectedFields)) 
                    <td>
                        {{ $inventory->quantity }}
                    </td> 
                    @endif
                </tr>
            @endforeach
        </tbody>
  
    </table>
</div>