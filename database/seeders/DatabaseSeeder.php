<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Truncate users table safely (handles FK constraints)
        Schema::disableForeignKeyConstraints();
        DB::table('users')->truncate();
        Schema::enableForeignKeyConstraints();

        // Seed base users (admin, user, mentor) with password 12345678
        $this->call(UserSeeder::class);

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
