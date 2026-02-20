@extends('layouts.app')

@section('content')

<div style="padding:20px;">
    <h1>Class Timetable — Class #{{ $classId }} (Run #{{ $runId }})</h1>

    <div style="margin:15px 0;">
        <a href="{{ url('/timetables/runs') }}">← Back to Runs</a> |
        <a href="{{ url('/timetables/run/'.$runId.'/class/'.$classId.'/pdf') }}">
            Download Class PDF
        </a>
    </div>

    <table border="1" cellpadding="6" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Day</th>
                <th>Period</th>
                <th>Subject</th>
                <th>Teacher</th>
            </tr>
        </thead>
        <tbody>
            @forelse($entries as $e)
                <tr>
                    <td>{{ $e->day }}</td>
                    <td>{{ $e->period }}</td>
                    <td>Subject #{{ $e->subject_id }}</td>
                    <td>
                        @if($e->teacher_id)
                            Teacher #{{ $e->teacher_id }}
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No timetable entries found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
