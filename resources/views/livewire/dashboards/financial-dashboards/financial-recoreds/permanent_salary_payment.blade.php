
<table class="table table-bordered">

    <thead>
        <tr>
            <th>#</th>
            <th>{{ __('label.employee_code') }}</th>
            <th>{{ __('label.name') }}</th>
            <th>{{ __('label.total_deduction') }}</th>
            <th>{{ __('label.net_salary') }}</th>
            <th>{{ __('label.payment_date') }}</th>
        </tr>
    </thead>

    <tbody>

        @foreach($records as $index => $record)

            <tr>

                <td>{{ $index + 1 }}</td>

                <td>
                    {{ $record?->employee?->employee_code ?? '-' }}
                </td>
                <td>
                    {{ $record?->employee?->name}} {{ $record?->employee?->last_name}}
                </td>

                <td>
                    {{ number_format($record->total_deductions) }}
                </td>
                <td>
                    {{ number_format($record->net_salary) }}
                </td>

                <td>
                    {{ $record->payment_date?->format('Y/m/d') }}
                </td>

            </tr>

        @endforeach

    </tbody>

</table>