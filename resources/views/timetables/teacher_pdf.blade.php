<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Class {{ $classId }} Timetable</title>
    <style>
        body{ font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; }
        table{ width:100%; border-collapse: collapse; margin-top: 10px;}
        th,td{ border:1px solid #ddd; padding:8px; text-align:left; }
        h2{ margin:0; }
    </style>
</head>
<body>
    <h2>Class #{{ $classId }} — Run #{{ $runId }}</h2>
    <table>
        <thead>
            <tr>
                <th>Day</th><th>Period</th><th>Subject</th><th>Teacher</th>
            </tr>
        </thead>
        <tbody>
        @foreach($entries as $e)
            <tr>
                <td>{{ $e->day }}</td>
                <td>{{ $e->period }}</td>
                <td>{{ $e->subject_name ?: 'Subject' }}</td>
                <td>{{ $e->teacher_name ?: 'Teacher' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
