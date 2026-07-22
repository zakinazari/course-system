    
<style>
    body, html {
        background: #fff !important;
        -webkit-print-color-adjust: exact;
    }

    body * {
        visibility: hidden;
    }

    #printArea, #printArea * {
        visibility: visible;
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

/* Logo */
.logo-container {
    text-align: center;
    margin-bottom: 15px;
}

.logo-container .logo {
    max-width: 120px;
}

</style>

<div id="printArea">
    <!-- Logo -->
    <div style="text-align:center;margin-bottom:10px;">
        <img src="{{ getLogo() }}" alt="Logo" style="height:70px;">
    </div>
    <!-- Title -->
    <h2 style="text-align:center;">
        {{ __('label.system_log_list') }}
    </h2>

    <!-- Date range, aligned left, close to table -->
    <div style="margin-bottom:3px; font-size:12px; text-align:left;">
    {{ __('label.from_date') }}: {{ $search['from'] ?? '---' }} &nbsp;&nbsp; {{ __('label.to_date') }}: {{ $search['to'] ?? '---' }}
    </div>

    <table class="data-table"  cellspacing="0" cellpadding="5" width="100%">
        <thead >
            <tr>
                @if(in_array('no', $selectedFields)) <th>{{ __('label.no') }}</th> @endif
                @if(in_array('user_id', $selectedFields)) <th>{{ __('label.user') }}</th> @endif
                @if(in_array('section', $selectedFields)) <th>{{ __('label.section') }}</th> @endif
                @if(in_array('type_id', $selectedFields)) <th>{{ __('label.action') }}</th> @endif
                @if(in_array('created_at', $selectedFields)) <th>{{ __('label.date') }}</th> @endif
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $i => $log)
                <tr>
                    @if(in_array('no', $selectedFields)) <td>{{ $i + 1 }}</td> @endif
                    @if(in_array('user_id', $selectedFields)) <td>{{ $log->user?->email }}</td> @endif
                    @if(in_array('section', $selectedFields)) <td>{{ $log->section }}</td> @endif

                    @if(in_array('type_id', $selectedFields)) 
                        
                    <td>
                        @php
                            $type = $log->type_id;

                            $color = match($type) {
                                1 => '#ffc107', // Warning
                                2 => '#52a57e', // Create
                                3 => '#0dcaf0', // Edit
                                4 => '#dc3545', // Delete
                                default => '#6c757d',
                            };

                            $label = match($type) {
                                1 => __('label.warning'),
                                2 => __('label.create'),
                                3 => __('label.edit'),
                                4 => __('label.delete'),
                                default => __('label.unknown'),
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
                            {{ $label }}
                        </span>
                    </td>

                    @endif

                    @if(in_array('created_at', $selectedFields)) <td>{{ $log->created_at->format('Y/m/d H:i:A') }}</td> @endif
                </tr>
            @endforeach
        </tbody>
  
    </table>
</div>
