<!doctype html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: DejaVu Sans, Arial; font-size: 12px; }
table { border-collapse: collapse; width: 100%; }
th, td { border: 1px solid #333; padding: 6px; }
th { background: #f2f2f2; }
</style>
</head>
<body>

<h2>Teacher Timetable — Teacher {{ $teacherId }}</h2>
<p>Run #{{ $run->id }} — {{ $run->name }}</p>

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
<td>{{ $e->class_id }}</td>
<td>{{ $e->subject_id }}</td>
</tr>
@endforeach
</tbody>
</table>

</body>
</html>
