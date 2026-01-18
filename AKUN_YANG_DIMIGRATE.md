# Data Akun yang Sudah Di-Migrate

## Admin Accounts
| Email | Password | Role |
|-------|----------|------|
| admin@adiwiyata.com | admin123 | admin |
| admin@sekolah.com | admin123 | admin |

## User Accounts
| Nama | Email | Password | Role |
|------|-------|----------|------|
| Budi Santoso | budi@sekolah.com | user123 | user |
| Siti Nurhaliza | siti@sekolah.com | user123 | user |
| Rini Wijaya | rini@sekolah.com | user123 | user |
| Ahmad Hidayat | ahmad@sekolah.com | user123 | user |

## Mentor Accounts
| Nama | Email | Password | Role |
|------|-------|----------|------|
| Dr. Bambang Setiawan | bambang@mentor.com | mentor123 | mentor |
| Ibu Dewi Lestari | dewi@mentor.com | mentor123 | mentor |
| Prof. Sunaryo | sunaryo@mentor.com | mentor123 | mentor |

## Perubahan yang Dilakukan:

1. **Migrasi Baru**: `2026_01_15_000000_add_role_to_users_table.php`
   - Menambahkan kolom `role` ke tabel `users` dengan tipe enum (admin, user, mentor)
   - Default role adalah 'user'

2. **Seeder Baru**: `AdminUserMentorSeeder.php`
   - Membuat 2 akun admin
   - Membuat 4 akun user biasa
   - Membuat 3 akun mentor
   - Total 9 akun yang tersedia untuk testing

3. **Update User Model**:
   - Menambahkan `'role'` ke array `$fillable` agar role dapat diassign

## Cara Login:
- Akses halaman login di: `http://localhost:8000/login`
- Gunakan salah satu email dan password dari tabel di atas
- Semua akun sudah tersedia di database SQLite
