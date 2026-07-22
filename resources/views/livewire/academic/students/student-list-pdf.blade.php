<style>


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
        'student_code' => __('label.student_code'),
        'name' => __('label.name'),
        'last_name' => __('label.last_name'),
        'father_name' => __('label.father_name'),
        'phone_no' => __('label.phone_no'),
        'tazkira_no' => __('label.tazkira_no'),
        'address' => __('label.address'),
        'registration_date' => __('label.registration_date'),
        'status' => __('label.status'),
        'branch_id' => __('label.branch'),
        'gender_id' => __('label.gender'),
        'occupation_id' => __('label.occupation'),
    ];
@endphp

<div style="text-align:center;margin-bottom:10px;">
    <img src="{{ getLogo() }}" alt="Logo" style="height:70px;">
</div>
<h2 style="text-align:center;">
    {{ __('label.center_name') }}
</h2>
<!-- Title -->
<h2 style="text-align:center;">
    Student List
</h2>
<table class="data-table"  cellspacing="0" cellpadding="5" width="100%">
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
        @foreach($students as $i => $student)
            <tr>
                @foreach($fields as $field)

                    <td>

                        @if($field === 'no')
                            {{ $i + 1 }}
                        @elseif($field === 'registration_date' && $student->registration_date)
                            {{ \Carbon\Carbon::parse($student->registration_date)->format('Y/m/d - h:i A') }}
                        @elseif($field === 'status')
                            {{ ucfirst($student->status) }}
                        @elseif($field==='branch_id')
                            {{ $student->branch?->name }}
                        @elseif($field==='gender_id')
                            {{ $student->gender?->name }}

                        @elseif($field==='occupation_id')
                            {{ $student->occupation?->name }}
                        @else
                            {{ $student->$field }}
                        @endif

                    </td>

                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
