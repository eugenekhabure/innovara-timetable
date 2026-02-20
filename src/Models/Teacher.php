<?php

namespace Innovara\Timetables\Models;

class Teacher
{
    public int $id;
    public string $name;
    public array $subjects = [];
    public array $availability = [];
}
