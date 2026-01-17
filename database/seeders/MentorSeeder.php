<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MentorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Assign mentors to school (user_id = 2)
        DB::table('assign_mentor')->insert([
            [
                'user_id_mentor' => 8,
                'user_id_sekolah' => 2,
                'assign_time_begin' => now(),
                'assign_time_finished' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}