@extends('layouts.app')

@section('content')
<div style="max-width:1200px;margin:0 auto;padding:20px;">

    <h1 style="margin:0 0 8px 0;font-size:40px;font-weight:800;">Timetable Runs</h1>
    <div style="color:#6b7280;margin-bottom:18px;">
        Pick a run to view dashboard, master timetable, conflicts, analytics and quality.
    </div>

    @forelse($runs as $run)
        @php
            $sid = (int) $run->id;
            $s = $runStats[$sid] ?? ['entries'=>0,'teachers'=>0,'classes'=>0,'subjects'=>0];
            $card = "border:1px solid #e5e7eb;border-radius:14px;padding:16px;background:#fff;margin-bottom:14px;";
            $pill = "display:inline-block;padding:6px 10px;border:1px solid #e5e7eb;border-radius:999px;font-size:13px;background:#f9fafb;margin-right:8px;margin-top:8px;";
            $btn = "display:inline-block;padding:10px 14px;border:1px solid #e5e7eb;border-radius:10px;text-decoration:none;margin-right:8px;margin-top:10px;";
        @endphp

        <div style="{{ $card }}">
            <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;align-items:center;">
                <div>
                    <div style="font-size:28px;font-weight:800;">Run #{{ $run->id }}</div>
                    <div style="color:#6b7280;font-size:13px;">
                        Name: {{ $run->name ?? '-' }} |
                        Created: {{ $run->created_at ?? '-' }}
                    </div>

                    <div style="margin-top:10px;">
                        <span style="{{ $pill }}">Entries: <b>{{ $s['entries'] }}</b></span>
                        <span style="{{ $pill }}">Teachers: <b>{{ $s['teachers'] }}</b></span>
                        <span style="{{ $pill }}">Classes: <b>{{ $s['classes'] }}</b></span>
                        <span style="{{ $pill }}">Subjects: <b>{{ $s['subjects'] }}</b></span>
                    </div>
                </div>

                <div>
                    <a href="{{ route('timetables.dashboard', ['runId'=>$run->id]) }}" style="{{ $btn }}">Dashboard</a>
                    <a href="{{ route('timetables.master', ['runId'=>$run->id]) }}" style="{{ $btn }}">Master</a>
                    <a href="{{ route('timetables.conflicts', ['runId'=>$run->id]) }}" style="{{ $btn }}">Conflicts</a>
                    <a href="{{ route('timetables.analytics.teachers', ['runId'=>$run->id]) }}" style="{{ $btn }}">Teacher Analytics</a>
                    <a href="{{ route('timetables.analytics.classes', ['runId'=>$run->id]) }}" style="{{ $btn }}">Class Analytics</a>
                    <a href="{{ route('timetables.analytics.subjects', ['runId'=>$run->id]) }}" style="{{ $btn }}">Subject Analytics</a>
                    <a href="{{ route('timetables.quality', ['runId'=>$run->id]) }}" style="{{ $btn }}">Quality</a>
                </div>
            </div>
        </div>
    @empty
        <div style="padding:18px;border:1px solid #e5e7eb;border-radius:12px;background:#fff;">
            No runs found.
        </div>
    @endforelse

</div>
@endsection
