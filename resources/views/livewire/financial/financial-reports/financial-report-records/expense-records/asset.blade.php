<table class="table table-bordered">
    <thead >
        <tr>
            <th>

                {{ __('label.NO') }} 
            </th>
            <th>

                {{ __('label.name') }}
            </th>
            <th>

                {{ __('label.category') }}
            </th>
            <th>

                {{ __('label.unit') }}
            </th>
        
            <th>

                {{ __('label.purchase_price') }}
            </th>

            <th>
                {{ __('label.purchase_date') }}
            </th>

            <th>{{ __('label.section') }}</th>

            @if(!auth()->user()->branch_id)
            <th>{{ __('label.branch') }}</th>
            @endif

            <th>{{ __('label.user') }}</th>
            
        </tr>
    </thead>
    <tbody >
         @php
            $total_amount = 0;
        @endphp
        @foreach($records as $i => $record)
        <tr>
            <td>{{  $i + 1 }}</td>
            <td>{{ $record->name }}</td>
            <td>{{ $record->category?->name }}</td>
            <td>{{ $record->unit?->name }}</td>
            <td>{{ $record->purchase_price }}</td>
            <td>{{ $record->purchase_date?->format('Y/m/d') }}</td>
            
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
        @php $total_amount += $record->purchase_price; @endphp
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