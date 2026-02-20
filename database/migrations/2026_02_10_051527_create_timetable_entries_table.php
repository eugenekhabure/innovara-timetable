<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timetable_entries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('timetable_run_id')
                  ->constrained('timetable_runs')
                  ->cascadeOnDelete();

            $table->string('day'); // Mon, Tue, Wed...
            $table->unsignedInteger('period_index');

            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('subject_id');

            $table->timestamps();

            // optional but very useful: prevent duplicate slot per class
            $table->unique(['timetable_run_id', 'day', 'period_index', 'class_id'], 'tt_unique_class_slot');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetable_entries');
    }
};
