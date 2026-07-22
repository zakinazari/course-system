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

                {{ __('label.unit') }}
            </th>
        
            <th>

                {{ __('label.purchase_price') }}
            </th>

            <th>
                {{ __('label.purchase_date') }}
            </th>
            
        </tr>
    </thead>
    <tbody class="table-border-bottom-0">
        @foreach($records as $i => $asset)
        <tr>
            <td>{{  $i + 1 }}</td>
            <td>{{ $asset->name }}</td>
            <td>{{ $asset->category?->name }}</td>
            <td>{{ $asset->unit?->name }}</td>
            <td>{{ $asset->purchase_price }}</td>
            <td>{{ $asset->purchase_date?->format('Y/m/d') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>