<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TimetableRun;
use Barryvdh\DomPDF\Facade\Pdf;

class TimetableController extends Controller
{
    public function runs()
    {
        $runs = TimetableRun::query()->orderByDesc('id')->get();

        $runStats = [];
        foreach ($runs as $r) {
            $runId = (int) $r->id;

            $runStats[$runId] = [
                'entries'  => (int) DB::table('timetable_entries')->where('timetable_run_id', $runId)->count(),
                'teachers' => (int) DB::table('timetable_entries')->where('timetable_run_id', $runId)->whereNotNull('teacher_id')->distinct('teacher_id')->count('teacher_id'),
                'classes'  => (int) DB::table('timetable_entries')->where('timetable_run_id', $runId)->whereNotNull('class_id')->distinct('class_id')->count('class_id'),
                'subjects' => (int) DB::table('timetable_entries')->where('timetable_run_id', $runId)->whereNotNull('subject_id')->distinct('subject_id')->count('subject_id'),
            ];
        }

        return view('timetables.runs', compact('runs', 'runStats'));
    }

    public function dashboard(int $runId)
    {
        $run = TimetableRun::findOrFail($runId);

        $stats = [
            'entries'  => (int) DB::table('timetable_entries')->where('timetable_run_id', $runId)->count(),
            'teachers' => (int) DB::table('timetable_entries')->where('timetable_run_id', $runId)->whereNotNull('teacher_id')->distinct('teacher_id')->count('teacher_id'),
            'classes'  => (int) DB::table('timetable_entries')->where('timetable_run_id', $runId)->whereNotNull('class_id')->distinct('class_id')->count('class_id'),
            'subjects' => (int) DB::table('timetable_entries')->where('timetable_run_id', $runId)->whereNotNull('subject_id')->distinct('subject_id')->count('subject_id'),
        ];

        $teacherLoadsTop = DB::table('timetable_entries')
            ->select('teacher_id', DB::raw('COUNT(*) as total'))
            ->where('timetable_run_id', $runId)
            ->whereNotNull('teacher_id')
            ->groupBy('teacher_id')
            ->orderByDesc('total')
            ->limit(12)
            ->get();

        $teacherAvg = $teacherLoadsTop->count() ? round($teacherLoadsTop->avg('total'), 2) : 0;

        $classLoadsTop = DB::table('timetable_entries')
            ->select('class_id', DB::raw('COUNT(*) as total'))
            ->where('timetable_run_id', $runId)
            ->whereNotNull('class_id')
            ->groupBy('class_id')
            ->orderByDesc('total')
            ->limit(12)
            ->get();

        // SQLite-safe label using || (not CONCAT)
        $subjectDemandTop = DB::table('timetable_entries as e')
            ->select(DB::raw("('Subject #' || e.subject_id) as label"), DB::raw('COUNT(*) as total'))
            ->where('e.timetable_run_id', $runId)
            ->whereNotNull('e.subject_id')
            ->groupBy('label')
            ->orderByDesc('total')
            ->limit(12)
            ->get();

        $teacherConflictCount = (int) DB::table('timetable_entries as e')
            ->select('e.day', 'e.period_index', 'e.teacher_id', DB::raw('COUNT(*) as cnt'))
            ->where('e.timetable_run_id', $runId)
            ->whereNotNull('e.teacher_id')
            ->groupBy('e.day', 'e.period_index', 'e.teacher_id')
            ->having('cnt', '>', 1)
            ->get()
            ->sum(fn($r) => max(0, ((int)$r->cnt) - 1));

        $classConflictCount = (int) DB::table('timetable_entries as e')
            ->select('e.day', 'e.period_index', 'e.class_id', DB::raw('COUNT(*) as cnt'))
            ->where('e.timetable_run_id', $runId)
            ->whereNotNull('e.class_id')
            ->groupBy('e.day', 'e.period_index', 'e.class_id')
            ->having('cnt', '>', 1)
            ->get()
            ->sum(fn($r) => max(0, ((int)$r->cnt) - 1));

        $allTeacherLoads = DB::table('timetable_entries')
            ->select('teacher_id', DB::raw('COUNT(*) as total'))
            ->where('timetable_run_id', $runId)
            ->whereNotNull('teacher_id')
            ->groupBy('teacher_id')
            ->get();

        $avg = $allTeacherLoads->count() ? (float) $allTeacherLoads->avg('total') : 0;
        $low = $avg * 0.85;
        $high = $avg * 1.15;

        $balanced = 0; $overloaded = 0; $underloaded = 0;
        foreach ($allTeacherLoads as $t) {
            if ($t->total < $low) $underloaded++;
            elseif ($t->total > $high) $overloaded++;
            else $balanced++;
        }

        $charts = [
            'teacher_labels' => $teacherLoadsTop->map(fn($r) => "Teacher #{$r->teacher_id}")->values(),
            'teacher_values' => $teacherLoadsTop->pluck('total')->values(),

            'class_labels'   => $classLoadsTop->map(fn($r) => "Class #{$r->class_id}")->values(),
            'class_values'   => $classLoadsTop->pluck('total')->values(),

            'subject_labels' => $subjectDemandTop->pluck('label')->values(),
            'subject_values' => $subjectDemandTop->pluck('total')->values(),

            'teacher_status_labels' => ['Balanced', 'Overloaded', 'Underloaded'],
            'teacher_status_values' => [$balanced, $overloaded, $underloaded],
        ];

        return view('timetables.dashboard', compact(
            'run', 'runId', 'stats', 'teacherAvg',
            'teacherConflictCount', 'classConflictCount', 'charts'
        ));
    }

