<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        h1 { margin: 0 0 6px 0; font-size: 18px; }
        .meta { color: #555; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background: #f3f4f6; text-align: left; }
    </style>
</head>
<body>
    <h1>TEACHER TIMETABLE — Teacher #{{ $teacherId }} (Run #{{ $runId }})</h1>
    <div class="meta">Exported: {{ now() }} | Rows: {{ count($entries) }}</div>

    <table>
        <thead>
            <tr>
                <th>Day</th>
                <th>Period</th>
                <th>Class</th>
                <th>Subject</th>
            </tr>
        </thead>
        <tbody>
        @foreach($entries as $e)
            <tr>
                <td>{{ $e->day }}</td>
                <td>{{ $e->period_index }}</td>
                <td>Class #{{ $e->class_id }}</td>
                <td>Subject #{{ $e->subject_id }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
