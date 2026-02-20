<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            // Make them nullable so SQLite can add them safely to an existing table
            if (!Schema::hasColumn('teachers', 'name')) {
                $table->string('name')->nullable()->after('id');
            }
            if (!Schema::hasColumn('teachers', 'code')) {
                $table->string('code')->nullable()->after('name');
            }
            if (!Schema::hasColumn('teachers', 'email')) {
                $table->string('email')->nullable()->after('code');
            }
            if (!Schema::hasColumn('teachers', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('teachers', 'active')) {
                $table->boolean('active')->default(1)->after('phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            if (Schema::hasColumn('teachers', 'active')) $table->dropColumn('active');
            if (Schema::hasColumn('teachers', 'phone')) $table->dropColumn('phone');
            if (Schema::hasColumn('teachers', 'email')) $table->dropColumn('email');
            if (Schema::hasColumn('teachers', 'code')) $table->dropColumn('code');
            if (Schema::hasColumn('teachers', 'name')) $table->dropColumn('name');
        });
    }
};
