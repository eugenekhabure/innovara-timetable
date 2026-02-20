<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class TimetableConflictDetector
{
    /**
     * Returns conflicts for a given timetable_run_id.
     * Output:
     *  - teacher_conflicts: grouped rows where same teacher appears >1 for same day+period
     *  - class_conflicts: grouped rows where same class appears >1 for same day+period
     */
    public static function detect(int $runId): array
    {
        // Teacher conflicts: same teacher, same day, same period_index, multiple entries
        $teacherConflicts = DB::table('timetable_entries as e')
            ->select(
                'e.teacher_id',
                'e.day',
                'e.period_index',
                DB::raw('COUNT(*) as total'),
                DB::raw('GROUP_CONCAT(e.class_id) as class_ids'),
                DB::raw('GROUP_CONCAT(e.subject_id) as subject_ids')
            )
            ->where('e.timetable_run_id', $runId)
            ->groupBy('e.teacher_id', 'e.day', 'e.period_index')
            ->having('total', '>', 1)
            ->orderBy('e.day')
            ->orderBy('e.period_index')
            ->get();

        // Class conflicts: same class, same day, same period_index, multiple entries
        $classConflicts = DB::table('timetable_entries as e')
            ->select(
                'e.class_id',
                'e.day',
                'e.period_index',
                DB::raw('COUNT(*) as total'),
                DB::raw('GROUP_CONCAT(e.teacher_id) as teacher_ids'),
                DB::raw('GROUP_CONCAT(e.subject_id) as subject_ids')
            )
            ->where('e.timetable_run_id', $runId)
            ->groupBy('e.class_id', 'e.day', 'e.period_index')
            ->having('total', '>', 1)
            ->orderBy('e.day')
            ->orderBy('e.period_index')
            ->get();

        // Optional: load friendly names (if tables exist)
        $teacherMap = DB::table('teachers')->select('id', 'name', 'code')->get()->keyBy('id');
        $classMap   = DB::table('school_classes')->select('id', 'name', 'stream')->get()->keyBy('id');
        $subjectMap = DB::table('subjects')->select('id', 'name', 'code')->get()->keyBy('id');

        return [
            'teacher_conflicts' => $teacherConflicts,
            'class_conflicts'   => $classConflicts,
            'maps' => [
                'teachers' => $teacherMap,
                'classes'  => $classMap,
                'subjects' => $subjectMap,
            ]
        ];
    }
}
