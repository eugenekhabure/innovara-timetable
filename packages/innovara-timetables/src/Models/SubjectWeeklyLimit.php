<?php

namespace Innovara\Timetables\Models;

class SubjectWeeklyLimit
{
    protected array $limits = [];
    protected array $assigned = [];

    public function setLimit(int $classId, int $subjectId, int $maxPerWeek): self
    {
        $this->limits[$classId][$subjectId] = $maxPerWeek;
        return $this;
    }

    public function canAssign(int $classId, int $subjectId): bool
    {
        $limit = $this->limits[$classId][$subjectId] ?? PHP_INT_MAX;
        $used  = $this->assigned[$classId][$subjectId] ?? 0;

        return $used < $limit;
    }

    public function increment(int $classId, int $subjectId): void
    {
        $this->assigned[$classId][$subjectId] =
            ($this->assigned[$classId][$subjectId] ?? 0) + 1;
    }

    public function getAssigned(int $classId, int $subjectId): int
    {
        return $this->assigned[$classId][$subjectId] ?? 0;
    }
}
