<?php

namespace Innovara\Timetables\Rules;

use Innovara\Timetables\Models\TeacherLoadLimit;
use Innovara\Timetables\Models\TimetableEntry;

class TeacherLoadLimitRule
{
    public function __construct(protected TeacherLoadLimit $limit) {}

    public function canPlace(TimetableEntry $entry): array
    {
        $ok = $this->limit->canAssign($entry->teacherId);

        return $ok
            ? [true, 'OK']
            : [false, 'Teacher weekly load limit reached'];
    }

    public function onPlaced(TimetableEntry $entry): void
    {
        $this->limit->increment($entry->teacherId);
    }
}
