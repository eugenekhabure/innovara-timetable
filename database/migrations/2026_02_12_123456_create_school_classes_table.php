<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_classes', function (Blueprint $table) {
            $table->id();

            // Display name e.g. "PP1 A", "Grade 4 Blue", "Form 2 East"
            $table->string('name');

            // One of: PP, PRIMARY, JSS, SECONDARY (flexible enough for Kenya)
            $table->string('level_type', 20)->index();

            // Level number:
            // PP1=1, PP2=2
            // Grade 1..9 => 1..9
            // Form 1..4 => 1..4
            $table->unsignedTinyInteger('level_number')->index();

            // Stream label like A/B/C, Blue/Red, East/West etc (optional)
            $table->string('stream', 50)->nullable()->index();

            // Optional ordering override (if you want manual sort later)
            $table->unsignedSmallInteger('sort_order')->nullable()->index();

            $table->timestamps();

            $table->unique(['level_type', 'level_number', 'stream'], 'uniq_level_stream');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_classes');
    }
};
