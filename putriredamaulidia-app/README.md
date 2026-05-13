## Tahapan Instalasi Project

1. Clone project dari repository
2. Akses project dari terminal (command prompt)
3. Jalankan perintah `composer install` untuk mendownload dependensi
4. Copy file `.env.example` dan rename menjadi `.env`
5. Jalankan perintah `php artisan key:generate`

## Menyiapkan database

1. Buat file `database.sqlite` di dalam folder `database/`
2. Jalankan perintah `php artisan migrate`

## Menjalankan server

1. Pastikan seluruh dependensi telah terinstall
2. Pastikan key sudah digenerate
3. Pastikan database sudah disiapkan
4. Jalankan perintah `php artisan serve`