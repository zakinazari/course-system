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
        'last_name' => __('label.last_name'),
        'father_name' => __('label.father_name'),
        'phone_no' => __('label.phone_no'),
        'subject' => __('label.subject'),
        'date' => __('label.date'),
        'status' => __('label.status'),
        'reference_id' => __('label.meeting_reference'),
        'branch_id' => __('label.branch'),
    ];
@endphp

<table width="100%">
    <tr>
        <td width="20%">
            <img src="{{ public_path('logo.png') }}" width="80">
        </td>
        <td width="60%" style="text-align:center;">
            <p style="font-size:20px; font-weight:bold; margin:0;">{{ __('label.center_name') }}</p>

            <p style="font-size:18px; font-weight:bold; margin:3px 0 0 0;">Meeting List</p>
        </td>
        <td width="20%"></td>
    </tr>
</table>

<table class="data-table" cellspacing="0" cellpadding="5" width="100%">
    <thead>
        <tr>
            @foreach($fields as $field)
                <th>
                    {{ $labels[$field] ?? ucfirst($field) }}
                </th>
            @endforeach
        </tr>
    </thead>

    <tbody>
        @foreach($meetings as $i => $meeting)
            <tr>
                @foreach($fields as $field)

                    <td>

                        @if($field === 'no')
                            {{ $i + 1 }}
                        @elseif($field === 'date' && $meeting->date)
                            {{ \Carbon\Carbon::parse($meeting->date)->format('Y/m/d - h:i A') }}
                        @elseif($field === 'status')
                            {{ ucfirst($meeting->status) }}
                        @elseif($field==='reference_id')
                            {{ $meeting->reference?->name }}
                        @elseif($field==='branch_id')
                            {{ $meeting->branch?->name }}
                        @else
                            {{ $meeting->$field }}
                        @endif

                    </td>

                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
