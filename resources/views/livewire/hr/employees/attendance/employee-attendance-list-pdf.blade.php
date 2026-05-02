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
        'employee_id' => __('label.employee'),
        'position_id' => __('label.position'),
        'attendance_status' => __('label.attendance_status'),
        'status' => __('label.status'),
        'branch_id' => __('label.branch'),
    ];
@endphp

<table width="100%">
    <tr>
        <td width="20%">
            <img src="{{ getLogo() }}" width="80">
        </td>
        <td width="60%" style="text-align:center;">
            <p style="font-size:20px; font-weight:bold; margin:0;">{{ __('label.center_name') }}</p>

            <p style="font-size:20px; font-weight:bold; margin:3px 0 0 0;">{{ __('label.employee_attendance') }}</p>
            <p style="font-size:20px; font-weight:bold; margin:3px 0 0 0;">{{ __('label.date') }}: {{ $attendance_date }}</p>
        </td>
        <td width="20%"></td>
    </tr>
</table>

<table class="data-table" cellspacing="0" cellpadding="5" width="100%">
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
        @foreach($selected_employees as $i => $emp)
            <tr>
                @foreach($selectedFields as $field)

                    <td>

                        @if($field === 'no')
                            {{ $i + 1 }}
                        @elseif($field === 'attendance_status')
                             @php
                             
                                $empId = $emp->employee->id ?? null;
                                $status = $empId ? ($attendances[$empId] ?? null) : null;

                                $color = match($status) {
                                    'present' => '#198754',  // سبز
                                    'absent'  => '#dc3545',  // قرمز
                                    'late'    => '#ffc107',  // زرد
                                    'excused' => '#0dcaf0',  // آبی
                                    default   => '#6c757d',  // خاکستری
                                };
                            @endphp

                            <span style="
                                background-color: {{ $color }};
                                color: #fff;
                                padding: 4px 8px;
                                border-radius: 4px;
                                font-size: 12px;
                                display: inline-block;
                            ">
                                {{ ucfirst($status) }}
                            </span>
                        @elseif($field === 'status')

                            @php
                                $empId = $emp->employee->id ?? null;
                                $taken = $empId ? ($existing_attendances[$empId] ?? false) : false;

                                $bgColor = $taken ? '#198754' : '#6c757d';
                                $label   = $taken ? 'Taken' : 'Not Taken';
                            @endphp

                            <span style="
                                background-color: {{ $bgColor }};
                                color: #fff;
                                padding: 4px 8px;
                                border-radius: 4px;
                                font-size: 12px;
                                display: inline-block;
                            ">
                                {{ $label }}
                            </span>

                        @elseif($field==='employee_id')
                            {{ $emp->employee?->name }}  {{ $emp->employee?->last_name }}
                        @elseif($field==='position_id')
                            {{ $emp->position?->name }} 
                        @elseif($field==='branch_id')
                            {{ $emp->branch?->name }} 
                        @else
                            {{ $emp->employee?->$field }}
                        @endif

                    </td>
                
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
