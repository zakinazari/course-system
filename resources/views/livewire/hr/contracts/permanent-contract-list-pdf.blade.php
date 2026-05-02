    
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
        {{ __('label.permanent_contract_list') }}
    </h2>

    <!-- Date range, aligned left, close to table -->
    <div style="margin-bottom:3px; font-size:12px; text-align:left;">
    {{ __('label.start_date') }}: {{ $search['start_date'] ?? '---' }} &nbsp;&nbsp; {{ __('label.end_date') }}: {{ $search['end_date'] ?? '---' }}
    </div>

    <table class="data-table"  cellspacing="0" cellpadding="5" width="100%">
        <thead >
            <tr>
                @if(in_array('no', $selectedFields)) <th>{{ __('label.no') }}</th> @endif
                @if(in_array('employee_id', $selectedFields)) <th>{{ __('label.employee') }}</th> @endif
                @if(in_array('position_id', $selectedFields)) <th>{{ __('label.position') }}</th> @endif
                @if(in_array('basic_salary', $selectedFields)) <th>{{ __('label.basic_salary') }}</th> @endif
                @if(in_array('start_date', $selectedFields)) <th>{{ __('label.start_date') }}</th> @endif
                @if(in_array('end_date', $selectedFields)) <th>{{ __('label.end_date') }}</th> @endif
                @if(in_array('status', $selectedFields)) <th>{{ __('label.status') }}</th> @endif
            </tr>
        </thead>
        <tbody>
            @foreach($contracts as $i => $contract)
                <tr>
                    @if(in_array('no', $selectedFields)) <td>{{ $i + 1 }}</td> @endif
                    @if(in_array('employee_id', $selectedFields)) <td>{{ $contract->employee?->name }} {{ $contract->employee?->last_name }}</td> @endif
                    @if(in_array('position_id', $selectedFields)) <td>{{ $contract->position?->name }}</td> @endif
                    @if(in_array('basic_salary', $selectedFields)) <td>{{ $contract->basic_salary }}</td> @endif
                    @if(in_array('start_date', $selectedFields)) <td>{{ $contract->start_date?->format('Y/m/d') }}</td> @endif
                    @if(in_array('end_date', $selectedFields)) <td>{{ $contract->end_date->format('Y/m/d') }}</td> @endif
                    @if(in_array('status', $selectedFields)) <td>{{ ucfirst($contract->status) }}</td> @endif
                </tr>
            @endforeach
        </tbody>
  
    </table>
</div>
