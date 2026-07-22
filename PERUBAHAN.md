# 📝 Perubahan yang Dilakukan

## 🔧 1. Fix Migration (Database)

**Masalah:** Ada dua file migration dengan timestamp yang sama (`2024_01_01_000002_`) yang menyebabkan error saat `php artisan migrate`:
- `2024_01_01_000002_create_users_table.php` ← **DIHAPUS** (duplikat, konflik dengan migration bawaan Laravel)
- `2024_01_01_000002_add_columns_to_users_table.php` ← **DIPERTAHANKAN & DIPERBAIKI**

**Perbaikan migration `add_role_columns_to_users_table`:**
- Menambah kolom `username`, `display_name`, `avatar_color`, `role` ke tabel users
- Menjadikan kolom `email` dan `name` bawaan Laravel menjadi **nullable** (karena pewarta/admin tidak harus punya email)

---

## 🔐 2. Satu Halaman Login Universal (`/login`)

**Masalah:** Ada 4 halaman login terpisah (`/login`, `/pewarta/login`, `/redaksi/login`, `/admin/login`)

**Solusi:** Satu halaman login di `/login` dengan tab selector role:
- 👤 **Pembaca** — login + registrasi akun baru
- ✍️ **Pewarta** — login untuk reporter/penulis
- 📋 **Redaksi** — login untuk pemimpin redaksi
- ⚙️ **Admin** — login untuk administrator

**Cara kerja:**
- User memilih tab role → masukkan username & password → sistem otomatis redirect ke dashboard yang sesuai
- Route `/pewarta/login`, `/redaksi/login`, `/admin/login` sekarang redirect ke `/login`

---

## 🔄 3. Unified Auth dengan Laravel Auth

**Masalah:** Pewarta & Redaksi pakai session manual (`pewarta_user_id`, `redaksi_user_id`), Admin pakai `admin_logged_in` session — tidak konsisten.

**Perbaikan di `AuthController`:**
- Login sekarang menggunakan `Auth::login()` standar Laravel untuk semua role
- Setelah login, sistem baca `user->role` lalu redirect ke dashboard yang tepat
- Session lama tetap diset sebagai backward compatibility

**Perbaikan di Middleware:**
- `AdminMiddleware`, `PewartaMiddleware`, `RedaksiMiddleware` sekarang cek `Auth::check()` + `Auth::user()->role` dahulu
- Fallback ke session lama tetap ada untuk backward compatibility

---

## 📁 Struktur File yang Berubah

```
app/Http/Controllers/AuthController.php    ← Diperbarui (login universal semua role)
app/Http/Controllers/AdminController.php   ← Diperbarui (redirect showLogin ke /login)
app/Http/Controllers/PewartaController.php ← Diperbarui (pakai Auth, redirect showLogin)
app/Http/Controllers/RedaksiController.php ← Diperbarui (pakai Auth, redirect showLogin)
app/Http/Middleware/AdminMiddleware.php     ← Diperbarui (pakai Auth::check())
app/Http/Middleware/PewartaMiddleware.php   ← Diperbarui (pakai Auth::check())
app/Http/Middleware/RedaksiMiddleware.php   ← Diperbarui (pakai Auth::check())
resources/views/auth/login.blade.php       ← Diperbarui (UI tab 4 role)
routes/web.php                             ← Diperbarui (route login/logout disederhanakan)
database/migrations/
  - 2024_01_01_000002_create_users_table.php           ← DIHAPUS (duplikat)
  - 2024_01_01_000002_add_role_columns_to_users_table  ← DIPERBARUI (fix nullable)
```

---

## 🚀 Cara Menjalankan Setelah Update

```bash
# 1. Copy file-file yang berubah ke project kamu

# 2. Jalankan migrasi ulang (jika fresh install)
php artisan migrate:fresh --seed

# 3. Atau jika sudah ada data, jalankan migrate saja
php artisan migrate

# 4. Jalankan server
php artisan serve
```

## 🔑 Akun Default (dari Seeder)

| Role    | Username | Password    | Nama           |
|---------|----------|-------------|----------------|
| Admin   | admin    | nexus2026   | Admin Zverse   |
| Pewarta | rizky    | pewarta123  | Rizky Pratama  |
| Pewarta | raka     | pewarta123  | Raka Aditya    |
| Pewarta | indri    | pewarta123  | Indri Sari     |
| Redaksi | tegar    | redaksi123  | Tegar Kusuma   |
| Redaksi | udin     | redaksi123  | Udin Saputra   |
| Redaksi | jaya     | redaksi123  | Jaya Pratama   |

Semua akun login dari satu halaman: `/login`
