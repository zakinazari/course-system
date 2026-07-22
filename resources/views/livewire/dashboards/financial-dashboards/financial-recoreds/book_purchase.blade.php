
<table class="table table-bordered">

    <thead>
        <tr>
            <th>#</th>
            <th>{{ __('label.warehouse') }}</th>
            <th>{{ __('label.physical_book') }}</th>
            <th>{{ __('label.quantity') }}</th>
            <th>{{ __('label.unit_price') }}</th>
            <th>{{ __('label.total_amount') }}</th>
            <th>{{ __('label.date') }}</th>
        </tr>
    </thead>

    <tbody>

        @foreach($records as $index => $record)

            <tr>

                <td>{{ $index + 1 }}</td>

                <td>
                    {{ $record?->inventory?->warehouse?->name ?? '-' }}
                </td>
                <td>
                    {{ $record?->inventory?->book?->name}}
                </td>

                <td>
                    {{ number_format($record->quantity_change) }}
                </td>
                <td>
                    {{ number_format($record->unit_price) }}
                </td>
                <td>
                    {{ number_format($record->unit_price) }}
                </td>

                <td>
                    {{ $record->created_at?->format('Y/m/d') }}
                </td>

            </tr>

        @endforeach

    </tbody>

</table>