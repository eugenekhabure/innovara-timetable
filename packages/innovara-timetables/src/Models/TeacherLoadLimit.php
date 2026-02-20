<?php

namespace Innovara\Timetables\Models;

class TeacherLoadLimit
{
    /**
     * Max lessons per week per teacherId
     * @var array<int,int>
     */
    protected array $maxPerWeek = [];

    /**
     * Current assigned lesson count per week per teacherId
     * @var array<int,int>
     */
    protected array $assignedPerWeek = [];

    public function setMaxPerWeek(int $teacherId, int $maxLessons): self
    {
        $this->maxPerWeek[$teacherId] = $maxLessons;
        return $this;
    }

    public function canAssign(int $teacherId): bool
    {
        $max = $this->maxPerWeek[$teacherId] ?? PHP_INT_MAX;
        $assigned = $this->assignedPerWeek[$teacherId] ?? 0;

        return $assigned < $max;
    }

    public function increment(int $teacherId): void
    {
        $this->assignedPerWeek[$teacherId] = ($this->assignedPerWeek[$teacherId] ?? 0) + 1;
    }

    public function getAssigned(int $teacherId): int
    {
        return $this->assignedPerWeek[$teacherId] ?? 0;
    }
}
