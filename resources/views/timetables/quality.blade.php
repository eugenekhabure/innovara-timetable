@extends('layouts.app')

@section('content')
<div style="max-width:1100px;margin:0 auto;padding:20px;">

    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
        <div>
            <a href="{{ route('timetables.runs') }}" style="text-decoration:none;color:#2563eb;">← Runs</a>
            <h1 style="margin:10px 0 0 0;font-size:40px;font-weight:800;">Quality Score — Run #{{ $runId }}</h1>
            <div style="color:#6b7280;margin-top:6px;">
                Weighted score across balance + conflicts
            </div>
        </div>

        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="{{ route('timetables.dashboard', ['runId'=>$runId]) }}" style="padding:10px 14px;border:1px solid #e5e7eb;border-radius:10px;text-decoration:none;">Dashboard</a>
            <a href="{{ route('timetables.master', ['runId'=>$runId]) }}" style="padding:10px 14px;border:1px solid #e5e7eb;border-radius:10px;text-decoration:none;">Master</a>
            <a href="{{ route('timetables.conflicts', ['runId'=>$runId]) }}" style="padding:10px 14px;border:1px solid #e5e7eb;border-radius:10px;text-decoration:none;">Conflicts</a>
        </div>
    </div>

    @php
        $card = "border:1px solid #e5e7eb;border-radius:12px;padding:14px 16px;background:#fff;min-width:220px;";
        $finalScore = $final ?? 0;
    @endphp

    <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:18px;">
        <div style="{{ $card }}">
            <div style="color:#6b7280;font-size:12px;">Final Score</div>
            <div style="font-size:34px;font-weight:900;">{{ $finalScore }}/100</div>
        </div>

        <div style="border:1px solid #fee2e2;border-radius:12px;padding:14px 16px;background:#fff5f5;min-width:220px;">
            <div style="color:#991b1b;font-size:12px;">Teacher Conflicts</div>
            <div style="font-size:28px;font-weight:900;color:#991b1b;">{{ $teacherConflictCount ?? 0 }}</div>
        </div>

        <div style="border:1px solid #fee2e2;border-radius:12px;padding:14px 16px;background:#fff5f5;min-width:220px;">
            <div style="color:#991b1b;font-size:12px;">Class Conflicts</div>
            <div style="font-size:28px;font-weight:900;color:#991b1b;">{{ $classConflictCount ?? 0 }}</div>
        </div>

        <div style="{{ $card }}">
            <div style="color:#6b7280;font-size:12px;">Balance Score</div>
            <div style="font-size:28px;font-weight:900;">{{ $balanceScore ?? 0 }}%</div>
        </div>

        <div style="{{ $card }}">
            <div style="color:#6b7280;font-size:12px;">Teacher Status</div>
            <div style="margin-top:8px;">
                Balanced: <b>{{ $balanced ?? 0 }}</b><br>
                Overloaded: <b>{{ $overloaded ?? 0 }}</b><br>
                Underloaded: <b>{{ $underloaded ?? 0 }}</b>
            </div>
        </div>
    </div>

    <div style="margin-top:18px;border:1px solid #e5e7eb;border-radius:12px;padding:14px;background:#fff;">
        <h3 style="margin:0 0 10px 0;">Interpretation</h3>
        <ul style="margin:0;padding-left:18px;color:#374151;">
            <li>Higher score = better overall timetable quality.</li>
            <li>Conflicts reduce the score significantly.</li>
            <li>Balance improves when most teachers are within ±15% of average load.</li>
        </ul>
    </div>

</div>
@endsection
