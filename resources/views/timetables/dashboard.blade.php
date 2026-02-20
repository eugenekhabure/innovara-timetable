@extends('layouts.app')

@section('content')
<div style="max-width:1200px;margin:0 auto;padding:20px;">

    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
        <div>
            <h1 style="margin:0;font-size:40px;font-weight:800;">Run #{{ $runId }} Dashboard</h1>
            <div style="margin-top:6px;color:#6b7280;">
                Teachers avg load: <b>{{ $teacherAvg ?? 0 }}</b> lessons/week
            </div>
        </div>

        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="{{ route('timetables.runs') }}" style="padding:10px 14px;border:1px solid #e5e7eb;border-radius:10px;text-decoration:none;">Runs</a>
            <a href="{{ route('timetables.master', ['runId'=>$runId]) }}" style="padding:10px 14px;border:1px solid #e5e7eb;border-radius:10px;text-decoration:none;">Master</a>
            <a href="{{ route('timetables.conflicts', ['runId'=>$runId]) }}" style="padding:10px 14px;border:1px solid #e5e7eb;border-radius:10px;text-decoration:none;">Conflicts</a>
            <a href="{{ route('timetables.analytics.teachers', ['runId'=>$runId]) }}" style="padding:10px 14px;border:1px solid #e5e7eb;border-radius:10px;text-decoration:none;">Teacher Analytics</a>
            <a href="{{ route('timetables.analytics.classes', ['runId'=>$runId]) }}" style="padding:10px 14px;border:1px solid #e5e7eb;border-radius:10px;text-decoration:none;">Class Analytics</a>
            <a href="{{ route('timetables.analytics.subjects', ['runId'=>$runId]) }}" style="padding:10px 14px;border:1px solid #e5e7eb;border-radius:10px;text-decoration:none;">Subject Analytics</a>
            <a href="{{ route('timetables.quality', ['runId'=>$runId]) }}" style="padding:10px 14px;border:1px solid #e5e7eb;border-radius:10px;text-decoration:none;">Quality</a>
        </div>
    </div>

    @php
        $stats = $stats ?? ['entries'=>0,'teachers'=>0,'classes'=>0,'subjects'=>0];
        $teacherConflictCount = $teacherConflictCount ?? 0;
        $classConflictCount = $classConflictCount ?? 0;

        $charts = $charts ?? [
            'teacher_labels'=>[],
            'teacher_values'=>[],
            'class_labels'=>[],
            'class_values'=>[],
            'subject_labels'=>[],
            'subject_values'=>[],
            'teacher_status_labels'=>[],
            'teacher_status_values'=>[],
        ];

        $card = "border:1px solid #e5e7eb;border-radius:12px;padding:14px 16px;min-width:180px;background:#fff;";
    @endphp

    {{-- Stats cards --}}
    <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:18px;">
        <div style="{{ $card }}">
            <div style="color:#6b7280;font-size:12px;">Total Entries</div>
            <div style="font-size:26px;font-weight:800;">{{ $stats['entries'] }}</div>
        </div>
        <div style="{{ $card }}">
            <div style="color:#6b7280;font-size:12px;">Teachers</div>
            <div style="font-size:26px;font-weight:800;">{{ $stats['teachers'] }}</div>
        </div>
        <div style="{{ $card }}">
            <div style="color:#6b7280;font-size:12px;">Classes</div>
            <div style="font-size:26px;font-weight:800;">{{ $stats['classes'] }}</div>
        </div>
        <div style="{{ $card }}">
            <div style="color:#6b7280;font-size:12px;">Subjects</div>
            <div style="font-size:26px;font-weight:800;">{{ $stats['subjects'] }}</div>
        </div>

        <div style="border:1px solid #fee2e2;border-radius:12px;padding:14px 16px;min-width:220px;background:#fff5f5;">
            <div style="color:#991b1b;font-size:12px;">Teacher Conflicts</div>
            <div style="font-size:26px;font-weight:800;color:#991b1b;">{{ $teacherConflictCount }}</div>
        </div>

        <div style="border:1px solid #fee2e2;border-radius:12px;padding:14px 16px;min-width:220px;background:#fff5f5;">
            <div style="color:#991b1b;font-size:12px;">Class Conflicts</div>
            <div style="font-size:26px;font-weight:800;color:#991b1b;">{{ $classConflictCount }}</div>
        </div>
    </div>

    {{-- Charts --}}
    <div style="display:flex;gap:14px;flex-wrap:wrap;margin-top:18px;">
        <div style="flex:1;min-width:420px;border:1px solid #e5e7eb;border-radius:12px;padding:14px;background:#fff;">
            <h3 style="margin:0 0 10px 0;">Top Teacher Loads</h3>
            <canvas id="teacherChart" height="140"></canvas>
        </div>

        <div style="flex:1;min-width:420px;border:1px solid #e5e7eb;border-radius:12px;padding:14px;background:#fff;">
            <h3 style="margin:0 0 10px 0;">Top Class Loads</h3>
            <canvas id="classChart" height="140"></canvas>
        </div>

        <div style="flex:1;min-width:420px;border:1px solid #e5e7eb;border-radius:12px;padding:14px;background:#fff;">
            <h3 style="margin:0 0 10px 0;">Top Subject Demand</h3>
            <canvas id="subjectChart" height="140"></canvas>
        </div>

        <div style="flex:1;min-width:420px;border:1px solid #e5e7eb;border-radius:12px;padding:14px;background:#fff;">
            <h3 style="margin:0 0 10px 0;">Teacher Status Mix</h3>
            <canvas id="statusChart" height="140"></canvas>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const teacherLabels = @json($charts['teacher_labels'] ?? []);
    const teacherValues = @json($charts['teacher_values'] ?? []);

    const classLabels = @json($charts['class_labels'] ?? []);
    const classValues = @json($charts['class_values'] ?? []);

    const subjectLabels = @json($charts['subject_labels'] ?? []);
    const subjectValues = @json($charts['subject_values'] ?? []);

    const statusLabels = @json($charts['teacher_status_labels'] ?? []);
    const statusValues = @json($charts['teacher_status_values'] ?? []);

    if (document.getElementById('teacherChart')) {
        new Chart(document.getElementById('teacherChart'), {
            type: 'bar',
            data: { labels: teacherLabels, datasets: [{ label: 'Lessons', data: teacherValues }] },
            options: { responsive:true }
        });
    }

    if (document.getElementById('classChart')) {
        new Chart(document.getElementById('classChart'), {
            type: 'bar',
            data: { labels: classLabels, datasets: [{ label: 'Lessons', data: classValues }] },
            options: { responsive:true }
        });
    }

    if (document.getElementById('subjectChart')) {
        new Chart(document.getElementById('subjectChart'), {
            type: 'bar',
            data: { labels: subjectLabels, datasets: [{ label: 'Lessons', data: subjectValues }] },
            options: { responsive:true }
        });
    }

    if (document.getElementById('statusChart')) {
        new Chart(document.getElementById('statusChart'), {
            type: 'pie',
            data: { labels: statusLabels, datasets: [{ data: statusValues }] },
            options: { responsive:true }
        });
    }
</script>
@endsection
