<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timetable_runs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // e.g. "Term 1 2026"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetable_runs');
    }
};

