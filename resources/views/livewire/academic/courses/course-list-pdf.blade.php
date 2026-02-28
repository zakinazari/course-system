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
        'course_type_id' => __('label.course_type'),
        'program_id' => __('label.program'),
        'book_id' => __('label.book'),
        'shift_id' => __('label.shift'),
        'min_capacity' => __('label.min_capacity'),
        'max_capacity' => __('label.max_capacity'),
        'total_teaching_days' => __('label.total_teaching_days'),
        'start_time' => __('label.start_time'),
        'end_time' => __('label.end_time'),
        'start_date' => __('label.start_date'),
        'end_date' => __('label.end_date'),
        'mid_exam_date' => __('label.mid_exam_date'),
        'final_exam_date' => __('label.final_exam_date'),
        'teacher_ids' => __('label.teacher'),
        'classroom_id' => __('label.classroom'),
        'status' => __('label.status'),
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

            <p style="font-size:20px; font-weight:bold; margin:3px 0 0 0;">Course List</p>
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
        @foreach($courses as $i => $course)
            <tr>
                @foreach($fields as $field)

                    <td>

                        @if($field === 'no')
                            {{ $i + 1 }}
                        @elseif($field === 'status')
                            {{ ucfirst($course->status) }}
                        @elseif($field === 'start_time')
                            {{ $course->start_time->format('h:i A') }}
                        @elseif($field === 'end_time')
                            {{ $course->end_time->format('h:i A') }}
                        @elseif($field === 'start_date')
                            {{ $course->start_date->format('Y/m/d') }}
                        @elseif($field === 'end_date')
                            {{ $course->end_date->format('Y/m/d') }}
                        @elseif($field === 'mid_exam_date')
                            {{ $course->mid_exam_date->format('Y/m/d') }}
                        @elseif($field === 'final_exam_date')
                            {{ $course->final_exam_date->format('Y/m/d') }}
                        @elseif($field==='program_id')
                            {{ $course->program?->name }}
                        @elseif($field==='book_id')
                            {{ $course->book?->name }}
                        @elseif($field==='shift_id')
                            {{ $course->shift?->name }}
                        @elseif($field==='course_type_id')
                            {{ $course->courseType?->name }}
                        @elseif($field==='classroom_id')
                            {{ $course->classroom?->name }}
                        @elseif($field==='branch_id')
                            {{ $course->branch?->name }}
                        @elseif($field==='teacher_ids')
                            {{ $course->teachers->pluck('name')->join(', ') }} {{ $course->teachers->pluck('last_name')->join(', ') }} 
                        @else
                            {{ $course->$field }}
                        @endif

                    </td>

                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

