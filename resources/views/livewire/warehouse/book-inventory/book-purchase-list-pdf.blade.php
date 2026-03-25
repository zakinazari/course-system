    
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
        {{ __('label.book_purchase_list') }}
    </h2>

    <!-- Date range, aligned left, close to table -->
    <div style="margin-bottom:3px; font-size:12px; text-align:left;">
    {{ __('label.from_date') }}: {{ $search['from'] ?? '---' }} &nbsp;&nbsp; {{ __('label.to_date') }}: {{ $search['to'] ?? '---' }}
    </div>

    <table class="data-table"  cellspacing="0" cellpadding="5" width="100%">
        <thead >
            <tr>
                @if(in_array('no', $selectedFields)) <th>{{ __('label.no') }}</th> @endif
                @if(in_array('warehouse_id', $selectedFields)) <th>{{ __('label.warehouse') }}</th> @endif
                @if(in_array('book_id', $selectedFields)) <th>{{ __('label.book') }}</th> @endif
                @if(in_array('quantity_change', $selectedFields)) <th>{{ __('label.quantity') }}</th> @endif
                @if(in_array('balance_after', $selectedFields)) <th>{{ __('label.balance_after') }}</th> @endif
                @if(in_array('created_at', $selectedFields)) <th>{{ __('label.date') }}</th> @endif
            </tr>
        </thead>
        <tbody>
            @foreach($purchases as $i => $purchase)
                <tr>
                    @if(in_array('no', $selectedFields)) <td>{{ $i + 1 }}</td> @endif
                    @if(in_array('warehouse_id', $selectedFields)) <td>{{ $purchase->inventory?->warehouse?->name }}</td> @endif
                    @if(in_array('book_id', $selectedFields)) <td>{{ $purchase->inventory?->book?->name }}</td> @endif
                    @if(in_array('quantity_change', $selectedFields)) <td>{{ $purchase->quantity_change }}</td> @endif
                    @if(in_array('balance_after', $selectedFields)) <td>{{ $purchase->balance_after }}</td> @endif
                    @if(in_array('created_at', $selectedFields)) <td>{{ $purchase->created_at->format('Y/m/d H:i:A') }}</td> @endif
                </tr>
            @endforeach
        </tbody>
  
    </table>
</div>