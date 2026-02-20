<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TimetableConflictDetector
{
    public function detect(int $runId): array
    {
        $dayCol    = $this->dayColumn();
        $periodCol = $this->periodColumn();

        // Teacher double-booking: same teacher, same day, same period
        $teacherConflicts = DB::table('timetable_entries as e')
            ->select("e.$dayCol as day", "e.$periodCol as period", 'e.teacher_id', DB::raw('COUNT(*) as total'))
            ->where('e.timetable_run_id', $runId)
            ->whereNotNull('e.teacher_id')
            ->groupBy("e.$dayCol", "e.$periodCol", 'e.teacher_id')
            ->havingRaw('COUNT(*) > 1')
            ->orderBy("e.$dayCol")
            ->orderBy("e.$periodCol")
            ->get();

        // Class double-booking: same class, same day, same period
        $classConflicts = DB::table('timetable_entries as e')
            ->select("e.$dayCol as day", "e.$periodCol as period", 'e.class_id', DB::raw('COUNT(*) as total'))
            ->where('e.timetable_run_id', $runId)
            ->whereNotNull('e.class_id')
            ->groupBy("e.$dayCol", "e.$periodCol", 'e.class_id')
            ->havingRaw('COUNT(*) > 1')
            ->orderBy("e.$dayCol")
            ->orderBy("e.$periodCol")
            ->get();

        // Build quick lookup sets to highlight cells in grid
        $teacherCellKeys = [];
        foreach ($teacherConflicts as $c) {
            $teacherCellKeys[$c->day.'|'.$c->period.'|T|'.$c->teacher_id] = true;
        }

        $classCellKeys = [];
        foreach ($classConflicts as $c) {
            $classCellKeys[$c->day.'|'.$c->period.'|C|'.$c->class_id] = true;
        }

        return [
            'teacher_conflicts' => $teacherConflicts,
            'class_conflicts'   => $classConflicts,
            'teacherCellKeys'   => $teacherCellKeys,
            'classCellKeys'     => $classCellKeys,
            'dayCol'            => $dayCol,
            'periodCol'         => $periodCol,
        ];
    }

    private function dayColumn(): string
    {
        // If your table ever changes, this stays safe
        if (Schema::hasColumn('timetable_entries', 'day')) return 'day';
        if (Schema::hasColumn('timetable_entries', 'day_index')) return 'day_index';
        return 'day';
    }

    private function periodColumn(): string
    {
        if (Schema::hasColumn('timetable_entries', 'period_index')) return 'period_index';
        if (Schema::hasColumn('timetable_entries', 'period')) return 'period';
        return 'period_index';
    }
}
