@extends('layouts.app')

@section('content')
<div style="max-width:1200px;margin:0 auto;padding:20px;">

    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
        <div>
            <a href="{{ route('timetables.runs') }}" style="text-decoration:none;color:#2563eb;">← Runs</a>
            <h1 style="margin:10px 0 0 0;font-size:40px;font-weight:800;">Conflicts — Run #{{ $runId }}</h1>
        </div>

        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="{{ route('timetables.dashboard', ['runId'=>$runId]) }}" style="padding:10px 14px;border:1px solid #e5e7eb;border-radius:10px;text-decoration:none;">Dashboard</a>
            <a href="{{ route('timetables.master', ['runId'=>$runId]) }}" style="padding:10px 14px;border:1px solid #e5e7eb;border-radius:10px;text-decoration:none;">Master</a>
        </div>
    </div>

    <div style="display:flex;gap:14px;flex-wrap:wrap;margin-top:16px;">
        <div style="flex:1;min-width:420px;border:1px solid #fee2e2;border-radius:12px;padding:14px;background:#fff5f5;">
            <h3 style="margin:0 0 10px 0;color:#991b1b;">Teacher Conflicts</h3>

            <div style="border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;background:#fff;">
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f9fafb;">
                            <th style="text-align:left;padding:10px;border-bottom:1px solid #e5e7eb;">Day</th>
                            <th style="text-align:left;padding:10px;border-bottom:1px solid #e5e7eb;">Period</th>
                            <th style="text-align:left;padding:10px;border-bottom:1px solid #e5e7eb;">Teacher</th>
                            <th style="text-align:left;padding:10px;border-bottom:1px solid #e5e7eb;">Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teacherConflicts as $c)
                            <tr>
                                <td style="padding:10px;border-bottom:1px solid #f1f5f9;">{{ $c->day }}</td>
                                <td style="padding:10px;border-bottom:1px solid #f1f5f9;">
                                    {{ $c->period ?? $c->period_index ?? '-' }}
                                </td>
                                <td style="padding:10px;border-bottom:1px solid #f1f5f9;">{{ $c->teacher_id }}</td>
                                <td style="padding:10px;border-bottom:1px solid #f1f5f9;">{{ $c->cnt }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" style="padding:14px;color:#6b7280;">No teacher conflicts.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div style="flex:1;min-width:420px;border:1px solid #fee2e2;border-radius:12px;padding:14px;background:#fff5f5;">
            <h3 style="margin:0 0 10px 0;color:#991b1b;">Class Conflicts</h3>

            <div style="border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;background:#fff;">
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f9fafb;">
                            <th style="text-align:left;padding:10px;border-bottom:1px solid #e5e7eb;">Day</th>
                            <th style="text-align:left;padding:10px;border-bottom:1px solid #e5e7eb;">Period</th>
                            <th style="text-align:left;padding:10px;border-bottom:1px solid #e5e7eb;">Class</th>
                            <th style="text-align:left;padding:10px;border-bottom:1px solid #e5e7eb;">Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classConflicts as $c)
                            <tr>
                                <td style="padding:10px;border-bottom:1px solid #f1f5f9;">{{ $c->day }}</td>
                                <td style="padding:10px;border-bottom:1px solid #f1f5f9;">
                                    {{ $c->period ?? $c->period_index ?? '-' }}
                                </td>
                                <td style="padding:10px;border-bottom:1px solid #f1f5f9;">{{ $c->class_id }}</td>
                                <td style="padding:10px;border-bottom:1px solid #f1f5f9;">{{ $c->cnt }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" style="padding:14px;color:#6b7280;">No class conflicts.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
