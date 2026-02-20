<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\TimetableRun;
use App\Models\TimetableEntry;
use Innovara\Timetables\Models\TimetableEntry as EngineEntry;
use Innovara\Timetables\Models\Day;

class TimetableGenerateDemo extends Command
{
    protected $signature = 'timetable:generate-demo {--name=Demo Run}';
    protected $description = 'Generate a demo timetable and save it to the database';

    public function handle(): int
    {
        $this->info('Generating timetable…');

        DB::transaction(function () {

            // 1) Create timetable run
            $run = TimetableRun::create([
                'name' => $this->option('name'),
            ]);

            // 2) Demo lesson placements (same as before)
            $placements = [
                new EngineEntry(1, 10, 101, Day::MONDAY, 1),
                new EngineEntry(1, 11, 102, Day::MONDAY, 2),
                new EngineEntry(2, 10, 103, Day::MONDAY, 2),

                new EngineEntry(1, 10, 101, Day::TUESDAY, 1),
                new EngineEntry(1, 11, 102, Day::TUESDAY, 2),
                new EngineEntry(2, 10, 103, Day::TUESDAY, 2),

                new EngineEntry(1, 10, 101, Day::WEDNESDAY, 1),
                new EngineEntry(1, 10, 101, Day::THURSDAY, 1),
                new EngineEntry(1, 11, 102, Day::FRIDAY, 1),
                new EngineEntry(2, 10, 103, Day::FRIDAY, 1),
            ];

            // 3) Save entries (KEY FIX: day is already a string)
            foreach ($placements as $p) {
                TimetableEntry::create([
                    'timetable_run_id' => $run->id,
                    'day'              => $p->day,         // ✅ FIXED (NO ->value)
                    'period_index'     => $p->periodIndex,
                    'class_id'         => $p->classId,
                    'teacher_id'       => $p->teacherId,
                    'subject_id'       => $p->subjectId,
                ]);
            }

            $this->info('Timetable saved to DB ✔');
        });

        return self::SUCCESS;
    }
}
