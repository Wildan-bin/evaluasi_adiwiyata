<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Wildan Mukorrobin',
        //     'email' => 'WildanTest@gmail.com',
        //     'password' => bcrypt('admin123'),
        // ]);

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
