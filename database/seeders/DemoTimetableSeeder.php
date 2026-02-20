<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\TimetableRun;

class DemoTimetableSeeder extends Seeder
{
    public function run()
    {
        // Create Run
        $run = TimetableRun::create([]);

        // Insert Class
        DB::table('school_classes')->insert([
            'id' => 1,
            'name' => 'Class 1',
            'level_type' => 'PRIMARY',
            'level_number' => 1,
            'stream' => 'A',
            'sort_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert Teacher
        DB::table('teachers')->insert([
            'id' => 10,
            'name' => 'Teacher 10',
            'code' => 'T10',
            'email' => null,
            'phone' => null,
            'active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert Timetable Entries
        DB::table('timetable_entries')->insert([
            [
                'run_id' => $run->id,
                'class_id' => 1,
                'teacher_id' => 10,
                'day' => 'Monday',
                'period' => 1,
                'subject' => 'Math',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'run_id' => $run->id,
                'class_id' => 1,
                'teacher_id' => 10,
                'day' => 'Tuesday',
                'period' => 2,
                'subject' => 'English',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
