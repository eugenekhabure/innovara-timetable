<?php

namespace Innovara\Timetables\Models;

class Day
{
    public const MONDAY    = 'Monday';
    public const TUESDAY   = 'Tuesday';
    public const WEDNESDAY = 'Wednesday';
    public const THURSDAY  = 'Thursday';
    public const FRIDAY    = 'Friday';

    public static function all(): array
    {
        return [
            self::MONDAY,
            self::TUESDAY,
            self::WEDNESDAY,
            self::THURSDAY,
            self::FRIDAY,
        ];
    }
}