    public function master(int $runId)
    {
        TimetableRun::findOrFail($runId);

        $entries = DB::table('timetable_entries')
            ->where('timetable_run_id', $runId)
            ->orderBy('day')
            ->orderBy('period_index')
            ->orderBy('class_id')
            ->get();

        return view('timetables.master', compact('runId', 'entries'));
    }

    public function masterPdf(Request $request, int $runId)
    {
        $run = TimetableRun::findOrFail($runId);

        $entries = DB::table('timetable_entries')
            ->where('timetable_run_id', $runId)
            ->orderBy('day')
            ->orderBy('period_index')
            ->orderBy('class_id')
            ->get();

        $compact = (int) $request->query('compact', 0) === 1;

        $pdf = Pdf::loadView('timetables.pdf.master_a3', [
            'run' => $run,
            'runId' => $runId,
            'entries' => $entries,
            'compact' => $compact,
        ])->setPaper('a3', 'landscape');

        return $pdf->download("master-run-{$runId}" . ($compact ? "-compact" : "") . ".pdf");
    }

    public function conflicts(int $runId)
    {
        TimetableRun::findOrFail($runId);

        $teacherConflicts = DB::table('timetable_entries as e')
            ->select('e.day', DB::raw('e.period_index as period'), 'e.teacher_id', DB::raw('COUNT(*) as cnt'))
            ->where('e.timetable_run_id', $runId)
            ->whereNotNull('e.teacher_id')
            ->groupBy('e.day', 'period', 'e.teacher_id')
            ->having('cnt', '>', 1)
            ->orderBy('e.day')
            ->orderBy('period')
            ->get();

        $classConflicts = DB::table('timetable_entries as e')
            ->select('e.day', DB::raw('e.period_index as period'), 'e.class_id', DB::raw('COUNT(*) as cnt'))
            ->where('e.timetable_run_id', $runId)
            ->whereNotNull('e.class_id')
            ->groupBy('e.day', 'period', 'e.class_id')
            ->having('cnt', '>', 1)
            ->orderBy('e.day')
            ->orderBy('period')
            ->get();

        return view('timetables.conflicts', compact('runId', 'teacherConflicts', 'classConflicts'));
    }

