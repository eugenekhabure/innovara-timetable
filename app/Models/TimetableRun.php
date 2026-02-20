<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimetableRun extends Model
{
    protected $table = 'timetable_runs';

    protected $fillable = [
        'name',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];
}
