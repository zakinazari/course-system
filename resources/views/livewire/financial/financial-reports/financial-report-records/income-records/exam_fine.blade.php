
<table class="table table-bordered">

    <thead>
        <tr>
            <th>#</th>
            <th>{{ __('label.student_code') }}</th>
            <th>{{ __('label.name') }}</th>
            <th>{{ __('label.course') }}</th>
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
                    {{ $record?->student?->student_code ?? '-' }}
                </td>
                <td>
                    {{ $record?->student?->name}} {{ $record?->student?->last_name}}
                </td>

                <td>
                    {{ $record?->course->name ?? '-' }}
                </td>

                <td>
                    {{ number_format($record->amount) }}
                </td>

                <td>
                    {{ $record->payment_date?->format('Y/m/d') }}
                </td>
                <td>
                    {{ $record?->course?->program?->section?->name ?? '-' }}
                </td>

                @if(!auth()->user()->branch_id)
                <td>
                    {{ $record->course?->branch?->name ?? '-' }}
                </td>
                @endif

                <td>
                    {{ $record->user?->email ?? '-' }}
                </td>
            </tr>
        @php $total_amount += $record->amount; @endphp
        @endforeach

            <tr>
                <td colspan="4" class="text-end">
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