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
    // labels ثابت
    $labels = [
        'no' => __('label.no'),
        'student_code' => __('label.student_code'),
        'name' => __('label.name'),
        'father_name' => __('label.father_name'),
        'total' => __('label.total_score'),
        'status' => __('label.status'),
    ];

    // labels داینامیک exam types
    foreach($exam_types ?? [] as $type) {
        $labels['exam_'.$type->id] = $type->name . ' (' . ($exam_percentages[$type->id] ?? 0) . '%)';
    }
@endphp

<table width="100%">
    <tr>
        <td width="20%">
            <img src="{{ getLogo() }}" width="80">
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
            <th>{{ __('label.program') }}: {{ $course->program?->name }}</th>
            <th>{{ __('label.book') }}: {{ $course->book?->name }}</th>
            <th>{{ __('label.shift') }}: {{ $course->shift?->name }}</th>
        </tr>
        <tr>
            <th>{{ __('label.time') }}: {{ $course->time?->start_time?->format('h:i A') }} - {{ $course->time?->end_time?->format('h:i A') }}</th>
            <th>{{ __('label.teacher') }}: {{ $course?->teacher?->name }} {{ $course?->teacher?->last_name }}</th>
            <th></th>
        </tr>
    </thead>
</table>
<table class="data-table" cellspacing="0" cellpadding="5" width="100%">
    <thead>
        
        <tr>
            @foreach($fields as $field)
                <th>
                    @if(in_array($field, ['no','student_code','name','father_name','total','status']))
                        {{ __('label.' . $field) ?? ucfirst($field) }}
                    @else
                        @php
                            $examType = $exam_types->firstWhere('name', $field);
                            $percentage = $exam_percentages[$examType?->id] ?? 0;
                        @endphp
                        {{ $field }} ({{ $percentage }}%)
                    @endif
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
                        @elseif(str_starts_with($field, 'exam_'))
                            @php
                                $examId = str_replace('exam_', '', $field);
                            @endphp
                            {{ $sc->result->{$examId} ?? '-' }}
                        @elseif($field === 'total')
                            {{ $sc->result->total ?? '-' }}
                        @elseif($field === 'status')
                            @php
                                $status = $sc->result->status ?? '';
                                $color = match($status) {
                                    'passed'  => '#198754',   // سبز
                                    'failed'  => '#dc3545',   // قرمز
                                    'makeup'    => '#ffc107',   // زرد
                                    'in_progress' => '#0dcaf0',   // آبی
                                    default   => '#6c757d',   // خاکستری
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
                        @elseif(in_array($field, ['student_code','name','father_name']))
                            {{ $sc->student?->$field }}
                        @else
                            {{ $sc->result->{$field} ?? '-' }}
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
<br><br>

<table width="100%" style="margin-top:20px;">
    <tr>
        <!-- چپ -->
        <td width="50%" style="text-align:center; vertical-align:bottom;">
            
            <div style="height:40px;"></div>

            <div style="border-top:1px solid #000; width:60%; margin:0 auto 8px;"></div>

            <div style="margin-top:5px;">
                <strong>{{ __('label.exam_unit') }}</strong>
            </div>

            <div style="margin-top:2px;">
                {{ __('label.signature') }}
            </div>

        </td>

        <!-- راست -->
        <td width="50%" style="text-align:center; vertical-align:bottom;">

            <div style="height:40px;"></div>

            <div style="border-top:1px solid #000; width:60%; margin:0 auto 8px;"></div>

            <div style="margin-top:5px;">
                <strong>{{ __('label.campus_incharge') }}</strong>
            </div>

            <div style="margin-top:2px;">
                {{ __('label.signature') }}
            </div>

        </td>
    </tr>
</table>
