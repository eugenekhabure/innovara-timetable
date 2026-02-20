@extends('layouts.app')

@section('content')

<div style="padding:20px; max-width:1000px; margin:0 auto;">

    <h1>Teacher Load Analytics — Run #{{ $runId }}</h1>

    <div style="margin:15px 0;">
        <a href="{{ url('/timetables/run/'.$runId.'/master') }}">← Back to Master</a> |
        <a href="{{ url('/timetables/run/'.$runId.'/analytics/teachers/csv') }}">
            Download CSV
        </a>
    </div>

    <table border="1" cellpadding="8" cellspacing="0" width="100%" style="border-collapse:collapse;">
        <thead style="background:#f9fafb;">
            <tr>
                <th>Teacher ID</th>
                <th>Total Lessons</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($loads as $load)
                @php
                    $status = 'Balanced';
                    $bg = '';

                    if ($load->total > 30) {
                        $status = 'Overloaded';
                        $bg = 'background:#fff1f2;';
                    }

                    if ($load->total < 10) {
                        $status = 'Underloaded';
                        $bg = 'background:#fff7ed;';
                    }
                @endphp

                <tr style="{{ $bg }}">
                    <td>
                        <a href="{{ url('/timetables/run/'.$runId.'/teacher/'.$load->teacher_id) }}">
                            Teacher {{ $load->teacher_id }}
                        </a>
                    </td>
                    <td>{{ $load->total }}</td>
                    <td>{{ $status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

@endsection
