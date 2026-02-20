<?php

namespace Innovara\Timetables\Engine\Rules;

use Innovara\Timetables\Models\TimetableEntry;
use Innovara\Timetables\Models\Period;

class NoLessonOnBreakRule implements RuleInterface
{
    public function passes(TimetableEntry $candidate, array $current, array $context = []): bool
    {
        /** @var array<int,Period> $periodsByIndex */
        $periodsByIndex = $context['periods'] ?? [];

        if (!isset($periodsByIndex[$candidate->periodIndex])) {
            // If no period definition exists, be safe: reject
            return false;
        }

        $period = $periodsByIndex[$candidate->periodIndex];
        return $period->isTeachingPeriod();
    }

    public function message(): string
    {
        return 'Invalid slot: cannot place a lesson during break/lunch.';
    }
}
