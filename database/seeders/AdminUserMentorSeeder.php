<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserMentorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin Users
        $admins = [
            [
                'name' => 'Admin Adiwiyata',
                'email' => 'admin@adiwiyata.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin'
            ],
            [
                'name' => 'Admin Sekolah',
                'email' => 'admin@sekolah.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin'
            ]
        ];

        foreach ($admins as $admin) {
            User::firstOrCreate(
                ['email' => $admin['email']],
                $admin
            );
        }

        // Regular Users
        $users = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@sekolah.com',
                'password' => Hash::make('user123'),
                'role' => 'user'
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti@sekolah.com',
                'password' => Hash::make('user123'),
                'role' => 'user'
            ],
            [
                'name' => 'Rini Wijaya',
                'email' => 'rini@sekolah.com',
                'password' => Hash::make('user123'),
                'role' => 'user'
            ],
            [
                'name' => 'Ahmad Hidayat',
                'email' => 'ahmad@sekolah.com',
                'password' => Hash::make('user123'),
                'role' => 'user'
            ]
        ];

        foreach ($users as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                $user
            );
        }

        // Mentors
        $mentors = [
            [
                'name' => 'Dr. Bambang Setiawan',
                'email' => 'bambang@mentor.com',
                'password' => Hash::make('mentor123'),
                'role' => 'mentor'
            ],
            [
                'name' => 'Ibu Dewi Lestari',
                'email' => 'dewi@mentor.com',
                'password' => Hash::make('mentor123'),
                'role' => 'mentor'
            ],
            [
                'name' => 'Prof. Sunaryo',
                'email' => 'sunaryo@mentor.com',
                'password' => Hash::make('mentor123'),
                'role' => 'mentor'
            ]
        ];

        foreach ($mentors as $mentor) {
            User::firstOrCreate(
                ['email' => $mentor['email']],
                $mentor
            );
        }
    }
}
