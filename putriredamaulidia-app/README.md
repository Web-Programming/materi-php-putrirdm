# Aplikasi Web dengan Laravel

Proyek ini adalah aplikasi web berbasis **Laravel 13** yang digunakan sebagai bahan ajar mata kuliah **Pemrograman Aplikasi Web I (PAW I) — Kelas SI4B**. Aplikasi mencakup pengelolaan data produk dan supplier dengan fitur autentikasi, autorisasi, dan REST API.

**Tech Stack:** Laravel 13 · PHP 8.3 · MySQL / SQLite · Bootstrap 5 · Blade Template · Laravel Sanctum

---

## Daftar Materi

| No | Materi | Deskripsi |
|----|--------|-----------|
| 1 | [CRUD](CRUDProduct.pdf) | Membuat operasi Create, Read, Update, Delete menggunakan Resource Controller dan Blade views |
| 2 | [Authentication](Authentication.md) | Membuat sistem login, register, dan logout secara manual menggunakan facade `Auth` |
| 3 | [Authorization](Authorization.md) | Membatasi akses fitur menggunakan Gate dan Policy |
| 4 | [REST API](RestAPI.md) | Membangun REST API dengan Laravel Sanctum untuk autentikasi berbasis token |

---

## Tahapan Instalasi Project

1. Clone project dari repository
2. Akses project dari terminal (command prompt)
3. Jalankan perintah `composer install` untuk mendownload dependensi
4. Copy file `.env.example` dan rename menjadi `.env`
5. Jalankan perintah `php artisan key:generate`

## Menyiapkan Database

1. Buat file `database.sqlite` di dalam folder `database/`
2. Jalankan perintah `php artisan migrate`
3. *(Opsional)* Jalankan seeder untuk mengisi data awal:
   ```bash
   php artisan db:seed
   ```

## Menjalankan Server

1. Pastikan seluruh dependensi telah terinstall
2. Pastikan key sudah digenerate
3. Pastikan database sudah disiapkan
4. Jalankan perintah `php artisan serve`
5. Buka browser dan akses `http://localhost:8000`