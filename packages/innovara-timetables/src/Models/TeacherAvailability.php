<?php

namespace Innovara\Timetables\Models;

class TeacherAvailability
{
    /**
     * @var array<int, array<string, array<int>>>  teacherId => day => [periodIndex...]
     * Example: [10 => ['Mon' => [1,2,3,5,6], 'Tue' => [1,2,3]]]
     */
    protected array $availableSlots = [];

    /**
     * Default: teacher is available for ALL lesson periods (not breaks/lunch).
     */
    public static function defaultAllLessonSlots(int $teacherId): self
    {
        $self = new self();

        $days = ['Mon','Tue','Wed','Thu','Fri'];

        // Use your KenyanPeriodTemplate to build what lesson period indexes exist
        $periods = KenyanPeriodTemplate::daily();

        $lessonIndexes = [];
        foreach ($periods as $p) {
            // assumes Period has ->type and ->index
            if (($p->type ?? null) === 'lesson') {
                $lessonIndexes[] = $p->index;
            }
        }

        foreach ($days as $day) {
            $self->availableSlots[$teacherId][$day] = $lessonIndexes;
        }

        return $self;
    }

    public function setAvailable(int $teacherId, string $day, array $periodIndexes): self
    {
        $this->availableSlots[$teacherId][$day] = array_values(array_unique($periodIndexes));
        sort($this->availableSlots[$teacherId][$day]);
        return $this;
    }

    public function isAvailable(int $teacherId, string $day, int $periodIndex): bool
    {
        if (!isset($this->availableSlots[$teacherId])) return false;
        if (!isset($this->availableSlots[$teacherId][$day])) return false;

        return in_array($periodIndex, $this->availableSlots[$teacherId][$day], true);
    }
}
