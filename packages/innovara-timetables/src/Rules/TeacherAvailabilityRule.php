<?php

namespace Innovara\Timetables\Rules;

use Innovara\Timetables\Models\TeacherAvailability;
use Innovara\Timetables\Models\TimetableEntry;

class TeacherAvailabilityRule
{
    public function __construct(protected TeacherAvailability $availability) {}

    public function canPlace(TimetableEntry $entry): array
    {
        $ok = $this->availability->isAvailable(
            $entry->teacherId,
            $entry->day,
            $entry->periodIndex
        );

        return $ok
            ? [true, 'OK']
            : [false, 'Teacher not available for this slot'];
    }
}
