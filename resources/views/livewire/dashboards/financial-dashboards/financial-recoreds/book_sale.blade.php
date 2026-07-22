
<table class="table table-bordered">

<thead>
    <tr>
        <th>#</th>
        <th>{{ __('label.student_code') }}</th>
        <th>{{ __('label.name') }}</th>
        <th>{{ __('label.book') }}</th>
        <th>{{ __('label.amount') }}</th>
        <th>{{ __('label.date') }}</th>
    </tr>
</thead>

<tbody>

    @foreach($records as $index => $record)

        <tr>

            <td>{{ $index + 1 }}</td>

            <td>
                {{ $record?->student?->student_code ?? '-' }}
            </td>
            <td>
                {{ $record?->student?->name}} {{ $record?->student?->last_name}}
            </td>

            <td>
                {{ $record?->book->name ?? '-' }}
            </td>

            <td>
                {{ number_format($record->price) }}
            </td>

            <td>
                {{ $record->payment_date?->format('Y/m/d') }}
            </td>

        </tr>

    @endforeach

</tbody>

</table>