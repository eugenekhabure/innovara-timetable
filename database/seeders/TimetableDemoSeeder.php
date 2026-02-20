<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimetableDemoSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Reset demo tables
        DB::table('timetable_entries')->delete();
        DB::table('timetable_runs')->delete();
        DB::table('subjects')->delete();
        DB::table('teachers')->delete();
        DB::table('school_classes')->delete();

        // Subjects (Kenyan typical set)
        $subjects = [
            ['name' => 'Mathematics', 'code' => 'MATH'],
            ['name' => 'English',     'code' => 'ENG'],
            ['name' => 'Kiswahili',   'code' => 'KIS'],
            ['name' => 'Science',     'code' => 'SCI'],
            ['name' => 'Social Studies', 'code' => 'SST'],
            ['name' => 'CRE',         'code' => 'CRE'],
            ['name' => 'Computer',    'code' => 'ICT'],
        ];

        $subjectIds = [];
        foreach ($subjects as $s) {
            $id = DB::table('subjects')->insertGetId([
                'name' => $s['name'],
                'code' => $s['code'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $subjectIds[] = $id;
        }

        // Teachers (names for initials)
        $teachers = [
            ['name' => 'Eugene Khabure', 'code' => 'EK'],
            ['name' => 'John Mwangi',    'code' => 'JM'],
            ['name' => 'Alice Kimani',   'code' => 'AK'],
            ['name' => 'Peter Otieno',   'code' => 'PO'],
        ];
        $teacherIds = [];
        foreach ($teachers as $t) {
            $teacherIds[] = DB::table('teachers')->insertGetId([
                'name' => $t['name'],
                'code' => $t['code'],
                'email' => null,
                'phone' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Classes: simulate streams (A/B/C)
        // (Our master bands will group by ID ranges, but this gives better volume)
        $classIds = [];
        $streams = ['A','B','C'];
        $grade = 1;

        foreach ($streams as $stream) {
            $classIds[] = DB::table('school_classes')->insertGetId([
                'name' => "Grade {$grade} {$stream}",
                'level_type' => 'PRIMARY',
                'level_number' => $grade,
                'stream' => $stream,
                'sort_order' => ($grade * 10),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Create run
        $runId = DB::table('timetable_runs')->insertGetId([
            'name' => 'Demo Run (Kenya)',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Days and periods
        $days = ['Monday','Tuesday','Wednesday','Thursday','Friday'];
        $periods = range(1, 8); // keep small for demo speed

        // Fill entries randomly but consistently
        $i = 0;
        foreach ($classIds as $classId) {
            foreach ($days as $day) {
                foreach ($periods as $p) {

                    // skip breaks/lunch “holes” for visual realism (example)
                    if (in_array($p, [3, 6], true)) {
                        continue;
                    }

                    $subjectId = $subjectIds[$i % count($subjectIds)];
                    $teacherId = $teacherIds[$i % count($teacherIds)];
                    $i++;

                    DB::table('timetable_entries')->insert([
                        'timetable_run_id' => $runId,
                        'class_id' => $classId,
                        'teacher_id' => $teacherId,
                        'day' => $day,
                        'period_index' => $p,
                        'subject_id' => $subjectId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }
}
