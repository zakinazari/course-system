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
        'student_code' => __('label.student_code'),
        'name' => __('label.name'),
        'last_name' => __('label.last_name'),
        'father_name' => __('label.father_name'),
        'cognitive' => __('label.cognitive_score'),
        'attendance' => __('label.attendance_score'),
        'midterm' => __('label.midterm_score'),
        'final' => __('label.final_score'),
        'total' => __('label.total_score'),
    ];
@endphp

<table width="100%">
    <tr>
        <td width="20%">
            <img src="{{ public_path('logo.png') }}" width="80">
        </td>
        <td width="60%" style="text-align:center;">
            <p style="font-size:20px; font-weight:bold; margin:0;">{{ __('label.center_name') }}</p>

            <p style="font-size:20px; font-weight:bold; margin:3px 0 0 0;">{{ __('label.student_course_marks') }}</p>
            <p style="font-size:20px; font-weight:bold; margin:3px 0 0 0;">{{ $course?->name }}</p>
            <p style="font-size:20px; font-weight:bold; margin:3px 0 0 0;">{{ $status }}</p>
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
        @foreach($students as $i => $sc)
            <tr>
                @foreach($fields as $field)

                    <td>

                        @if($field === 'no')
                            {{ $i + 1 }}
                        @elseif($field==='cognitive')
                            {{ $sc->result?->cognitive }}
                        @elseif($field==='attendance')
                            {{ $sc->result?->attendance }}
                        @elseif($field==='midterm')
                            {{ $sc->result?->midterm }}
                        @elseif($field==='final')
                            {{ $sc->result?->final }}
                        @elseif($field==='total')
                            {{ $sc->result?->total }}
                        @else
                            {{ $sc->student?->$field }}
                        @endif

                    </td>

                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
