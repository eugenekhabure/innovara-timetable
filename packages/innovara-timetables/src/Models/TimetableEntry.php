<?php

namespace Innovara\Timetables\Models;

class TimetableEntry
{
    public int $classId;
    public int $teacherId;
    public int $subjectId;
    public string $day;        // e.g. Monday
    public int $periodIndex;   // e.g. 1..12

    public function __construct(
        int $classId,
        int $teacherId,
        int $subjectId,
        string $day,
        int $periodIndex
    ) {
        $this->classId = $classId;
        $this->teacherId = $teacherId;
        $this->subjectId = $subjectId;
        $this->day = $day;
        $this->periodIndex = $periodIndex;
    }

    public function slotKey(): string
    {
        return $this->day . ':' . $this->periodIndex;
    }
}
