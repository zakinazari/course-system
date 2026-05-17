<style>
    @font-face {
        font-family: 'Vazir';
        src: url({{ public_path('fonts/Vazir-Regular.ttf') }}) format('truetype');
        font-weight: normal;
        font-style: normal;
    }

    body, table, th, td {
        font-family: 'Vazir', sans-serif;
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
</style>


@php
    $labels = [
        'no' => __('label.no'),
        'name' => __('label.name'),
        'quantity' => __('label.quantity'),
        'unit_id' => __('label.unit'),
        'category_id' => __('label.category'),
        'unit_price' => __('label.unit_price'),
        'total_amount' => __('label.total_amount'),
        'expense_date' => __('label.expense_date'),
        'note' => __('label.note'),
        'section_id' => __('label.section'),
        'shop_id' => __('label.shop'),
        'employee_id' => __('label.purchased_by'),
        'branch_id' => __('label.branch'),
    ];
@endphp

 <!-- Logo -->
    <div style="text-align:center;margin-bottom:10px;">
        <img src="{{ getLogo() }}" alt="Logo" style="height:70px;">
    </div>
    <!-- Title -->
    <h2 style="text-align:center;">
        {{ __('label.expense_list') }}
    </h2>

    <!-- Date range, aligned left, close to table -->
    <div style="margin-bottom:3px; font-size:12px; text-align:left;">
    {{ __('label.from_date') }}: {{ $search['from'] ?? '---' }} &nbsp;&nbsp; {{ __('label.to_date') }}: {{ $search['to'] ?? '---' }}
    </div>
<table class="data-table"  cellspacing="0" cellpadding="5" width="100%">
    <thead>
        <tr>
            @foreach($selectedFields as $field)
                <th>
                    {{ $labels[$field] ?? ucfirst($field) }}
                </th>
            @endforeach
        </tr>
    </thead>

    <tbody>
        @foreach($expenses as $i => $expense)
            <tr>
                @foreach($selectedFields as $field)
       
                    <td>

                        @if($field === 'no')
                            {{ $i + 1 }}
                        @elseif($field === 'unit_id')
                            {{ $expense?->unit?->name }}
                        @elseif($field === 'category_id')
                            {{ $expense?->category?->name }}
                        @elseif($field==='section_id')
                            {{ $expense->section?->name }}
                        @elseif($field==='branch_id')
                            {{ $expense->branch?->name }}
                        @elseif($field==='shop_id')
                            {{ $expense->shop?->name }}
                        @elseif($field==='employee_id')
                            {{ $expense->purchasedByEmployee?->name }} {{ $expense->purchasedByEmployee?->last_name }}
                        @elseif($field==='expense_date')
                            {{ $expense->expense_date?->format('Y/m/d') }}
                        @else
                            {{ $expense->$field }}
                        @endif

                    </td>

                @endforeach
            </tr>
        @endforeach
    </tbody>
    @if(in_array('total_amount', $selectedFields))
    <tfoot >
        <tr>
            <th colspan="{{ count($selectedFields)-1 }}">{{ __('label.total') }}</th>
            <th colspan="">{{ $total_expense_amount }}</th>
        </tr>
    </tfoot>
    @endif
</table>
