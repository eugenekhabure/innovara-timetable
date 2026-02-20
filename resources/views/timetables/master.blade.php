@extends('layouts.app')

@section('content')
<div style="max-width:1200px;margin:0 auto;padding:20px;">

    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
        <div>
            <a href="{{ route('timetables.runs') }}" style="text-decoration:none;color:#2563eb;">← Runs</a>
            <h1 style="margin:10px 0 0 0;font-size:40px;font-weight:800;">Master Timetable — Run #{{ $runId }}</h1>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="{{ route('timetables.dashboard', ['runId'=>$runId]) }}" style="padding:10px 14px;border:1px solid #e5e7eb;border-radius:10px;text-decoration:none;">Dashboard</a>
            <a href="{{ route('timetables.conflicts', ['runId'=>$runId]) }}" style="padding:10px 14px;border:1px solid #e5e7eb;border-radius:10px;text-decoration:none;">Conflicts</a>
        </div>
    </div>

    <div style="margin-top:14px;color:#6b7280;">
        Showing raw timetable entries (day, period, class, teacher, subject).
    </div>

    <div style="margin-top:16px;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;background:#fff;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#f9fafb;">
                    <th style="text-align:left;padding:10px;border-bottom:1px solid #e5e7eb;">Day</th>
                    <th style="text-align:left;padding:10px;border-bottom:1px solid #e5e7eb;">Period</th>
                    <th style="text-align:left;padding:10px;border-bottom:1px solid #e5e7eb;">Class</th>
                    <th style="text-align:left;padding:10px;border-bottom:1px solid #e5e7eb;">Teacher</th>
                    <th style="text-align:left;padding:10px;border-bottom:1px solid #e5e7eb;">Subject</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entries as $e)
                    <tr>
                        <td style="padding:10px;border-bottom:1px solid #f1f5f9;">{{ $e->day }}</td>
                        <td style="padding:10px;border-bottom:1px solid #f1f5f9;">{{ $e->period_index }}</td>
                        <td style="padding:10px;border-bottom:1px solid #f1f5f9;">{{ $e->class_id ?? '-' }}</td>
                        <td style="padding:10px;border-bottom:1px solid #f1f5f9;">{{ $e->teacher_id ?? '-' }}</td>
                        <td style="padding:10px;border-bottom:1px solid #f1f5f9;">{{ $e->subject_id ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding:14px;color:#6b7280;">No entries for this run.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
