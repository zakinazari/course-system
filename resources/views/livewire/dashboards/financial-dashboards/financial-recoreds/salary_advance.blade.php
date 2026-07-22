
<table class="table table-bordered">

    <thead>
        <tr>
            <th>#</th>
            <th>{{ __('label.employee_code') }}</th>
            <th>{{ __('label.name') }}</th>
            <th>{{ __('label.amount') }}</th>
            <th>{{ __('label.date') }}</th>
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
                    {{ number_format($record->total_amount) }}
                </td>

                <td>
                    {{ $record->created_at?->format('Y/m/d') }}
                </td>

            </tr>

        @endforeach

    </tbody>

</table>