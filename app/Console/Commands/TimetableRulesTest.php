<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Innovara\Timetables\Engine\TimetableGenerator;
use Innovara\Timetables\Engine\Rules\NoClassClashRule;
use Innovara\Timetables\Engine\Rules\NoLessonOnBreakRule;
use Innovara\Timetables\Engine\Rules\NoTeacherClashRule;
use Innovara\Timetables\Models\Day;
use Innovara\Timetables\Models\KenyanPeriodTemplate;
use Innovara\Timetables\Models\TimetableEntry;

class TimetableRulesTest extends Command
{
    protected $signature = 'timetable:rules-test';
    protected $description = 'Quick test for Innovara timetable conflict rules';

    public function handle(): int
    {
        // Build periodsByIndex for NoLessonOnBreakRule context
        $periods = KenyanPeriodTemplate::daily();
        $periodsByIndex = [];
        foreach ($periods as $p) {
            $periodsByIndex[$p->index] = $p;
        }

        $gen = new TimetableGenerator([
            'periods' => $periodsByIndex
        ]);

        // Add rules
        $gen->addRule(new NoLessonOnBreakRule());
        $gen->addRule(new NoTeacherClashRule());
        $gen->addRule(new NoClassClashRule());

        $this->info("Testing rule engine...\n");

        // 1) Valid placement (Lesson 1)
        $fail = null;
        $ok = $gen->tryPlace(new TimetableEntry(
            classId: 1,
            teacherId: 10,
            subjectId: 101,
            day: Day::MONDAY,
            periodIndex: 1
        ), $fail);

        $this->line("1) Place class=1 teacher=10 Mon P1 => " . ($ok ? "OK ✅" : "BLOCKED ❌ ($fail)"));

        // 2) Teacher clash (same teacher same slot different class)
        $fail = null;
        $ok = $gen->tryPlace(new TimetableEntry(
            classId: 2,
            teacherId: 10,
            subjectId: 102,
            day: Day::MONDAY,
            periodIndex: 1
        ), $fail);

        $this->line("2) Teacher clash test (teacher=10 Mon P1 again) => " . ($ok ? "OK (unexpected) ❌" : "BLOCKED ✅ ($fail)"));

        // 3) Class clash (same class same slot different teacher)
        $fail = null;
        $ok = $gen->tryPlace(new TimetableEntry(
            classId: 1,
            teacherId: 11,
            subjectId: 103,
            day: Day::MONDAY,
            periodIndex: 1
        ), $fail);

        $this->line("3) Class clash test (class=1 Mon P1 again) => " . ($ok ? "OK (unexpected) ❌" : "BLOCKED ✅ ($fail)"));

        // 4) Break slot test (Break 1 is index 4 in our template)
        $fail = null;
        $ok = $gen->tryPlace(new TimetableEntry(
            classId: 3,
            teacherId: 12,
            subjectId: 104,
            day: Day::MONDAY,
            periodIndex: 4
        ), $fail);

        $this->line("4) Break/Lunch test (Mon P4 is break) => " . ($ok ? "OK (unexpected) ❌" : "BLOCKED ✅ ($fail)"));

        $this->info("\nDone.");
        return self::SUCCESS;
    }
}
