<?php

namespace Innovara\Timetables\Engine\Rules;

use Innovara\Timetables\Models\TimetableEntry;

class NoTeacherClashRule implements RuleInterface
{
    public function passes(TimetableEntry $candidate, array $current, array $context = []): bool
    {
        foreach ($current as $e) {
            if ($e->teacherId === $candidate->teacherId && $e->slotKey() === $candidate->slotKey()) {
                return false;
            }
        }
        return true;
    }

    public function message(): string
    {
        return 'Teacher clash: same teacher already assigned in this slot.';
    }
}
