<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimetableEntry extends Model
{
    protected $fillable = [
        'timetable_run_id',
        'day',
        'period_index',
        'class_id',
        'teacher_id',
        'subject_id',
    ];

    public function run(): BelongsTo
    {
        return $this->belongsTo(TimetableRun::class, 'timetable_run_id');
    }
}