    public function analyticsTeachers(int $runId)
    {
        $run = TimetableRun::findOrFail($runId);

        $loads = DB::table('timetable_entries')
            ->select('teacher_id', DB::raw('COUNT(*) as total'), DB::raw('COUNT(DISTINCT day) as days'))
            ->where('timetable_run_id', $runId)
            ->whereNotNull('teacher_id')
            ->groupBy('teacher_id')
            ->orderByDesc('total')
            ->get()
            ->map(function ($row) {
                $row->avg_per_day = ($row->days > 0) ? round($row->total / $row->days, 2) : 0;
                return $row;
            });

        $avg = $loads->count() ? round($loads->avg('total'), 2) : 0;
        $low = $avg * 0.85;
        $high = $avg * 1.15;

        $loads = $loads->map(function ($row) use ($low, $high) {
            if ($row->total < $low) $row->status = 'Underloaded';
            elseif ($row->total > $high) $row->status = 'Overloaded';
            else $row->status = 'Balanced';
            return $row;
        });

        $summary = [
            'total_teachers' => (int) $loads->count(),
            'total_lessons'  => (int) $loads->sum('total'),
            'avg_load'       => (float) $avg,
            'max_load'       => (int) ($loads->max('total') ?? 0),
            'min_load'       => (int) ($loads->min('total') ?? 0),
            'balanced'       => (int) $loads->where('status', 'Balanced')->count(),
            'overloaded'     => (int) $loads->where('status', 'Overloaded')->count(),
            'underloaded'    => (int) $loads->where('status', 'Underloaded')->count(),
        ];

        return view('timetables.analytics_teachers', compact('run', 'runId', 'loads', 'summary'));
    }

    public function analyticsClasses(int $runId)
    {
        $run = TimetableRun::findOrFail($runId);

        $loads = DB::table('timetable_entries')
            ->select('class_id', DB::raw('COUNT(*) as total'), DB::raw('COUNT(DISTINCT day) as days'))
            ->where('timetable_run_id', $runId)
            ->whereNotNull('class_id')
            ->groupBy('class_id')
            ->orderByDesc('total')
            ->get()
            ->map(function ($row) {
                $row->avg_per_day = ($row->days > 0) ? round($row->total / $row->days, 2) : 0;
                return $row;
            });

        $avg = $loads->count() ? round($loads->avg('total'), 2) : 0;
        $low = $avg * 0.85;
        $high = $avg * 1.15;

        $loads = $loads->map(function ($row) use ($low, $high) {
            if ($row->total < $low) $row->status = 'Underloaded';
            elseif ($row->total > $high) $row->status = 'Overloaded';
            else $row->status = 'Balanced';
            return $row;
        });

        $summary = [
            'total_classes' => (int) $loads->count(),
            'total_lessons' => (int) $loads->sum('total'),
            'avg_load'      => (float) $avg,
            'max_load'      => (int) ($loads->max('total') ?? 0),
            'min_load'      => (int) ($loads->min('total') ?? 0),
        ];

        return view('timetables.analytics_classes', compact('run', 'runId', 'loads', 'summary'));
    }

    public function analyticsSubjects(int $runId)
    {
        $run = TimetableRun::findOrFail($runId);

        $loads = DB::table('timetable_entries as e')
            ->select('e.subject_id', DB::raw("('Subject #' || e.subject_id) as subject_name"), DB::raw('COUNT(*) as total'), DB::raw('COUNT(DISTINCT e.day) as days'))
            ->where('e.timetable_run_id', $runId)
            ->whereNotNull('e.subject_id')
            ->groupBy('e.subject_id', 'subject_name')
            ->orderByDesc('total')
            ->get()
            ->map(function ($row) {
                $row->avg_per_day = ($row->days > 0) ? round($row->total / $row->days, 2) : 0;
                return $row;
            });

        $summary = [
            'total_subjects' => (int) $loads->count(),
            'total_lessons'  => (int) $loads->sum('total'),
            'max_load'       => (int) ($loads->max('total') ?? 0),
            'min_load'       => (int) ($loads->min('total') ?? 0),
        ];

        return view('timetables.analytics_subjects', compact('run', 'runId', 'loads', 'summary'));
    }

