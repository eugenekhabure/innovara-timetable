<?php

namespace Innovara\Timetables\Engine\Rules;

use Innovara\Timetables\Models\TimetableEntry;

class NoClassClashRule implements RuleInterface
{
    public function passes(TimetableEntry $candidate, array $current, array $context = []): bool
    {
        foreach ($current as $e) {
            if ($e->classId === $candidate->classId && $e->slotKey() === $candidate->slotKey()) {
                return false;
            }
        }
        return true;
    }

    public function message(): string
    {
        return 'Class clash: class already has a lesson in this slot.';
    }
}
