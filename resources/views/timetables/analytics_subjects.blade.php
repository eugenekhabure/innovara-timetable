@extends('layouts.app')

@section('content')
<div style="max-width:1200px;margin:0 auto;padding:20px;">

    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
        <div>
            <a href="{{ route('timetables.dashboard', ['runId'=>$runId]) }}" style="text-decoration:none;color:#2563eb;">← Back to Dashboard</a>
            <h1 style="margin:10px 0 0;font-size:38px;font-weight:900;">Subject Analytics — Run #{{ $runId }}</h1>
        </div>

        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="{{ route('timetables.runs') }}" style="padding:10px 14px;border:1px solid #e5e7eb;border-radius:10px;text-decoration:none;">Runs</a>
            <a href="{{ route('timetables.conflicts', ['runId'=>$runId]) }}" style="padding:10px 14px;border:1px solid #e5e7eb;border-radius:10px;text-decoration:none;">Conflicts</a>
            <a href="{{ route('timetables.quality', ['runId'=>$runId]) }}" style="padding:10px 14px;border:1px solid #e5e7eb;border-radius:10px;text-decoration:none;">Quality</a>
        </div>
    </div>

    @php
        $summary = $summary ?? [];
        $loads = $loads ?? collect();
    @endphp

    <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:16px;">
        <div style="border:1px solid #e5e7eb;border-radius:12px;padding:12px 14px;background:#fff;">
            Subjects: <b>{{ $summary['total_subjects'] ?? $loads->count() }}</b>
        </div>
        <div style="border:1px solid #e5e7eb;border-radius:12px;padding:12px 14px;background:#fff;">
            Total lessons: <b>{{ $summary['total_lessons'] ?? 0 }}</b>
        </div>
    </div>

    <div style="margin-top:16px;border:1px solid #e5e7eb;border-radius:12px;overflow:auto;background:#fff;">
        <table style="width:100%;border-collapse:collapse;min-width:900px;">
            <thead>
                <tr style="background:#f9fafb;">
                    <th style="text-align:left;padding:10px;border-bottom:1px solid #e5e7eb;">Subject</th>
                    <th style="text-align:left;padding:10px;border-bottom:1px solid #e5e7eb;">Lessons</th>
                    <th style="text-align:left;padding:10px;border-bottom:1px solid #e5e7eb;">Days</th>
                    <th style="text-align:left;padding:10px;border-bottom:1px solid #e5e7eb;">Avg/Day</th>
                </tr>
            </thead>
            <tbody>
            @forelse($loads as $r)
                <tr>
                    <td style="padding:10px;border-bottom:1px solid #f1f5f9;">
                        {{ $r->subject_name ?? ('Subject #'.$r->subject_id) }}
                    </td>
                    <td style="padding:10px;border-bottom:1px solid #f1f5f9;"><b>{{ $r->total }}</b></td>
                    <td style="padding:10px;border-bottom:1px solid #f1f5f9;">{{ $r->days ?? '-' }}</td>
                    <td style="padding:10px;border-bottom:1px solid #f1f5f9;">{{ $r->avg_per_day ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" style="padding:14px;color:#6b7280;">No data.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
