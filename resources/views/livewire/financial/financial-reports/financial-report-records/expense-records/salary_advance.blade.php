
<table class="table table-bordered">

    <thead>
        <tr>
            <th>#</th>
            <th>{{ __('label.employee_code') }}</th>
            <th>{{ __('label.name') }}</th>
            <th>{{ __('label.amount') }}</th>
            <th>{{ __('label.date') }}</th>

            <th>{{ __('label.section') }}</th>

            @if(!auth()->user()->branch_id)
            <th>{{ __('label.branch') }}</th>
            @endif

            <th>{{ __('label.user') }}</th>
        </tr>
    </thead>

    <tbody>
         @php
            $total_amount = 0;
        @endphp
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

                <td>
                    {{ $record?->section?->name ?? '-' }}
                </td>
                @if(!auth()->user()->branch_id)
                    <td>
                        {{ $record?->branch?->name ?? '-' }}
                    </td>
                @endif
                <td>
                    {{ $record->user?->email }}
                </td>

            </tr>
              @php $total_amount += $record->total_amount; @endphp
        @endforeach
        <tr>
            <td colspan="3" class="text-end">
                <strong>{{ __('label.total') }}</strong>
            </td>

            <td>
                <strong>{{ number_format($total_amount) }}</strong>
            </td>

            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

    </tbody>

</table>