    public function quality(int $runId)
    {
        TimetableRun::findOrFail($runId);

        $teacherConflictCount = (int) DB::table('timetable_entries as e')
            ->select('e.day', 'e.period_index', 'e.teacher_id', DB::raw('COUNT(*) as cnt'))
            ->where('e.timetable_run_id', $runId)
            ->whereNotNull('e.teacher_id')
            ->groupBy('e.day', 'e.period_index', 'e.teacher_id')
            ->having('cnt', '>', 1)
            ->get()
            ->sum(fn($r) => max(0, ((int)$r->cnt) - 1));

        $classConflictCount = (int) DB::table('timetable_entries as e')
            ->select('e.day', 'e.period_index', 'e.class_id', DB::raw('COUNT(*) as cnt'))
            ->where('e.timetable_run_id', $runId)
            ->whereNotNull('e.class_id')
            ->groupBy('e.day', 'e.period_index', 'e.class_id')
            ->having('cnt', '>', 1)
            ->get()
            ->sum(fn($r) => max(0, ((int)$r->cnt) - 1));

        $allTeacherLoads = DB::table('timetable_entries')
            ->select('teacher_id', DB::raw('COUNT(*) as total'))
            ->where('timetable_run_id', $runId)
            ->whereNotNull('teacher_id')
            ->groupBy('teacher_id')
            ->get();

        $avg = $allTeacherLoads->count() ? (float) $allTeacherLoads->avg('total') : 0;
        $low = $avg * 0.85;
        $high = $avg * 1.15;

        $balanced = 0; $overloaded = 0; $underloaded = 0;
        foreach ($allTeacherLoads as $t) {
            if ($t->total < $low) $underloaded++;
            elseif ($t->total > $high) $overloaded++;
            else $balanced++;
        }

        $teacherCount = max(1, (int) $allTeacherLoads->count());
        $balanceScore = round(($balanced / $teacherCount) * 100, 2);

        $confPenalty = ($teacherConflictCount + $classConflictCount) * 2;
        $final = (int) max(0, min(100, round(($balanceScore * 0.7) + (max(0, 100 - $confPenalty) * 0.3))));

        return view('timetables.quality', compact(
            'runId',
            'teacherConflictCount',
            'classConflictCount',
            'balanced',
            'overloaded',
            'underloaded',
            'balanceScore',
            'final'
        ));
    }

    public function classPdf(int $runId, int $classId)
    {
        TimetableRun::findOrFail($runId);

        $entries = DB::table('timetable_entries')
            ->where('timetable_run_id', $runId)
            ->where('class_id', $classId)
            ->orderBy('day')
            ->orderBy('period_index')
            ->get();

        $pdf = Pdf::loadView('timetables.pdf.class_a4', [
            'runId' => $runId,
            'classId' => $classId,
            'entries' => $entries,
        ])->setPaper('a4', 'portrait');

        return $pdf->download("class-{$classId}-run-{$runId}.pdf");
    }

    public function teacherPdf(int $runId, int $teacherId)
    {
        TimetableRun::findOrFail($runId);

        $entries = DB::table('timetable_entries')
            ->where('timetable_run_id', $runId)
            ->where('teacher_id', $teacherId)
            ->orderBy('day')
            ->orderBy('period_index')
            ->get();

        $pdf = Pdf::loadView('timetables.pdf.teacher_a4', [
            'runId' => $runId,
            'teacherId' => $teacherId,
            'entries' => $entries,
        ])->setPaper('a4', 'portrait');

        return $pdf->download("teacher-{$teacherId}-run-{$runId}.pdf");
    }
}
