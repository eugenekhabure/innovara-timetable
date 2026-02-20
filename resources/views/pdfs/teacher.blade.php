<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; }
        h1 { font-size: 14px; margin: 0 0 8px; }
        .meta { color: #555; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 5px; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Teacher Timetable — Teacher #{{ $teacherId }} (Run #{{ $runId }})</h1>
    <div class="meta">Generated: {{ now() }}</div>

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
                <td>{{ $e->period }}</td>
                <td>Class #{{ $e->class_id }}</td>
                <td>Subject #{{ $e->subject_id }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
