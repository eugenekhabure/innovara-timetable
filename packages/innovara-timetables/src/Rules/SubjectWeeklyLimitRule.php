<?php

namespace Innovara\Timetables\Rules;

use Innovara\Timetables\Models\SubjectWeeklyLimit;
use Innovara\Timetables\Models\TimetableEntry;

class SubjectWeeklyLimitRule
{
    public function __construct(protected SubjectWeeklyLimit $limits) {}

    public function canPlace(TimetableEntry $entry): array
    {
        $ok = $this->limits->canAssign($entry->classId, $entry->subjectId);

        return $ok
            ? [true, 'OK']
            : [false, 'Subject weekly limit exceeded'];
    }

    public function onPlaced(TimetableEntry $entry): void
    {
        $this->limits->increment($entry->classId, $entry->subjectId);
    }
}
