    
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
        {{ __('label.temporary_teachers_payroll') }}
    </h2>

    <!-- Date range, aligned left, close to table -->
    <div style="margin-bottom:3px; font-size:18px; text-align:left;">
    {{ __('label.year') }}: {{ $year ?? '---' }} &nbsp;&nbsp; {{ __('label.month') }}: {{ $month->name ?? '---' }}
    </div>

    <table class="data-table"  cellspacing="0" cellpadding="5" width="100%">
        <thead >
            <tr>
                @if(in_array('no', $selectedFields)) <th>{{ __('label.no') }}</th> @endif
                @if(in_array('branch_id', $selectedFields) && !auth()->user()->branch_id) <th>{{ __('label.branch') }}</th> @endif
                @if(in_array('employee_id', $selectedFields)) <th>{{ __('label.employee') }}</th> @endif
                @if(in_array('status', $selectedFields)) <th>{{ __('label.status') }}</th> @endif
                @if(in_array('gross_salary', $selectedFields)) <th>{{ __('label.gross_salary') }}</th> @endif
                @if(in_array('taxi_fare', $selectedFields)) <th>{{ __('label.taxi_fare') }}</th> @endif
                @if(in_array('credit_card', $selectedFields)) <th>{{ __('label.credit_card') }}</th> @endif
                @if(in_array('tax', $selectedFields)) <th>{{ __('label.tax') }}</th> @endif
                @if(in_array('food_deduction', $selectedFields)) <th>{{ __('label.food_deduction') }}</th> @endif
                @if(in_array('advance_deduction', $selectedFields)) <th>{{ __('label.advance_deduction') }}</th> @endif
                @if(in_array('net_salary', $selectedFields)) <th>{{ __('label.net_salary') }}</th> @endif
                @if(in_array('payment_date', $selectedFields)) <th>{{ __('label.payment_date') }}</th> @endif
            </tr>
        </thead>
        <tbody>
            @php $total_salary=0; @endphp
            @foreach($selected_employees as $i => $employee)
            <tr>
                @if(in_array('no', $selectedFields)) <td>{{ $i + 1 }}</td> @endif

                    @if(in_array('branch_id', $selectedFields) && !auth()->user()->branch_id) <td>{{ $employee->branch?->name }}</td> @endif
                    @if(in_array('employee_id', $selectedFields)) <td>{{ $employee?->name }} {{ $employee?->last_name }}</td> @endif
                    @if(in_array('status', $selectedFields)) <td>{{ ucfirst($employee->payroll?->status) }}</td> @endif
                    @if(in_array('gross_salary', $selectedFields)) <td>{{ $employee->payroll?->gross_salary }}</td> @endif
                    @if(in_array('taxi_fare', $selectedFields)) <td>{{ $employee->payroll?->taxi_fare }}</td> @endif
                    @if(in_array('credit_card', $selectedFields)) <td>{{ $employee->payroll?->credit_card }}</td> @endif
                    @if(in_array('tax', $selectedFields)) <td>{{ $employee->payroll?->tax }}</td> @endif
                    @if(in_array('food_deduction', $selectedFields)) <td>{{ $employee->payroll?->food_deduction }}</td> @endif
                    @if(in_array('advance_deduction', $selectedFields)) <td>{{ $employee->payroll?->advance_deduction }}</td> @endif
                    @if(in_array('net_salary', $selectedFields)) <td>{{ $employee->payroll?->net_salary }}</td> @endif
                    @if(in_array('payment_date', $selectedFields)) <td>{{ $employee->payroll?->payment_date?->format('Y/m/d') }}</td> @endif
                   
                </tr>
                @php $total_salary += $employee->payroll?->net_salary  @endphp
            @endforeach
            <tr>
                <th colspan="{{ count($selectedFields) - 2 }}">{{ __('label.total_salary') }}</th>
                <th>{{ $total_salary }}</th>
                <th></th>
            </tr>
        </tbody>
  
    </table>
</div>

