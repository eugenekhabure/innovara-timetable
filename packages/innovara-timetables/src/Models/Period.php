<?php

namespace Innovara\Timetables\Models;

final class Period
{
    public function __construct(
        public int $index,
        public string $name,
        public string $type,  // lesson | break | lunch
        public string $start, // HH:MM
        public string $end    // HH:MM
    ) {}

    public function isBreak(): bool
    {
        return $this->type === 'break';
    }

    public function isLunch(): bool
    {
        return $this->type === 'lunch';
    }

    public function isTeachingPeriod(): bool
    {
        return $this->type === 'lesson';
    }
}
