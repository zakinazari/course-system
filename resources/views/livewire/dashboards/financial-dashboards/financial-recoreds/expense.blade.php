<table class="table table-borderd">
    <thead class="table-dark">
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

                {{ __('label.quantity') }}
            </th>
            <th>

                {{ __('label.unit') }}
            </th>
        
            <th>

                {{ __('label.unit_price') }}
            </th>
            <th>

                {{ __('label.total_amount') }}
            </th>

            <th>
                {{ __('label.expense_date') }}
            </th>
            
        </tr>
    </thead>
    <tbody class="table-border-bottom-0">
        @foreach($records as $i => $expense)
        <tr>
            <td>{{  $i + 1 }}</td>
            <td>{{ $expense->name }}</td>
            <td>{{ $expense->category?->name }}</td>
            <td>{{ $expense->quantity }}</td>
            <td>{{ $expense->unit?->name }}</td>
            <td>{{ $expense->unit_price }}</td>
            <td>{{ $expense->total_amount }}</td>

            <td>{{ $expense->expense_date?->format('Y/m/d') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>