<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin account
        \App\Models\User::create([
            'name' => 'Admin Greenedu',
            'email' => 'admin@greenedu.com',
            'password' => bcrypt('12345678'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // User account
        \App\Models\User::create([
            'name' => 'User Test',
            'email' => 'user@greenedu.com',
            'password' => bcrypt('12345678'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        // Mentor account (optional)
        \App\Models\User::create([
            'name' => 'Mentor Greenedu',
            'email' => 'mentor@greenedu.com',
            'password' => bcrypt('12345678'),
            'role' => 'mentor',
            'email_verified_at' => now(),
        ]);
    }
}
