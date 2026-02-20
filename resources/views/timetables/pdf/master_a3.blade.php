<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        h1 { margin: 0 0 6px 0; font-size: 18px; }
        .meta { color: #555; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 4px; }
        th { background: #f3f4f6; text-align: left; }
        .day { background:#111827; color:#fff; font-weight:bold; }
        .small { font-size: 9px; color:#444; }
    </style>
</head>
<body>
    <h1>MASTER TIMETABLE — Run #{{ $runId }} @if($compact) (COMPACT) @endif</h1>
    <div class="meta">
        Exported: {{ now() }} &nbsp; | &nbsp;
        Rows: {{ count($entries) }}
    </div>

    @php
        $grouped = collect($entries)->groupBy('day');
    @endphp

    @foreach($grouped as $day => $rows)
        <div class="day" style="padding:6px;margin-top:8px;">{{ $day }}</div>

        <table>
            <thead>
                <tr>
                    <th style="width:60px;">Period</th>
                    <th style="width:80px;">Class</th>
                    <th style="width:80px;">Teacher</th>
                    <th style="width:90px;">Subject</th>
                </tr>
            </thead>
            <tbody>
            @foreach($rows as $e)
                <tr>
                    <td>{{ $e->period_index }}</td>
                    <td>
                        @if($compact)
                            C{{ $e->class_id }}
                        @else
                            Class #{{ $e->class_id }}
                        @endif
                    </td>
                    <td>
                        @if($compact)
                            T{{ $e->teacher_id }}
                        @else
                            Teacher #{{ $e->teacher_id }}
                        @endif
                    </td>
                    <td>
                        @if($compact)
                            S{{ $e->subject_id }}
                        @else
                            Subject #{{ $e->subject_id }}
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endforeach

    <div class="small" style="margin-top:10px;">
        Tip: Use compact mode: <b>/timetables/run/{{ $runId }}/master/pdf?compact=1</b>
    </div>
</body>
</html>
