    
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

<div id="printArea" >
      <!-- Logo -->
        <div style="text-align:center;margin-bottom:10px;">
            <img src="{{ public_path('logo.png') }}" alt="Logo" style="height:70px;">
        </div>
        <!-- Title -->
        <h2 style="text-align:center;">
            Student Other Fees Report
        </h2>
        @if(!empty($search['fee_type_id']))
            <h2 style="text-align:center;">
                {{ $fee_type_name }}
            </h2>
        @endif
        <!-- Date range, aligned left, close to table -->
        <div style="margin-bottom:3px; font-size:12px; text-align:left;">
        {{ __('label.from_date') }}: {{ $search['from'] ?? '---' }} &nbsp;&nbsp; {{ __('label.to_date') }}: {{ $search['to'] ?? '---' }}
        </div>

        <table class="data-table"  cellspacing="0" cellpadding="5" width="100%">
            <thead class="table-dark">
                <tr>
                    @if(in_array('no', $selectedFields)) <th>{{ __('label.no') }}</th> @endif
                    @if(in_array('student', $selectedFields)) <th>{{ __('label.student') }}</th> @endif
                    @if(in_array('amount', $selectedFields)) <th>{{ __('label.amount') }}</th> @endif
                    @if(in_array('payment_date', $selectedFields)) <th>{{ __('label.payment_date') }}</th> @endif
                    @if(in_array('branch', $selectedFields)) <th>{{ __('label.branch') }}</th> @endif
                </tr>
            </thead>
            <tbody>
                @foreach($fees as $i => $fee)
                    <tr>
                        @if(in_array('no', $selectedFields)) <td>{{ $i + 1 }}</td> @endif
                        @if(in_array('student', $selectedFields)) <td>{{ $fee?->student?->name }} {{ $fee?->student?->last_name }}</td> @endif
                        @if(in_array('amount', $selectedFields)) <td>{{ $fee->amount }}</td> @endif
                        @if(in_array('payment_date', $selectedFields)) <td>{{ $fee->payment_date->format('Y/m/d') }}</td> @endif
                        @if(in_array('branch', $selectedFields)) <td>{{ $fee?->branch?->name }}</td> @endif
                    </tr>
                @endforeach
            </tbody>
            @if(in_array('amount', $selectedFields))
            <tfoot class="table-dark">
                <tr>
                    <th colspan="{{ count($selectedFields) - 3 }}">{{ __('label.total') }}</th>
                    <th>{{ $total_payments }}</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>