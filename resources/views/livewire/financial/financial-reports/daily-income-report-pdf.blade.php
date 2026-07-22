    
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
        {{ __('label.daily_income_report') }}
    </h2>

    <!-- Date range, aligned left, close to table -->
    <div style="margin-bottom:3px; font-size:12px; text-align:left;">
    {{ __('label.from_date') }}: {{ $search['from'] ?? '---' }} &nbsp;&nbsp; {{ __('label.to_date') }}: {{ $search['to'] ?? '---' }}
    </div>

    <table  class="data-table"  cellspacing="0" cellpadding="5" width="100%">
        <thead class="table-dark">
            <tr>
                @if(in_array('no', $selectedFields)) <th>{{ __('label.no') }}</th> @endif
                
                @if(in_array('category', $selectedFields)) <th>{{ __('label.category') }}</th> @endif
                @if(in_array('amount', $selectedFields)) <th>{{ __('label.amount') }}</th> @endif
                @if(in_array('date', $selectedFields)) <th>{{ __('label.date') }}</th> @endif
                @if(in_array('section', $selectedFields)) <th>{{ __('label.section') }}</th> @endif
                @if(in_array('user', $selectedFields)) <th>{{ __('label.user') }}</th> @endif
                @if(in_array('branch', $selectedFields)) <th>{{ __('label.branch') }}</th> @endif
            </tr>
        </thead>
        <tbody>
            @foreach($incomes as $i => $income)
                <tr>
                    @if(in_array('no', $selectedFields)) <td>{{ $i + 1 }}</td> @endif
                    
                    @if(in_array('category', $selectedFields)) <td>{{ str($income->category)->replace('_', ' ')->title() }}</td> @endif

                    @if(in_array('amount', $selectedFields)) <td>{{ $income->amount }}</td> @endif

                    @if(in_array('date', $selectedFields)) <td>{{ $income?->created_at->format('Y/m/d h:i A') }}</td> @endif

                    @if(in_array('section', $selectedFields)) <td>{{ $income?->section?->name }}</td> @endif
                    @if(in_array('user', $selectedFields)) <td>{{ $income?->user?->email }}</td> @endif
                    
                    @if(in_array('branch', $selectedFields)) <td>{{ $income?->branch?->name }}</td> @endif
                </tr>
            @endforeach
        </tbody>
        @if(in_array('amount', $selectedFields))
        <tbody class="table-dark">
            <tr>
                <th colspan="{{ count($selectedFields) - 2 }}">{{ __('label.total') }}</th>
                <th>{{ $total_income }}</th>
            </tr>
        </tbody>
        @endif
</table>
</div>
