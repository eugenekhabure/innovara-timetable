<?php

namespace Innovara\Timetables\Timetable;

use Innovara\Timetables\Models\Day;
use Innovara\Timetables\Models\KenyanPeriodTemplate;
use Innovara\Timetables\Models\TimetableEntry;

class Generator
{
    /** @var TimetableEntry[] */
    private array $placed = [];

    /** @var array<string, bool> */
    private array $teacherSlot = []; // "teacherId|day|periodIndex" => true

    /** @var array<string, bool> */
    private array $classSlot = [];   // "classId|day|periodIndex" => true

    /** @var array<int, bool> */
    private array $breakOrLunch = []; // periodIndex => true

    /** @var array<int, array<int, int>> */
    private array $subjectWeeklyCount = []; // classId => [subjectId => count]

    /** @var array<int, array<int, int>> */
    private array $subjectWeeklyLimit = []; // classId => [subjectId => limit]

    /**
     * $subjectWeeklyLimit example:
     * [
     *   1 => [101 => 5, 102 => 3], // classId 1 subject 101 limit 5 per week
     * ]
     */
    public function __construct(array $subjectWeeklyLimit = [])
    {
        $this->subjectWeeklyLimit = $subjectWeeklyLimit;

        // mark break/lunch indices from Kenya template
        foreach (KenyanPeriodTemplate::daily() as $p) {
            if (in_array($p->type, ['break', 'lunch'], true)) {
                $this->breakOrLunch[$p->index] = true;
            }
        }
    }

    /**
     * @param array<int, array{classId:int, teacherId:int, subjectId:int, lessonsPerWeek:int}> $requirements
     * @return TimetableEntry[]
     */
    public function generate(array $requirements): array
    {
        $tasks = $this->expandTasks($requirements);

        // simple ordering: hardest first (more weekly lessons first)
        usort($tasks, fn($a, $b) => $b['weight'] <=> $a['weight']);

        $ok = $this->backtrack($tasks, 0);

        if (!$ok) {
            throw new \RuntimeException("Failed to generate timetable with given constraints.");
        }

        return $this->placed;
    }

    /**
     * @param array<int, array{classId:int, teacherId:int, subjectId:int, lessonsPerWeek:int}> $requirements
     * @return array<int, array{classId:int, teacherId:int, subjectId:int, weight:int}>
     */
    private function expandTasks(array $requirements): array
    {
        $tasks = [];

        foreach ($requirements as $r) {
            $count = (int) $r['lessonsPerWeek'];
            for ($i = 0; $i < $count; $i++) {
                $tasks[] = [
                    'classId' => (int) $r['classId'],
                    'teacherId' => (int) $r['teacherId'],
                    'subjectId' => (int) $r['subjectId'],
                    'weight' => $count, // used for ordering
                ];
            }
        }

        return $tasks;
    }

    /**
     * @param array<int, array{classId:int, teacherId:int, subjectId:int, weight:int}> $tasks
     */
    private function backtrack(array $tasks, int $i): bool
    {
        if ($i >= count($tasks)) {
            return true;
        }

        $task = $tasks[$i];

        foreach ($this->candidateSlots($task['classId']) as [$day, $periodIndex]) {
            $entry = new TimetableEntry(
                classId: $task['classId'],
                teacherId: $task['teacherId'],
                subjectId: $task['subjectId'],
                day: $day,
                periodIndex: $periodIndex
            );

            $fail = null;
            if (!$this->tryPlace($entry, $fail)) {
                continue;
            }

            if ($this->backtrack($tasks, $i + 1)) {
                return true;
            }

            $this->unplace($entry);
        }

        return false;
    }

    /**
     * @return array<int, array{0:int,1:int}> list of [dayConst, periodIndex]
     */
    private function candidateSlots(int $classId): array
    {
        // Mon-Fri, Periods 1..12, skip break/lunch indices
        $days = [Day::MONDAY, Day::TUESDAY, Day::WEDNESDAY, Day::THURSDAY, Day::FRIDAY];

        $slots = [];
        for ($p = 1; $p <= 12; $p++) {
            if (isset($this->breakOrLunch[$p])) continue;
            foreach ($days as $d) {
                $slots[] = [$d, $p];
            }
        }

        // light heuristic: prefer earlier periods
        return $slots;
    }

    public function tryPlace(TimetableEntry $entry, ?string &$fail = null): bool
    {
        // 1) Break/lunch rule
        if (isset($this->breakOrLunch[$entry->periodIndex])) {
            $fail = "Invalid slot: cannot place during break/lunch.";
            return false;
        }

        // 2) Teacher clash rule
        $tKey = $entry->teacherId . '|' . $entry->day . '|' . $entry->periodIndex;
        if (isset($this->teacherSlot[$tKey])) {
            $fail = "Teacher clash: same teacher already assigned in this slot.";
            return false;
        }

        // 3) Class clash rule
        $cKey = $entry->classId . '|' . $entry->day . '|' . $entry->periodIndex;
        if (isset($this->classSlot[$cKey])) {
            $fail = "Class clash: class already has a lesson in this slot.";
            return false;
        }

        // 4) Weekly subject limit rule (optional)
        $limit = $this->subjectWeeklyLimit[$entry->classId][$entry->subjectId] ?? null;
        if ($limit !== null) {
            $current = $this->subjectWeeklyCount[$entry->classId][$entry->subjectId] ?? 0;
            if ($current + 1 > $limit) {
                $fail = "Weekly limit exceeded for this subject in this class.";
                return false;
            }
        }

        $this->place($entry);
        return true;
    }

    private function place(TimetableEntry $entry): void
    {
        $this->placed[] = $entry;

        $tKey = $entry->teacherId . '|' . $entry->day . '|' . $entry->periodIndex;
        $cKey = $entry->classId . '|' . $entry->day . '|' . $entry->periodIndex;

        $this->teacherSlot[$tKey] = true;
        $this->classSlot[$cKey] = true;

        $this->subjectWeeklyCount[$entry->classId][$entry->subjectId] =
            ($this->subjectWeeklyCount[$entry->classId][$entry->subjectId] ?? 0) + 1;
    }

    private function unplace(TimetableEntry $entry): void
    {
        // remove last matching entry (stack discipline works because backtrack places/unplaces in order)
        $last = array_pop($this->placed);

        if (!$last) return;

        $tKey = $last->teacherId . '|' . $last->day . '|' . $last->periodIndex;
        $cKey = $last->classId . '|' . $last->day . '|' . $last->periodIndex;

        unset($this->teacherSlot[$tKey], $this->classSlot[$cKey]);

        $this->subjectWeeklyCount[$last->classId][$last->subjectId] =
            max(0, ($this->subjectWeeklyCount[$last->classId][$last->subjectId] ?? 1) - 1);
    }
}
