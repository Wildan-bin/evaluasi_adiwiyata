<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AdministrasiSekolah;
use Illuminate\Database\Seeder;

class AdministrasiSekolahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users with role 'user' (at least 3)
        $users = User::where('role', 'user')->take(3)->get();

        if ($users->count() < 3) {
            // Create 3 dummy users if not enough
            for ($i = 1; $i <= 3; $i++) {
                $users->push(User::create([
                    'name' => "Sekolah User $i",
                    'email' => "sekolah$i@test.com",
                    'password' => bcrypt('password'),
                    'role' => 'user',
                    'email_verified_at' => now(),
                ]));
            }
        }

        $schoolsData = [
            [
                'npsn' => '20101234',
                'nama_sekolah' => 'SMP Negeri 1 Jakarta',
                'provinsi' => 'DKI Jakarta',
                'kota' => 'Jakarta Pusat',
                'kecamatan' => 'Cempaka Putih',
                'kelurahan' => 'Cempaka Putih Timur',
                'alamat' => 'Jl. Pendidikan No. 123, Jakarta Pusat 12345',
                'kode_pos' => '12345',
                'nama_kepala_sekolah' => 'Dr. Budi Santoso, S.Pd., M.Pd.',
                'nip_kepala_sekolah' => '19650512 198703 1 001',
                'email' => 'smpn1jakarta@sch.id',
                'telepon' => '021-123456',
                'website' => 'https://smpn1jakarta.sch.id',
                'google_maps_url' => 'https://www.google.com/maps/place/SMP+Negeri+1+Jakarta',
                'latitude' => '-6.1753',
                'longitude' => '106.8249',
                'tim_greenedu' => json_encode([
                    'ketua' => [
                        'nama' => 'Ibu Siti Nurhaliza',
                        'jabatan' => 'Wakil Kepala Sekolah',
                        'wa' => '628987654321',
                        'email' => 'siti.nurhaliza@sch.id'
                    ],
                    'anggota' => [
                        ['nama' => 'Budi Hermawan', 'jabatan' => 'Guru Biologi', 'wa' => '628111111111'],
                        ['nama' => 'Sinta Dewi', 'jabatan' => 'Guru IPA', 'wa' => '628222222222'],
                        ['nama' => 'Ahmad Ridho', 'jabatan' => 'Guru Sejarah', 'wa' => '628333333333'],
                    ]
                ]),
                'status' => 'unverified',
            ],
            [
                'npsn' => '20205678',
                'nama_sekolah' => 'SMA Negeri 2 Bandung',
                'provinsi' => 'Jawa Barat',
                'kota' => 'Bandung',
                'kecamatan' => 'Bandung Kulon',
                'kelurahan' => 'Sukaraja',
                'alamat' => 'Jl. Raya Bandung Km. 5, Bandung 40123',
                'kode_pos' => '40123',
                'nama_kepala_sekolah' => 'Ir. Ahmad Wijaya, M.M.',
                'nip_kepala_sekolah' => '19600324 198703 1 002',
                'email' => 'sman2bandung@sch.id',
                'telepon' => '022-654321',
                'website' => 'https://sman2bandung.sch.id',
                'google_maps_url' => 'https://www.google.com/maps/place/SMA+Negeri+2+Bandung',
                'latitude' => '-6.9271',
                'longitude' => '107.6412',
                'tim_greenedu' => json_encode([
                    'ketua' => [
                        'nama' => 'Drs. Hermawan Susanto',
                        'jabatan' => 'Wakil Kepala Sekolah Bidang Sarana Prasarana',
                        'wa' => '628345678901',
                        'email' => 'hermawan.susanto@sch.id'
                    ],
                    'anggota' => [
                        ['nama' => 'Eka Putri', 'jabatan' => 'Guru Kimia', 'wa' => '628444444444'],
                        ['nama' => 'Roni Wijaya', 'jabatan' => 'Guru Fisika', 'wa' => '628555555555'],
                        ['nama' => 'Lina Marlina', 'jabatan' => 'Guru Biologi', 'wa' => '628666666666'],
                    ]
                ]),
                'status' => 'unverified',
            ],
            [
                'npsn' => '20303456',
                'nama_sekolah' => 'SD Negeri 3 Surabaya',
                'provinsi' => 'Jawa Timur',
                'kota' => 'Surabaya',
                'kecamatan' => 'Karang Pilang',
                'kelurahan' => 'Karang Pilang',
                'alamat' => 'Jl. Gatot Subroto No. 88, Surabaya 60123',
                'kode_pos' => '60123',
                'nama_kepala_sekolah' => 'Ibu Endang Suryani, S.Pd.',
                'nip_kepala_sekolah' => '19620415 198703 2 001',
                'email' => 'sdn3surabaya@sch.id',
                'telepon' => '031-456789',
                'website' => 'https://sdn3surabaya.sch.id',
                'google_maps_url' => 'https://www.google.com/maps/place/SD+Negeri+3+Surabaya',
                'latitude' => '-7.2504',
                'longitude' => '112.7508',
                'tim_greenedu' => json_encode([
                    'ketua' => [
                        'nama' => 'Bapak Darmawan, S.Pd.',
                        'jabatan' => 'Koordinator Program Adiwiyata',
                        'wa' => '628567890123',
                        'email' => 'darmawan@sch.id'
                    ],
                    'anggota' => [
                        ['nama' => 'Nur Hidayah', 'jabatan' => 'Guru IPA', 'wa' => '628777777777'],
                        ['nama' => 'Bambang Sutrisno', 'jabatan' => 'Guru Matematika', 'wa' => '628888888888'],
                        ['nama' => 'Ratna Sari', 'jabatan' => 'Guru Bahasa Indonesia', 'wa' => '628999999999'],
                    ]
                ]),
                'status' => 'unverified',
            ],
        ];

        foreach ($schoolsData as $index => $data) {
            if (isset($users[$index])) {
                $data['user_id'] = $users[$index]->id;
                AdministrasiSekolah::create($data);
            }
        }

        $this->command->info('AdministrasiSekolah seeder berhasil dijalankan dengan ' . count($schoolsData) . ' records.');
    }
}
