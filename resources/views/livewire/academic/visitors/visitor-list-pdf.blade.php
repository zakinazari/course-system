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
        'visit_date' => __('label.visit_date'),
        'visit_purpose_id' => __('label.visit_purpose'),
        'referral_source_id' => __('label.referral_source'),
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

            <p style="font-size:18px; font-weight:bold; margin:3px 0 0 0;">Visitor List</p>
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
        @foreach($visitors as $i => $visitor)
            <tr>
                @foreach($fields as $field)

                    <td>

                        @if($field === 'no')
                            {{ $i + 1 }}
                        @elseif($field === 'visit_date' && $visitor->visit_date)
                            {{ \Carbon\Carbon::parse($visitor->visit_date)->format('Y/m/d - h:i A') }}
                        @elseif($field==='visit_purpose_id')
                            {{ $visitor->visitPurPose?->name }}
                        @elseif($field==='referral_source_id')
                            {{ $visitor->referralSource?->name }}
                        @elseif($field==='branch_id')
                            {{ $visitor->branch?->name }}
                        @else
                            {{ $visitor->$field }}
                        @endif

                    </td>

                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

