<?php

namespace Innovara\Timetables\Engine\Rules;

use Innovara\Timetables\Models\TimetableEntry;

interface RuleInterface
{
    /**
     * @param TimetableEntry   $candidate   The entry we want to place
     * @param TimetableEntry[] $current     Entries already placed
     * @param array            $context     Extra info (period templates, settings, etc.)
     */
    public function passes(TimetableEntry $candidate, array $current, array $context = []): bool;

    public function message(): string;
}
