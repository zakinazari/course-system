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
        'employee_code' => __('label.employee_code'),
        'name' => __('label.name'),
        'last_name' => __('label.last_name'),
        'father_name' => __('label.father_name'),
        'phone_no' => __('label.phone_no'),
        'tazkira_no' => __('label.tazkira_no'),
        'address' => __('label.address'),
        'hire_date' => __('label.hire_date'),
        'status' => __('label.status'),
        'branch_id' => __('label.branch'),
        'role' => __('label.role'),
    ];
@endphp

<table width="100%">
    <tr>
        <td width="20%">
            <img src="{{ public_path('logo.png') }}" width="80">
        </td>
        <td width="60%" style="text-align:center;">
            <p style="font-size:20px; font-weight:bold; margin:0;">{{ __('label.center_name') }}</p>

            <p style="font-size:18px; font-weight:bold; margin:3px 0 0 0;">Employee List</p>
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
        @foreach($employees as $i => $employee)
            <tr>
                @foreach($fields as $field)

                    <td>

                        @if($field === 'no')
                            {{ $i + 1 }}
                        @elseif($field === 'hire_date' && $employee->hire_date)
                            {{ \Carbon\Carbon::parse($employee->hire_date)->format('Y/m/d') }}
                        @elseif($field === 'status')
                            {{ ucfirst($employee->status) }}
                        @elseif($field==='branch_id')
                            {{ $employee->branch?->name }}
                        @elseif($field==='role')
                            {{ $employee->employeeRoles->pluck('name')->join(', ') }}
                        @else
                            {{ $employee->$field }}
                        @endif

                    </td>

                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

