<?php

namespace Innovara\Timetables\Models;

final class KenyanPeriodTemplate
{
    public static function daily(): array
    {
        return [
            new Period(1,  'Lesson 1', 'lesson', '08:00', '08:40'),
            new Period(2,  'Lesson 2', 'lesson', '08:40', '09:20'),
            new Period(3,  'Lesson 3', 'lesson', '09:20', '10:00'),

            new Period(4,  'Break 1',  'break',  '10:00', '10:20'),

            new Period(5,  'Lesson 4', 'lesson', '10:20', '11:00'),
            new Period(6,  'Lesson 5', 'lesson', '11:00', '11:40'),

            new Period(7,  'Break 2',  'break',  '11:40', '12:00'),

            new Period(8,  'Lesson 6', 'lesson', '12:00', '12:40'),
            new Period(9,  'Lesson 7', 'lesson', '12:40', '13:20'),

            new Period(10, 'Lunch',    'lunch',  '13:20', '14:00'),

            new Period(11, 'Lesson 8', 'lesson', '14:00', '14:40'),
            new Period(12, 'Lesson 9', 'lesson', '14:40', '15:20'),
        ];
    }
}
